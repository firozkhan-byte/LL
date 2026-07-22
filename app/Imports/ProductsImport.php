<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        $sku = $row['sku'] ?? ('SKU-'.strtoupper(Str::random(8)));

        return Product::updateOrCreate(
            ['sku' => $sku],
            [
                'name' => $row['name'],
                'slug' => Str::slug($row['name']).'-'.strtolower(Str::random(4)),
                'barcode' => $row['barcode'] ?? null,
                'liquor_type' => $row['liquor_type'] ?? 'Spirit',
                'volume_ml' => intval($row['volume_ml'] ?? 750),
                'alcohol_percentage' => floatval($row['alcohol_percentage'] ?? 40.00),
                'mrp' => floatval($row['mrp'] ?? 0.00),
                'selling_price' => floatval($row['selling_price'] ?? 0.00),
                'purchase_price' => floatval($row['purchase_price'] ?? 0.00),
                'origin_country' => $row['origin_country'] ?? null,
                'origin_region' => $row['origin_region'] ?? null,
                'expiry_tracking' => strtolower($row['expiry_tracking'] ?? '') === 'yes',
                'batch_tracking' => strtolower($row['batch_tracking'] ?? '') === 'yes',
                'serial_tracking' => strtolower($row['serial_tracking'] ?? '') === 'yes',
            ]
        );
    }
}
