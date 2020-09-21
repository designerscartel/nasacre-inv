<?php

namespace App\Http\Controllers;

use App\Actions\Invoice\CreateInvoiceInformation;
use App\Contracts\Invoice\DeletesInvoice;
use App\Contracts\Invoice\UpdatesInvoiceInformation;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Invoice $invoice)
    {
        //
        return Inertia::render('Invoices/Dashboard', [
            'invoices' => InvoiceResource::collection($invoice::all())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return Inertia::render('Invoices/Create', [
            'invoice' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $invoice = app(CreateInvoiceInformation::class)->create($request->all());

        return redirect()->route('invoices.show', ['invoice' => $invoice->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice->toResource(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        app(UpdatesInvoiceInformation::class)->update($invoice, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Invoice $invoice)
    {
        //
        app(DeletesInvoice::class)->delete($invoice);

        return back(303);
    }

}
