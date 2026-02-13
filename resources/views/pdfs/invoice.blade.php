<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Invoice</title>

    <style type="text/css">

        @page {
            margin: 1cm 1cm 1cm 1cm;
        }

        body {
            font-family: Helvetica, Arial, "Lucida Grande", sans-serif;
            position: relative;
            height: 29.7cm;
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

        .small {
            font-size: .65em;
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

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 1cm;
            margin-bottom: -4cm;
        }

        .footer td {
            text-align: center;
            vertical-align: bottom;
        }

        .logo-holder {
            width: 5cm;
        }

        .logo {
            width: 100%;
            margin-bottom: 10px;
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
        <td><h1>INVOICE</h1></td>
        <td class="logo-holder">
            <img class="logo" src="{{ public_path().'/nasacre-logo.png' }}"/>
            <p class="small">Registered Charity Number: 1212494</p>
        </td>
    </tr>
</table>

<table class="details">
    <tr>
        <td><p>Invoice Date:</p></td>
        <td><p><strong>{{ $date->format('d/m/Y') }}</strong></p></td>
    </tr>
    <tr>
        <td><p>Invoice Number:</p></td>
        <td><p><strong>{{ $date->format('y') }}/SUBS/{{ $sacre->code }}@if(!empty($sacre->short_code))
                        /{{ $sacre->short_code }}
                    @endif/{{ $invoice->id }}</strong></p></td>
    </tr>
    @if(!empty($invoice->po_number))
        <tr>
            <td><p>PO Number</p></td>
            <td><p><strong>{{ $invoice->po_number }}</strong></p></td>
        </tr>
    @endif
</table>


<div class="attendance">
    <p>For:<br/>
        <strong>NASACRE Annual Subscription: Academic Year {{ $date->format('Y') }}
            -{{ $date->format('y') + 1 }}</strong>
        <br/>SACRE: {{ $sacre->title }}</p>
</div>

<div class="fao">
    @if(!empty($sacre->address))
        <p>Invoice Payment</p>
        <p>{{ $sacre->address }}</p>
    @endif
</div>

<table class="booking">
    <tr>
        <th><h5>SACRE</h5></th>
        <th><h5>Year</h5></th>
        <th><h5>&pound;</h5></th>
    </tr>
    <tr>
        <td><p>{{ $sacre->title}}</p></td>
        <td><p>{{ $date->format('Y') }}-{{ $date->format('y') + 1 }}</p></td>
        <td><p>&pound;{{ $invoice->subs }}</p></td>
    </tr>
    @if($sacre->virtual_training == 1)
        <tr>
            <td><p>Virtual Training</p></td>
            <td><p>{{ $date->format('Y') }}-{{ $date->format('y') + 1 }}</p></td>
            <td><p>&pound;60.00</p></td>
        </tr>
        @php
            $invoice->subs = $invoice->subs + 60.00;
        @endphp
    @endif
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2"><p>TOTAL</p></td>
        <td colspan="1"><p><strong>&pound;{{ $invoice->subs }}</strong></p></td>
    </tr>

</table>
<p><strong>Payment is now due</strong></p>

<div class="how">
    <table>
        <tr>
            <td class="how-column">
                <div class="well">
                    <h6>How to pay: BACS</h6>
                    <p><span class="underline">Please send notification</span> to<br/>
                        email address: admin@nasacre.org.uk</p>

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
                {{--
                <div class="well">
                    <h6>CHEQUE</h6>
                    <p>
                        To be made payable to:<br/>
                        <strong>NASACRE/ The National Association of SACREs</strong>
                    </p>
                    <p>
                        Please send to:<br/>
                        <strong>
                            NASACRE Treasurer<br/>
                            196 Stone Road,<br/>
                            Stafford<br/>
                            ST16 1NT</strong>
                    </p>
                </div>
                --}}
            </td>
        </tr>
    </table>
</div>

<table class="footer">
    <tr>
        <td class="">
            <p class="small">NASACRE c/o Religious Education Council of England and Wales,<br/>Northgate House, North
                Gate, New Basford, Nottingham, NG7 7BQ</p>
        </td>
    </tr>
</table>


</body>
</html>
