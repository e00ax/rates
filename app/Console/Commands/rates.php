<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CsvParseService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class rates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses rates & services';

    /**
     * CSV parsing service
     * 
     * @var CsvParseService
     */
    protected CsvParseService $csvParseService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CsvParseService $csvParseService)
    {
        $this->csvParseService = $csvParseService;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $start = Carbon::now();
            Log::warning('########################################################################');
            Log::warning('Command: ' . $this->getName());
            Log::warning('Start: ' . $start->toISOString());

            // Parse service & rate CSV file
            echo "Parsing services & rates...\n";
            $parse = $this->csvParseService->parse();
            echo "done...\n\n";

            // Transform CSV to array
            echo "Transforming CSV...\n";
            $transform = $this->csvParseService->transform($parse);
            echo "done...\n\n";

            // Write transformed to db
            echo "Write transformed to db...\n";
            $this->csvParseService->write($transform);
            echo "done...\n\n";

            $end = Carbon::now();
            $duration = $end->diffinSeconds($start);
            Log::warning('End: ' . $end->toISOString());
            Log::warning('Duration: ' . $duration);
            Log::warning('########################################################################');
        } catch (\Throwable $e) {
            Log::error('Unable to handle service & rates CSV: ' . $e);
        }
        
        return 0;
    }
}
