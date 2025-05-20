<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;

class CleanupTransactionFiles extends Command
{
    protected $signature = 'transactions:cleanup-files';
    protected $description = 'Cleanup invalid file references in transactions';

    public function handle()
    {
        $transactions = Transaction::whereNotNull('bukti_pembayaran')->get();
        $totalFixed = 0;

        foreach ($transactions as $transaction) {
            $originalFiles = $transaction->getRawOriginal('bukti_pembayaran') ?? [];
            
            if (!is_array($originalFiles)) {
                $originalFiles = json_decode($originalFiles, true) ?? [];
            }

            $validFiles = [];
            foreach ($originalFiles as $file) {
                if (Storage::exists($file)) {
                    $validFiles[] = $file;
                }
            }

            if (count($originalFiles) !== count($validFiles)) {
                $transaction->bukti_pembayaran = $validFiles;
                $transaction->save();
                $totalFixed++;
                $this->info("Fixed transaction ID: {$transaction->id}");
            }
        }

        $this->info("Total transactions fixed: {$totalFixed}");
        return 0;
    }
}