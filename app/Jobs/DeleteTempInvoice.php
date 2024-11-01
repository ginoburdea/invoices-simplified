<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteTempInvoice implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $file_name)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $file_path = env('TEMP_INVOICE_FOLDER') . '/' . $this->file_name;

        if (!file_exists($file_path)) {
            return;
        };

        unlink($file_path);
    }
}
