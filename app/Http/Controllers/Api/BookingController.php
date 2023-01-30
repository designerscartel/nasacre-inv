<?php

namespace App\Http\Controllers\Api;

use App\Actions\Booking\CreateBookingRefInformation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $booking = app(CreateBookingRefInformation::class)->create($request);
    }


}
