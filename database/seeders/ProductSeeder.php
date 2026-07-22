<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Categories
        $whiskeyId = Str::uuid()->toString();
        $beerId = Str::uuid()->toString();
        $wineId = Str::uuid()->toString();
        $vodkaId = Str::uuid()->toString();

        $singleMaltId = Str::uuid()->toString();
        $blendedId = Str::uuid()->toString();
        $lagerId = Str::uuid()->toString();
        $redWineId = Str::uuid()->toString();

        DB::table('categories')->insert([
            // Parents
            ['id' => $whiskeyId, 'parent_id' => null, 'name' => 'Whiskey', 'slug' => 'whiskey', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $beerId, 'parent_id' => null, 'name' => 'Beer', 'slug' => 'beer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $wineId, 'parent_id' => null, 'name' => 'Wine', 'slug' => 'wine', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $vodkaId, 'parent_id' => null, 'name' => 'Vodka', 'slug' => 'vodka', 'created_at' => now(), 'updated_at' => now()],

            // Children
            ['id' => $singleMaltId, 'parent_id' => $whiskeyId, 'name' => 'Single Malt', 'slug' => 'single-malt', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $blendedId, 'parent_id' => $whiskeyId, 'name' => 'Blended Whiskey', 'slug' => 'blended-whiskey', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $lagerId, 'parent_id' => $beerId, 'name' => 'Lager', 'slug' => 'lager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $redWineId, 'parent_id' => $wineId, 'name' => 'Red Wine', 'slug' => 'red-wine', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Brands
        $brandJwId = Str::uuid()->toString();
        $brandGfId = Str::uuid()->toString();
        $brandCrId = Str::uuid()->toString();
        $brandBudId = Str::uuid()->toString();
        $brandJcId = Str::uuid()->toString();
        $brandGgId = Str::uuid()->toString();

        DB::table('brands')->insert([
            ['id' => $brandJwId, 'name' => 'Johnnie Walker', 'slug' => 'johnnie-walker', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $brandGfId, 'name' => 'Glenfiddich', 'slug' => 'glenfiddich', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $brandCrId, 'name' => 'Corona', 'slug' => 'corona', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $brandBudId, 'name' => 'Budweiser', 'slug' => 'budweiser', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $brandJcId, 'name' => "Jacob's Creek", 'slug' => 'jacobs-creek', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $brandGgId, 'name' => 'Grey Goose', 'slug' => 'grey-goose', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Manufacturers
        $mfgDiageoId = Str::uuid()->toString();
        $mfgUbId = Str::uuid()->toString();
        $mfgPernodId = Str::uuid()->toString();
        $mfgBacardiId = Str::uuid()->toString();

        DB::table('manufacturers')->insert([
            ['id' => $mfgDiageoId, 'name' => 'Diageo India Ltd', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $mfgUbId, 'name' => 'United Breweries Group', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $mfgPernodId, 'name' => 'Pernod Ricard India', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $mfgBacardiId, 'name' => 'Bacardi India Pvt Ltd', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Products
        DB::table('products')->insert([
            [
                'id' => Str::uuid()->toString(),
                'category_id' => $blendedId,
                'brand_id' => $brandJwId,
                'manufacturer_id' => $mfgDiageoId,
                'name' => 'Johnnie Walker Black Label 12 Years',
                'slug' => 'johnnie-walker-black-label-12-years',
                'sku' => 'SKU-JW-BLK-750',
                'barcode' => '5000267014207',
                'hsn_code' => '22083013',
                'gst_rate' => 18.00,
                'liquor_type' => 'Spirit',
                'volume_ml' => 750,
                'alcohol_percentage' => 40.00,
                'mrp' => 3800.00,
                'purchase_price' => 2800.00,
                'selling_price' => 3500.00,
                'origin_country' => 'Scotland',
                'origin_region' => 'Speyside',
                'expiry_tracking' => false,
                'batch_tracking' => true,
                'serial_tracking' => false,
                'description' => 'Johnnie Walker Black Label is a true icon, recognized as the benchmark for all other luxury blended whiskies.',
                'status' => 'active',
                'attributes' => json_encode(['vintage' => '12 Years', 'peat_level' => 'Medium']),
                'tags' => json_encode(['Whiskey', 'Premium', 'Blended']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'category_id' => $singleMaltId,
                'brand_id' => $brandGfId,
                'manufacturer_id' => $mfgPernodId,
                'name' => 'Glenfiddich 12 Year Old Single Malt',
                'slug' => 'glenfiddich-12-year-old-single-malt',
                'sku' => 'SKU-GF-12-700',
                'barcode' => '5010326000010',
                'hsn_code' => '22083012',
                'gst_rate' => 18.00,
                'liquor_type' => 'Spirit',
                'volume_ml' => 700,
                'alcohol_percentage' => 40.00,
                'mrp' => 6800.00,
                'purchase_price' => 5200.00,
                'selling_price' => 6500.00,
                'origin_country' => 'Scotland',
                'origin_region' => 'Highlands',
                'expiry_tracking' => false,
                'batch_tracking' => true,
                'serial_tracking' => false,
                'description' => 'Glenfiddich 12 Year Old is a classic single malt scotch whisky matured in fine Oloroso sherry and bourbon casks.',
                'status' => 'active',
                'attributes' => json_encode(['vintage' => '12 Years', 'peat_level' => 'None']),
                'tags' => json_encode(['Whiskey', 'Single Malt', 'Luxury']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'category_id' => $lagerId,
                'brand_id' => $brandCrId,
                'manufacturer_id' => $mfgUbId,
                'name' => 'Corona Extra Beer Bottle',
                'slug' => 'corona-extra-beer-bottle',
                'sku' => 'SKU-CRN-LAG-330',
                'barcode' => '7501064191388',
                'hsn_code' => '22030000',
                'gst_rate' => 18.00,
                'liquor_type' => 'Beer',
                'volume_ml' => 330,
                'alcohol_percentage' => 4.50,
                'mrp' => 280.00,
                'purchase_price' => 190.00,
                'selling_price' => 250.00,
                'origin_country' => 'Mexico',
                'origin_region' => 'Mexico City',
                'expiry_tracking' => true,
                'batch_tracking' => true,
                'serial_tracking' => false,
                'description' => 'Corona Extra is a premium lager beer, famous worldwide for its clean taste and classic lime-wedge companion.',
                'status' => 'active',
                'attributes' => json_encode(['color' => 'Golden', 'packaging' => 'Glass Bottle']),
                'tags' => json_encode(['Beer', 'Lager', 'Imported']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'category_id' => $lagerId,
                'brand_id' => $brandBudId,
                'manufacturer_id' => $mfgUbId,
                'name' => 'Budweiser Premium King of Beers Can',
                'slug' => 'budweiser-premium-can-500',
                'sku' => 'SKU-BUD-CAN-500',
                'barcode' => '8901045201124',
                'hsn_code' => '22030000',
                'gst_rate' => 18.00,
                'liquor_type' => 'Beer',
                'volume_ml' => 500,
                'alcohol_percentage' => 5.00,
                'mrp' => 180.00,
                'purchase_price' => 120.00,
                'selling_price' => 160.00,
                'origin_country' => 'India',
                'origin_region' => 'Maharashtra',
                'expiry_tracking' => true,
                'batch_tracking' => true,
                'serial_tracking' => false,
                'description' => 'Budweiser Premium Lager is brewed with fine barley malt and a blend of premium hop varieties.',
                'status' => 'active',
                'attributes' => json_encode(['color' => 'Pale Amber', 'packaging' => 'Aluminium Can']),
                'tags' => json_encode(['Beer', 'Lager', 'Can']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'category_id' => $redWineId,
                'brand_id' => $brandJcId,
                'manufacturer_id' => $mfgPernodId,
                'name' => "Jacob's Creek Shiraz Cabernet Red Wine",
                'slug' => 'jacobs-creek-shiraz-cabernet-red-wine',
                'sku' => 'SKU-JC-SHZ-750',
                'barcode' => '9300727008107',
                'hsn_code' => '22042190',
                'gst_rate' => 18.00,
                'liquor_type' => 'Wine',
                'volume_ml' => 750,
                'alcohol_percentage' => 13.90,
                'mrp' => 1400.00,
                'purchase_price' => 950.00,
                'selling_price' => 1200.00,
                'origin_country' => 'Australia',
                'origin_region' => 'Barossa Valley',
                'expiry_tracking' => false,
                'batch_tracking' => true,
                'serial_tracking' => false,
                'description' => "Jacob's Creek Shiraz Cabernet is a classic Australian red wine blend offering dark berry fruit characters with spice notes.",
                'status' => 'active',
                'attributes' => json_encode(['vintage' => '2024', 'flavor_profile' => 'Dry Fruity']),
                'tags' => json_encode(['Wine', 'Red Wine', 'Imported']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'category_id' => $vodkaId,
                'brand_id' => $brandGgId,
                'manufacturer_id' => $mfgBacardiId,
                'name' => 'Grey Goose Original Vodka',
                'slug' => 'grey-goose-original-vodka',
                'sku' => 'SKU-GG-VDK-750',
                'barcode' => '080660650228',
                'hsn_code' => '22086000',
                'gst_rate' => 18.00,
                'liquor_type' => 'Spirit',
                'volume_ml' => 750,
                'alcohol_percentage' => 40.00,
                'mrp' => 4800.00,
                'purchase_price' => 3600.00,
                'selling_price' => 4400.00,
                'origin_country' => 'France',
                'origin_region' => 'Cognac',
                'expiry_tracking' => false,
                'batch_tracking' => true,
                'serial_tracking' => false,
                'description' => 'Grey Goose is a premium French vodka crafted from fine Picardie winter wheat and natural Gensac spring water.',
                'status' => 'active',
                'attributes' => json_encode(['color' => 'Crystal Clear', 'filtration' => '5 Times']),
                'tags' => json_encode(['Vodka', 'Premium', 'French']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
