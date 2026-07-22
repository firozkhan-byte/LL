<?php

namespace App\Console\Commands;

use App\Models\ProductBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkdownExpiringBatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excise:markdown-expiring-batches 
                            {--days=30 : Days before expiry to trigger markdown} 
                            {--discount=20.00 : Discount percentage to apply}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically discount product batches nearing expiry to prevent write-offs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = intval($this->option('days'));
        $discountPct = floatval($this->option('discount'));

        $thresholdDate = now()->addDays($days)->format('Y-m-d');

        $expiringBatches = ProductBatch::where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $thresholdDate)
            ->where('is_markdown', false)
            ->get();

        if ($expiringBatches->isEmpty()) {
            $this->info('No new expiring batches found for markdown.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiringBatches->count()} expiring batch(es) for markdown processing...");

        foreach ($expiringBatches as $batch) {
            $originalPrice = $batch->selling_price;
            $newPrice = $originalPrice * (1 - ($discountPct / 100));

            $batch->update([
                'selling_price' => $newPrice,
                'is_markdown' => true,
                'markdown_percent' => $discountPct,
            ]);

            // Log operational audit
            Log::info("Excise Expiry Markdown: Batch {$batch->batch_number} of product ID {$batch->product_id} marked down from ₹{$originalPrice} to ₹{$newPrice} ({$discountPct}% off).");
            
            $this->line("- Batch [{$batch->batch_number}]: Price reduced from ₹{$originalPrice} to ₹{$newPrice} (Exp: {$batch->expiry_date->format('Y-m-d')})");
        }

        $this->info('Expiry markdown processing completed successfully.');
        return Command::SUCCESS;
    }
}
