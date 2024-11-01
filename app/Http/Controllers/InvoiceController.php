<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteTempInvoice;
use App\Models\Invoice;
use App\Models\Product;
use Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Invoice::class);

        $data = $request->validate([
            'query' => ['sometimes', 'string'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'sortField' => ['sometimes', Rule::in(['number', 'total'])],
            'sortType' => ['sometimes', Rule::in(['asc', 'desc'])],
        ]);

        $default_data = [
            'page' => 1,
            'sortField' => 'number',
            'sortType' => 'desc',
        ];

        $keys_exceptions = ['query'];
        $keys = ['query', 'page', 'sortField', 'sortType'];

        // If the data contains defaults, then add the default and redirect
        $data_with_defaults = [];
        foreach ($keys as $key) {
            if (!isset($data[$key]) && in_array($key, $keys_exceptions)) {
                continue;
            }

            $data_with_defaults[$key] = $data[$key] ?? $default_data[$key];
        }
        if ($data_with_defaults !== $data) {
            return redirect()->to($request->fullUrlWithQuery($data_with_defaults));
        }

        $filters = [
            'expression' => '1',
            'placeholders' => [],
        ];
        if (isset($data['query'])) {
            $filters['expression'] = 'customer LIKE ?';
            array_push($filters['placeholders'], '%' . $data['query'] . '%');

            $filter_is_a_numeric_string = preg_match("/^\d+$/", $data['query']);
            if ($filter_is_a_numeric_string) {
                $filters['expression'] .= ' OR number = ?';
                array_push($filters['placeholders'], intval($data['query']));
            }
        }

        $page_size = 25;
        $invoices = $request
            ->user()
            ->invoices()
            ->select(['id', 'number', 'customer', 'total'])
            ->whereRaw($filters['expression'], $filters['placeholders'])
            ->orderBy($data['sortField'], $data['sortType'])
            ->offset(($data['page'] - 1) * $page_size)
            ->limit($page_size)
            ->get();

        $formatted_invoices = array_map(
            fn($invoice) => [
                 ...$invoice,
                'customer' => str_replace(["\r\n", "\n"], ", ", $invoice['customer']),
            ],
            $invoices->toArray()
        );

        $prev_invoice = null;
        if ($data['page'] > 1) {
            $prev_invoice = $request
                ->user()
                ->invoices()
                ->select(['id'])
                ->whereRaw($filters['expression'], $filters['placeholders'])
                ->orderBy($data['sortField'], $data['sortType'])
                ->offset(($data['page'] - 1) * $page_size - 1)
                ->first();
        }

        $next_invoice = $request
            ->user()
            ->invoices()
            ->select(['id'])
            ->whereRaw($filters['expression'], $filters['placeholders'])
            ->orderBy($data['sortField'], $data['sortType'])
            ->offset($data['page'] * $page_size)
            ->first();

        $prev_page_url = null;
        if (isset($prev_invoice)) {
            $prev_page_url = $request->fullUrlWithQuery([ ...$data, 'page' => $data['page'] - 1]);
        }

        $next_page_url = null;
        if (isset($next_invoice)) {
            $next_page_url = $request->fullUrlWithQuery([ ...$data, 'page' => $data['page'] + 1]);
        }

        return Inertia::render('Invoice/List/Index', [
            'invoices' => $formatted_invoices,
            'prev_page_url' => $prev_page_url,
            'next_page_url' => $next_page_url,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Invoice::class);

        $last_invoice = $request->user()->invoices()->latest()->first();

        return Inertia::render('Invoice/CreateEdit/Index', [
            'last_vendor_info' => isset($last_invoice) ? $last_invoice['vendor'] : null,
            'action' => 'create',
        ]);
    }

    protected function calculateInvoiceTotal(int $invoice_id)
    {
        $products = Product::where('invoice_id', $invoice_id)->get();

        $total = 0;
        foreach ($products as $product) {
            $total += $product['price'] * 100 * $product['quantity'] / 100;
        }

        return $total;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Invoice::class);

        $data = $request->validate([
            'vendor' => ['required', 'string', 'min:4', 'max:500'],
            'customer' => ['required', 'string', 'min:4', 'max:500'],

            'products' => ['required', 'array', 'min:1', 'max:25'],
            'products.*.name' => ['required', 'string', 'min:4', 'max:100'],
            'products.*.price' => ['required', 'decimal:0,2', 'min:0.01', 'max:1000'],
            'products.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $invoice_data = [
            'vendor' => $data['vendor'],
            'customer' => $data['customer'],
            'total' => 0,
        ];
        $invoice = $request->user()->invoices()->create($invoice_data);

        $invoice->products()->createMany($data['products']);

        $invoice['total'] = $this->calculateInvoiceTotal($invoice['id']);
        $invoice->save();

        return redirect()->route('invoices.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Inertia::render('Invoice/ViewById', []);
    }

    /**
     * Download the invoice
     */
    public function download(Invoice $invoice)
    {
        Gate::authorize('download', $invoice);

        // Create the PDF

        // Requirements:
        // 1. title: INVOICE
        // 2. subtitle: number and date
        // 3. 2 boxes containing the vendor and customer info
        // 4. a table containing the products
        // 5. the invoice total under the table on the right

        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 24);

        // 1. Title
        $pdf->Cell(0, 0, 'INVOICE', 0, 0, 'C');
        $pdf->Ln(10);

        // 2. Subtitle
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 0, 'No. ' . $invoice['number'] . ' / ' . date('d.m.Y'), 0, 0, 'C');
        $pdf->Ln(20);

        // 2. Billing info
        $billing_box_width = 90;
        $x_before_vendor_box_title = $pdf->GetX();
        $y_before_vendor_box_title = $pdf->GetY();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($billing_box_width, 0, "VENDOR", 0, 2);
        $pdf->Ln(5);

        $x_before_vendor_box_content = $pdf->GetX();
        $y_before_vendor_box_content = $pdf->GetY();

        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell($billing_box_width, 7, $invoice['vendor'], 0);

        // Place the cursor next to the other box title
        // Add 10 for some space between the boxes
        // Do the same for the box content

        $pdf->SetXY($x_before_vendor_box_title + $billing_box_width + 10, $y_before_vendor_box_title);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($billing_box_width, 0, "CUSTOMER", 0, 2);
        $pdf->Ln(5);

        $pdf->SetXY($x_before_vendor_box_content + $billing_box_width + 10, $y_before_vendor_box_content);

        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell($billing_box_width, 7, $invoice['customer'], 0);

        $pdf->Ln(15);

        // 4 & 5. Products table and total

        // 210 - total page width
        // 10 - padding on each side
        // 190 - actual page width

        $products = $invoice->products()->get()->toArray();
        $formatted_products = array_map(
            fn($product) => [
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                // the price comes as a string from the database. it must be converted first
                'price' => floatval($product['price']),
                // Preventing the floating point error:
                // multiply each number by 100 to make sure they are integers
                // divide the result by 100 at power of (number count)
                'total' => ($product['quantity'] * 100) * (floatval($product['price']) * 100) / (100 * 100),
            ],
            $products
        );

        $total = 0;
        foreach ($formatted_products as $product) {
            $total = ($total * 100 + $product['total'] * 100) / 100;
        }

        $table_rows = [
            [
                'name' => 'Name',
                'quantity' => 'Quantity',
                'price' => 'Price',
                'total' => 'Line total',
            ],
            ...$formatted_products,
            [
                'name' => '',
                'quantity' => '',
                'price' => 'TOTAL',
                'total' => $total,
            ],
        ];

        $row_height = 10;
        $cell_widths = [
            'name' => 120,
            'quantity' => 20,
            'price' => 25,
            'total' => 25,
        ];

        foreach ($table_rows as $row_index => $row) {
            $is_first_row = $row_index == 0;
            if ($is_first_row) {
                $pdf->SetTextColor(255, 255, 255);
            } else {
                $pdf->SetTextColor(0, 0, 0);
            }

            $is_last_row = $row_index == count($table_rows) - 1;
            if ($is_last_row) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 14);
            }

            foreach ($row as $key => $value) {
                $formatted_value = is_numeric($value) ? number_format($value, 2) : $value;

                $pdf->Cell(
                    $cell_widths[$key],
                    $row_height,
                    $formatted_value,
                    // Only apply borders if it's not the last row (which contains the total)
                    $is_last_row ? 0 : 1,
                    0,
                    'L',
                    // Fill the cell's background with black if it's the header/first row
                    $is_first_row
                );
            }
            $pdf->Ln($row_height);
        };

        // Make sure the output directory exists
        if (!file_exists(env('TEMP_INVOICE_FOLDER'))) {
            mkdir(env('TEMP_INVOICE_FOLDER'));
        }

        // Save the PDF
        $file_name_on_disk = floor(microtime(true) * 1000) . '.pdf';
        $pdf_path = env('TEMP_INVOICE_FOLDER') . '/' . $file_name_on_disk;
        error_log(json_encode($pdf_path));
        $pdf->Output('F', $pdf_path);

        DeleteTempInvoice::dispatch($file_name_on_disk)->delay(now()->addMinutes(1));

        // Return the PDF
        $pdf_name = 'Invoice ' . $invoice['number'] . '.pdf';
        return response()->download($pdf_path, $pdf_name);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
