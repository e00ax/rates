<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Rate;

class CsvParseService
{
    /**
     * Memory limit
     *
     * @var string
     */
    protected string $memoryLimit = '2G';

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        // Set memory limit to 2GB
        ini_set('memory_limit', $this->memoryLimit);
    }

    /**
     * Parse CSV file
     *
     * @return array $out
     */
    public function parse(): array
    {
        $row = 0;
        $out = [];
        $csvPath = Storage::disk('csv')->path('rates.csv');

        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                // Split header & data
                $row == 0 
                    ? $out['header'][] = $data
                    : $out['data'][] = $data;

                $row++;
            }

            fclose($handle);
        }

        return $out;
    }

    /**
     * Transform CSV file
     *
     * @param array $csvArray
     * @return array $out
     */
    public function transform(array $csvArray): array
    {
        $out = [];

        foreach ($csvArray['data'] as $line) {
            $i = 0;

            foreach ($line as $rate) {
                // Trim rate before processing
                $tRate = trim($rate);
                $service = $line[0];

                if ($tRate == 'ok' || $tRate == 'okC') {
                    $out[$service][] = $csvArray['header'][0][$i];
                }

                $i++;
            }
        }

        return $out;
    }

    /**
     * Write CSV array to db
     *
     * @param array $csvArray
     * @return void
     */
    public function write(array $csvArray): void
    {
        foreach ($csvArray as $rateCombinationKey => $rateCombinationValue) {
            foreach ($rateCombinationValue as $rate) {
                Rate::updateOrCreate([
                    'service' => $rateCombinationKey,
                    'rate' => $rate
                ]);
            }
        }
    }
}
