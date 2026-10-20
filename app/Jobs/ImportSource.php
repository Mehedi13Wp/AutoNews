<?php

namespace App\Jobs;

use App\Models\Source;
use App\Services\NewsImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportSource implements ShouldQueue
{
    use Queueable;

    public int $timeout = 1100;

    public int $tries = 1;

    public function __construct(public int $sourceId) {}

    public function handle(NewsImporter $importer): void
    {
        if ($source = Source::find($this->sourceId)) {
            $importer->run($source);
        }
    }
}
