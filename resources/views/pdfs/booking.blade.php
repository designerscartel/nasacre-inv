@php
    $total = 0;
    $delegateCost = ($bookingData->sacre->member)? $bookingData->booking->subscribed : $bookingData->booking->none_subscribed;
@endphp

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>AGM Booking Invoice</title>

    <style type="text/css">

        @page {
            margin: 1cm;
        }

        body {
            background-repeat: no-repeat;
            background-size: 100%;
            background-position: top right;
            font-family: Helvetica, Arial, "Lucida Grande", sans-serif;
            margin: 1cm 0;
            width: 100%;
            height: 100%;
        }

        table, tr, td {
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
            text-align: left;;
        }

        table {
            width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
        }

        th {
            font-weight: normal;
        }

        td {
            vertical-align: top;
        }

        tr {
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 0;
        }


        h1 {
            margin-bottom: 60px;
        }

        p, ul, ol {
            font-size: .85em;
            page-break-inside: avoid;
        }

        p {
            margin: 0;
        }

        .booking td, .booking th {
            border: 1px solid #DDD;
            padding: 10px;
        }

        .header {
            width: 100%;
            margin-bottom: 40px;
        }

        .header td {
            text-align: left;
            vertical-align: bottom;
        }

        .logo-holder {
            width: 5cm;
        }

        .logo {
            width: 100%;
        }

        .details {
            width: 50%;
            margin-bottom: 40px;
        }

        .attendance {
            margin-bottom: 20px;
            width: 55%;
        }

        .fao {
            margin-bottom: 20px;
            width: 70%;
        }

        .how {
            margin-top: 40px;
        }

        .how p {
            margin-bottom: 10px;
        }

        .bank td, .bank th {
            padding: 0;
        }

        .bank p {
            margin: 0;
        }

        .underline {
            text-decoration: underline;
        }

        .well {
            border: 1px solid #DDD;
            padding: 10px;
            height: 180px;
        }

        .how-column {
            width: 45%;
        }

        .how-small {
            width: 10%;
        }


    </style>
</head>

<body>

<table class="header">
    <tr>
        <td><h1>NASACRE AGM INVOICE</h1></td>
        <td class="logo-holder"><img class="logo" src="{{ public_path().'/nasacre-logo.png' }}" /></td>
    </tr>
</table>

<table class="details">
    <tr>
        <td><p>Invoice Number:</p></td>
        <td><p><strong>{{ $date->format('y') }}/AGM/{{ $bookingData->sacre->code }}/{{ $bookingData->sacre->short_code }}/{{ $bookingData->id }}</strong></p></td>
    </tr>
    <tr>
        <td><p>Invoice Date:</p></td>
        <td><p><strong>{{ $date->format('d/m/Y') }}</strong></p></td>
    </tr>
    @if(!empty($bookingData->po_number))
        <tr>
            <td><p>PO Number</p></td>
            <td><p><strong>{{ $bookingData->po_number }}</strong></p></td>
        </tr>
    @endif
</table>


<div class="attendance">
    <p>For:<br/>
        <strong>Attendance at the NASACRE Conference and AGM {{ $bookingDataDate->format('Y') }}</strong><br />
        {{ $bookingData->booking->venue }}
        <br/>SACRE: {{ $bookingData->sacre->title }}</p>
</div>

<div class="fao">
    <p>Invoice Payment</p>
    @if(!empty($bookingData->address))
        <p>{{ $bookingData->address }}</p>
    @else
        @if(!empty($bookingData->sacre->address))
            <p>{{ $bookingData->sacre->address }}</p>
        @endif
    @endif
</div>

