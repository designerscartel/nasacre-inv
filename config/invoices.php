<?php

return [
    'pdf_template' => env('INVOICE_PDF_TEMPLATE', 'pdfs.2026-invoice'),
    'training_price' => (float) env('INVOICE_TRAINING_PRICE', 60),
    'charity_number' => env('INVOICE_CHARITY_NUMBER', '1212494'),
    'payment_email' => env('INVOICE_PAYMENT_EMAIL', 'admin@nasacre.org.uk'),
    'bank' => [
        'name' => env('INVOICE_BANK_NAME', 'Cooperative Bank'),
        'account_name' => env('INVOICE_BANK_ACCOUNT_NAME', 'NASACRE'),
        'account_number' => env('INVOICE_BANK_ACCOUNT_NUMBER', '67410361'),
        'sort_code' => env('INVOICE_BANK_SORT_CODE', '08-92-99'),
    ],
];
