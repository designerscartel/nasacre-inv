<?php

namespace App\Http\Controllers;

use App\Contracts\SacreInvoice\UpdatesSacreInvoiceInformation;
use App\Contracts\Invoice\CreatesInvoicePdf;
use App\Models\Sacre;
use App\Models\SacreInvoice;
use Illuminate\Http\Request;
use Inertia\Inertia;


/**
 * Class SacreInvoiceControlller
 * @package App\Http\Controllers
 */
class SacreInvoiceControlller extends Controller
{


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Sacre $sacre
     * @return \Illuminate\Http\Response
     */
    public function show(Sacre $sacre)
    {
        //
        return Inertia::render('Sacres/Invoices', [
            'sacre' => $sacre->toResource(),
        ]);
    }


    /**
     * Update the given Scare Invoices details.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sacre $sacre
     * @param \App\Models\SacreInvoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sacre $sacre, SacreInvoice $sacreInvoice)
    {
        abort_unless($sacreInvoice->sacre_id === $sacre->id, 404);
        app(UpdatesSacreInvoiceInformation::class)->update($sacreInvoice, $request->all());
        return back(303);
    }


    /**
     * @param Request $request
     * @param Sacre $sacre
     * @param SacreInvoice $invoice
     */
    public function pdf(Request $request, Sacre $sacre, SacreInvoice $sacreInvoice)
    {
        abort_unless($sacreInvoice->sacre_id === $sacre->id, 404);

        $pdfSacre = $sacre->replicate();
        $pdfInvoice = $sacreInvoice->invoice->replicate();

        if(!empty($sacreInvoice->address)) {
            $pdfSacre->address = $sacreInvoice->address;
        }

        if(!empty($sacreInvoice->po_number)) {
            $pdfInvoice->po_number = $sacreInvoice->po_number;
        }

        if(!empty($sacreInvoice->date)) {
            $pdfInvoice->date = $sacreInvoice->date;
        }

        $pdfInvoice->virtual_training = $sacreInvoice->virtual_training;

        $pdf = app(CreatesInvoicePdf::class)->inline($pdfInvoice, $pdfSacre);
        return $pdf;
    }


}
