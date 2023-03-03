<?php

namespace App\Http\Controllers;

use App\Actions\Booking\CreateBookingInformation;
use App\Contracts\Booking\AmendsBookingInformation;
use App\Contracts\Booking\CreatesBookingPdf;
use App\Contracts\Booking\DeletesBooking;
use App\Contracts\Booking\DeletesBookingInformation;
use App\Contracts\Booking\UpdatesBookingInformation;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\SacreBooking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Csv\Writer;


class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Booking $booking)
    {
        //
        return Inertia::render('Bookings/Dashboard', [
            'bookings' => BookingResource::collection($booking::all())
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
        return Inertia::render('Bookings/Create', [
            'booking' => null,
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
        $booking = app(CreateBookingInformation::class)->create($request->all());

        return redirect()->route('bookings.show', ['booking' => $booking->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        //
        $booking = $booking->with('bookings')->get()->first();
        $booking = $booking->toResource();
        return Inertia::render('Bookings/Show', [
            'booking' => $booking,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
        return Inertia::render('Bookings/Edit', [
            'booking' => $booking->toResource(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        app(UpdatesBookingInformation::class)->update($booking, $request->all());

        return back(303);
    }


    public function amend(Request $request, Booking $booking, SacreBooking $sacreBooking)
    {
        app(AmendsBookingInformation::class)->update($sacreBooking, $request->all());
        return back(303);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Booking $booking)
    {
        //
        app(DeletesBooking::class)->delete($booking);
        return back(303);
    }


    public function destroyBookingInformation(Request $request, Booking $booking, SacreBooking $sacreBooking)
    {
        //
        app(DeletesBookingInformation::class)->delete($sacreBooking);
        return back(303);
    }

    public function pdf(SacreBooking $sacreBooking)
    {
        $pdf = app(CreatesBookingPdf::class)->inline($sacreBooking);
        return $pdf;
    }

    public function csv(Booking $booking)
    {

        $header = [
            'PO number',
            'Name',
            'Email',
            'Phone',
            'Date',
            'Sacre',
            'Delegate One Name',
            'Delegate One Email',
            'Delegate One Diet',
            'Delegate Two Name',
            'Delegate Two Email',
            'Delegate Two Diet',
            'Virtual One Name',
            'Virtual One Email',
            'Virtual Two_Name',
            'Virtual Two Email'
        ];


        $records = [];

        $booking = $booking->with([
            'bookings' => function ($query) {
                $query->where('confirmed', 1);
            }]
        )->get()->first();

        foreach ($booking->bookings as $sacreBooking) {
            $records[] = [
                $sacreBooking->po_number,
                $sacreBooking->name,
                $sacreBooking->email,
                $sacreBooking->phone,
                $sacreBooking->date,
                $sacreBooking->sacre->title,
                $sacreBooking->delegate_one_name,
                $sacreBooking->delegate_one_email,
                $sacreBooking->delegate_one_diet,
                $sacreBooking->delegate_two_name,
                $sacreBooking->delegate_two_email,
                $sacreBooking->delegate_two_diet,
                $sacreBooking->virtual_one_name,
                $sacreBooking->virtual_one_email,
                $sacreBooking->virtual_two_name,
                $sacreBooking->virtual_two_email
            ];
        }

        //load the CSV document from a string
        $csv = Writer::createFromString();
        //insert the header
        $csv->insertOne($header);
        //insert all the records
        $csv->insertAll($records);

        echo $csv->output('booking.csv');

    }


}
