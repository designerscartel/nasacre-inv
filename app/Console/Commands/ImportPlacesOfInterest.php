<?php

namespace App\Console\Commands;

use App\Models\PlaceOfInterest;
use App\Models\Region;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ImportPlacesOfInterest extends Command
{
    protected $signature = 'places:import {path=storage/RE-Hubs.csv}';

    protected $description = 'Import places of interest and their areas from the RE Hubs CSV';

    public function handle(): int
    {
        $path = base_path($this->argument('path'));
        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->error("Unable to open {$path}");
            return self::FAILURE;
        }

        try {
            $headers = fgetcsv($handle, null, ',', '"', '');
            if ($headers === false) {
                throw new RuntimeException('The CSV is empty.');
            }

            $indexes = array_flip($headers);
            $required = [
                'Religion & Belief system',
                'Name',
                'POI or SS',
                'Hub',
                'Place of Interest Address, if applicable',
            ];

            foreach ($required as $header) {
                if (! array_key_exists($header, $indexes)) {
                    throw new RuntimeException("Missing required CSV column: {$header}");
                }
            }

            $imported = 0;
            while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
                if (! str_contains(strtoupper($this->value($row, $indexes, 'POI or SS')), 'POI')) {
                    continue;
                }

                DB::transaction(function () use ($row, $indexes, &$imported) {
                    $name = $this->value($row, $indexes, 'Place of Interest Address, if applicable')
                        ?: $this->value($row, $indexes, 'Name');
                    $religion = $this->value($row, $indexes, 'Religion & Belief system');
                    $organisationEmail = $this->value($row, $indexes, 'Organisation Email');
                    $personalEmail = $this->value($row, $indexes, 'Personal Email');
                    $sourceKey = hash('sha256', strtolower(implode('|', [
                        $name,
                        $religion,
                        $organisationEmail,
                        $personalEmail,
                    ])));

                    $place = PlaceOfInterest::withTrashed()->updateOrCreate(
                        ['source_key' => $sourceKey],
                        [
                            'name' => $name,
                            'religion' => $religion ?: null,
                            'contact_name' => $this->value($row, $indexes, 'Name') ?: null,
                            'organisation_email' => $organisationEmail ?: null,
                            'personal_email' => $personalEmail ?: null,
                            'phone' => $this->value($row, $indexes, 'Phone number') ?: null,
                            'website' => $this->value($row, $indexes, 'Place of Interest website if applicable') ?: null,
                            'last_training' => $this->value($row, $indexes, 'Date of most recent training') ?: null,
                            'charge_to_speak' => $this->value($row, $indexes, 'Charge to speak') ?: null,
                            'notes' => $this->value($row, $indexes, 'Notes') ?: null,
                            'do_not_list' => $this->value($row, $indexes, 'DO NOT LIST') !== '',
                            'deleted_at' => null,
                        ]
                    );

                    if ($place->trashed()) {
                        $place->restore();
                    }

                    $regionIds = collect(explode(',', $this->value($row, $indexes, 'Hub')))
                        ->map(fn ($region) => trim($region))
                        ->filter()
                        ->unique()
                        ->map(fn ($region) => Region::firstOrCreate(['title' => $region])->id);

                    $place->regions()->sync($regionIds);
                    $imported++;
                });
            }

            $this->info("Imported {$imported} places of interest.");
            return self::SUCCESS;
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        } finally {
            fclose($handle);
        }
    }

    private function value(array $row, array $indexes, string $header): string
    {
        return trim((string) ($row[$indexes[$header] ?? -1] ?? ''));
    }
}
