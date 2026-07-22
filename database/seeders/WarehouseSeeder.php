<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouse = DB::table('warehouses')->where('code', 'WH-MUM-CTR')->first();
        $product1 = DB::table('products')->first();
        $product2 = DB::table('products')->skip(1)->first();

        if (! $warehouse || ! $product1) {
            return;
        }

        // 1. Racks
        $rackId = Str::uuid()->toString();
        DB::table('warehouse_racks')->insert([
            'id' => $rackId,
            'warehouse_id' => $warehouse->id,
            'code' => 'RACK-A',
            'name' => 'Aisle A - Spirits',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Shelves
        $shelfId = Str::uuid()->toString();
        DB::table('warehouse_shelves')->insert([
            'id' => $shelfId,
            'rack_id' => $rackId,
            'code' => 'SHELF-A1',
            'name' => 'Shelf Level 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Bins
        $bin1Id = Str::uuid()->toString();
        $bin2Id = Str::uuid()->toString();
        DB::table('warehouse_bins')->insert([
            [
                'id' => $bin1Id,
                'shelf_id' => $shelfId,
                'code' => 'BIN-A1-S1-01',
                'name' => 'Bin 01',
                'capacity_weight' => 200.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => $bin2Id,
                'shelf_id' => $shelfId,
                'code' => 'BIN-A1-S1-02',
                'name' => 'Bin 02',
                'capacity_weight' => 200.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. Bin Inventories
        DB::table('bin_inventories')->insert([
            [
                'id' => Str::uuid()->toString(),
                'bin_id' => $bin1Id,
                'product_id' => $product1->id,
                'quantity' => 120.00,
                'batch_number' => 'BATCH-JW-100X',
                'expiry_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        if ($product2) {
            DB::table('bin_inventories')->insert([
                [
                    'id' => Str::uuid()->toString(),
                    'bin_id' => $bin2Id,
                    'product_id' => $product2->id,
                    'quantity' => 80.00,
                    'batch_number' => 'BATCH-GF-50Y',
                    'expiry_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
