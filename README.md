# NASACRE Invoice System

Laravel application for managing SACRE contacts, invoices, campaigns, files, and conference bookings.

## Requirements

- PHP 7.4 or 8.0 with the extensions required by Laravel 8
- Composer 2
- MySQL or MariaDB
- Node.js 14 or 16 and npm
- `wkhtmltopdf` for PDF generation

PHP 8.5 and current Node.js releases can boot the legacy application but generate compatibility warnings. Use the versions above until the framework and frontend toolchain are upgraded.

## Fresh Installation

1. Copy the environment template:

   ```bash
   cp .env.example .env
   ```

2. Configure the database, mail, queue, and application URL values in `.env`.

3. Install dependencies:

   ```bash
   composer install
   npm install
   ```

4. Generate the application key and database schema:

   ```bash
   php artisan key:generate
   php artisan migrate
   ```

5. Optionally create an initial administrator by setting `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD`, then running:

   ```bash
   php artisan db:seed
   ```

   Remove those seed credentials from `.env` immediately afterward.

6. Build frontend assets:

   ```bash
   npm run production
   ```

   On newer Node.js versions, the legacy Webpack build may require:

   ```bash
   NODE_OPTIONS=--openssl-legacy-provider npm run production
   ```

7. Start a queue worker for invoice and campaign email jobs:

   ```bash
   php artisan queue:work
   ```

8. Point the web server document root at `public/`. Ensure `storage/` and `bootstrap/cache/` are writable by PHP.

## Existing Installation

Back up the database and `storage/app/public/files` before deploying. Then run:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan queue:restart
```

The 2026 reconciliation migration creates schema objects that exist in the supplied production dump but were missing from source control. The file migration moves uploaded documents from publicly accessible storage into `storage/app/files`.

## Reference Dump

`database/database.nasacre.org.uk_2026-10-30.sql` was used only as a schema reference. Do not commit or deploy database dumps containing production data.
