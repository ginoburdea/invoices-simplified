<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Invoice::class);
        return Inertia::render('Invoice/List', []);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Invoice/Create', []);
    }

    protected function calculateInvoiceTotal(int $invoice_id)
    {
        $products = Product::where('invoice_id', $invoice_id)->get();
        // error_log(json_encode($products));

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
        // error_log(json_encode($invoice_data));
        $invoice = $request->user()->invoices()->create($invoice_data);
        // error_log(json_encode($invoice));

        $invoice->products()->createMany($data['products']);

        $invoice['total'] = $this->calculateInvoiceTotal($invoice['id']);
        // error_log($invoice['total']);
        $invoice->save();

        return redirect()->route('invoices.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
