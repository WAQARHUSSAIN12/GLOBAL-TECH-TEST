<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Purchase;

class MigrateLegacyPurchases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:legacy-purchases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maps and injects raw unnormalized inputs safely into the schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Legacy multi-tiered raw array
        $legacyPurchases = [
            ['item_name' => 'Sugar', 'brand_name' => 'ABC', 'qty' => 10, 'price' => 100],
            ['item_name' => 'Sugar', 'brand_name' => 'XYZ', 'qty' => 5, 'price' => 110],
            ['item_name' => 'Flour', 'brand_name' => 'ABC', 'qty' => 2, 'price' => 250],
        ];

        $this->info('Starting legacy normalization pass...');

        DB::transaction(function () use ($legacyPurchases) {
            $totalInvoiceValue = 0;
            $itemsToProcess = [];

            foreach ($legacyPurchases as $row) {
                // Idempotent first-or-create updates mapping normalized identities 
                $item = Item::firstOrCreate(['name' => trim($row['item_name'])]);
                $brand = Brand::firstOrCreate(['name' => trim($row['brand_name'])]);

                $subTotal = $row['qty'] * $row['price'];
                $totalInvoiceValue += $subTotal;

                $itemsToProcess[] = [
                    'item_id' => $item->id,
                    'brand_id' => $brand->id,
                    'qty' => $row['qty'],
                    'price' => $row['price']
                ];
            }

            // FIX: Injecting user_id directly into the parent Purchase model record
            $purchase = Purchase::create([
                'total' => $totalInvoiceValue,
                'user_id' => 1 // Maps the main invoice entry to User ID 1
            ]);

            foreach ($itemsToProcess ?? [] as $data) {
                // Idempotent constraint verification lookup to prevent execution duplication
                $exists = DB::table('purchase_items')->where([
                    'purchase_id' => $purchase->id,
                    'item_id'     => $data['item_id'],
                    'brand_id'    => $data['brand_id']
                ])->exists();

                if (!$exists) {
                    $purchase->items()->create($data);
                }
            }
        });

        $this->info('Legacy operations successfully migrated without duplication constraints!');
    }
}