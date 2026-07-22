<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return Product::all()->map(function ($p) {
            return [
                'name' => $p->name,
                'sku' => $p->sku,
                'barcode' => $p->barcode,
                'liquor_type' => $p->liquor_type,
                'volume_ml' => $p->volume_ml,
                'alcohol_percentage' => $p->alcohol_percentage,
                'mrp' => $p->mrp,
                'selling_price' => $p->selling_price,
                'purchase_price' => $p->purchase_price,
                'origin_country' => $p->origin_country,
                'origin_region' => $p->origin_region,
                'expiry_tracking' => $p->expiry_tracking ? 'Yes' : 'No',
                'batch_tracking' => $p->batch_tracking ? 'Yes' : 'No',
                'serial_tracking' => $p->serial_tracking ? 'Yes' : 'No',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'SKU',
            'Barcode',
            'Liquor Type',
            'Volume (ml)',
            'Alcohol %',
            'MRP',
            'Selling Price',
            'Purchase Price',
            'Origin Country',
            'Origin Region',
            'Expiry Tracking',
            'Batch Tracking',
            'Serial Tracking',
        ];
    }
}
