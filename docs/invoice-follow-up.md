# Invoice Follow-up

The invoice pricing work completed on 30 October 2026 includes:

- A separate £60 virtual training charge.
- A per-SACRE final total override.
- Effective totals displayed in the SACRE invoice list.
- Safer recipient selection, batching, retries, validation, and invoice ownership checks.
- A send preview with recipient and skipped-SACRE details.
- Explicit resend confirmation and previous batch summaries.
- Queue delivery progress and failure reporting.
- CSV reporting for amount, batch, sent date, and delivery status.
- Historical invoice support for soft-deleted SACREs.

## Remaining work

### High priority

- Add a safe way to retry only failed recipients without generating another complete batch.

### Invoice records

- Confirm whether invoice numbers should include the batch number. At present, resending the same annual invoice to a SACRE produces the same visible invoice number.
- Decide whether edits to PO number, address, date, training, or total should be recorded in an audit history.
- Decide whether invoice records should support cancelled, void, paid, and outstanding statuses.

### PDF and email

- Replace the hard-coded `2026-invoice` PDF template selection with a reusable or configurable template.
- Move bank details, charity number, payment email, training price, and other annual values into configuration rather than embedding them in the PDF.
- Add an invoice preview or test-email action before sending a full batch.
- Confirm whether edited invoices should be emailed again from the SACRE invoice screen.

### Testing and maintenance

- Add queue and mail tests covering successful delivery, failure, retry, and duplicate prevention.
- Add feature tests for invoice editing, SACRE ownership checks, CSV downloads, and PDF overrides.
- Revisit the invoice workflow after upgrading Laravel because the current mail and queue dependencies are old and produce PHP 8.5 deprecation warnings.

## Suggested next step

Add a failed-recipient retry flow, including confirmation, queue tests, and duplicate prevention.
