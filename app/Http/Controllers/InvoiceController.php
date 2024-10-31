<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
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

        $keys = ['query', 'page', 'sortField', 'sortType'];

        // If the data contains defaults, then add the default and redirect
        $data_with_defaults = [];
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
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

        return Inertia::render('Invoice/Create/Index', [
            'last_vendor_info' => isset($last_invoice) ? $last_invoice['vendor'] : null,
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
    public function download(string $id)
    {
        return response()->json('file...');
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