<table class="booking">
    <tr>
        <th><h5>Attendee(s)</h5></th>
        <th><h5>Representing</h5></th>
        <th><h5>&pound;</h5></th>
    </tr>

    @if(!empty($bookingData->delegate_one_name))
        @php
            if(!$bookingData->delegate_one_free) {
                $total = $total + $delegateCost;
            } else {
                $total = $total + 0;
            }
        @endphp
        <tr>
            <td><p>{{ $bookingData->delegate_one_name }}</p></td>
            <td><p>{{ $bookingData->sacre->title }}</p></td>
            @if(!$bookingData->delegate_one_free)
                <td><p>&pound;{{ $delegateCost }}</p></td>
            @else
                <td><p>Free</p></td>
            @endif
        </tr>
    @endif

    @if(!empty($bookingData->delegate_two_name))
        @php
            if(!$bookingData->delegate_two_free) {
                $total = $total + $delegateCost;
            } else {
                $total = $total + 0;
            }
        @endphp
        <tr>
            <td><p>{{ $bookingData->delegate_two_name }}</p></td>
            <td><p>{{ $bookingData->sacre->title }}</p></td>
            @if(!$bookingData->delegate_two_free)
                <td><p>&pound;{{ $delegateCost }}</p></td>
            @else
                <td><p>Free</p></td>
            @endif
        </tr>
    @endif

    @if($bookingData->sacre->member)
        @if(!empty($bookingData->virtual_one_name))
            @php
               if(!$bookingData->virtual_one_free) {
                   $total = $total + $bookingData->booking->additional;
               } else {
                   $total = $total + 0;
               }
            @endphp
            <tr>
                <td><p>{{ $bookingData->virtual_one_name }}</p></td>
                <td><p>{{ $bookingData->sacre->title }}</p></td>
                @if(!$bookingData->virtual_one_free)
                    <td><p>&pound;{{  $bookingData->booking->additional }}</p></td>
                @else
                    <td><p>Free</p></td>
                @endif

            </tr>
        @endif

        @if(!empty($bookingData->virtual_two_name))
        <tr>
            <td><p>{{ $bookingData->virtual_two_name }}</p></td>
            <td><p>{{ $bookingData->sacre->title }}</p></td>
            <td><p></p></td>
        </tr>
        @endif

    @else

        @if(!empty($bookingData->virtual_one_name))
            @php
                if(!$bookingData->virtual_one_free) {
                    $total = $total + 30;
                } else {
                    $total = $total + 0;
                }
            @endphp
            <tr>
                <td><p>{{ $bookingData->virtual_one_name }}</p></td>
                <td><p>{{ $bookingData->sacre->title }}</p></td>
                @if(!$bookingData->virtual_one_free)
                    <td><p>&pound;30</p></td>
                @else
                    <td><p>Free</p></td>
                @endif

            </tr>
        @endif

        @if(!empty($bookingData->virtual_two_name))

                @php
                    if(!$bookingData->virtual_two_free) {
                        $total = $total + 30;
                    } else {
                        $total = $total + 0;
                    }
                @endphp

            <tr>
                <td><p>{{ $bookingData->virtual_two_name }}</p></td>
                <td><p>{{ $bookingData->sacre->title }}</p></td>
                @if(!$bookingData->virtual_two_free)
                    <td><p>&pound;30</p></td>
                @else
                    <td><p>Free</p></td>
                @endif
            </tr>
        @endif


    @endif

    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2"><p>TOTAL</p></td>
        <td colspan="1"><p><strong>&pound;{{ $total }}</strong></p></td>
    </tr>

</table>
<p><strong>Payment is now due</strong></p>

<div class="how">
    <h5>How to pay</h5>
    <table>
        <tr>
            <td class="how-column">
                <div class="well">
                    <h6>BACS</h6>
                    <p>If paying by BACS, <span class="underline">please send notification</span> to<br/>
                        email address: treasurer@nasacre.org.uk</p>

                    <table class="bank">
                        <tr>
                            <td><p>Bank:</p></td>
                            <td><p><strong>Cooperative Bank</strong></p></td>
                        </tr>
                        <tr>
                            <td><p>Account Name:</p></td>
                            <td><p><strong>NASACRE</strong></p></td>
                        </tr>
                        <tr>
                            <td><p>Account Number:</p></td>
                            <td><p><strong>67410361</strong></p></td>
                        </tr>
                        <tr>
                            <td><p>Sort Code:</p></td>
                            <td><p><strong>08-92-99</strong></p></td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="how-small">
            </td>

            <td class="how-column">
                <div class="well">
                    <h6>CHEQUE</h6>
                    <p>
                        To be made payable to:<br/>
                        <strong>NASACRE/ The National Association of SACREs</strong>
                    </p>
                    <p>
                        Please send to:<br/>
                        <strong>Rev. Preb. Michael Metcalf<br/>
                            NASACRE Treasurer<br/>
                            196 Stone Road,<br/>
                            Stafford<br/>
                            ST16 1NT</strong>
                    </p>
                </div>
            </td>
        </tr>
    </table>
</div>