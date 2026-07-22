<?php

namespace Tests\Feature\Admin;

use App\Imports\ProductsImport;
use App\Livewire\Admin\ProductCatalog;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ProductImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');
    }

    public function test_product_export_trigger(): void
    {
        Excel::fake();

        Product::create([
            'name' => 'Test Brew',
            'volume_ml' => 500,
            'alcohol_percentage' => 5.0,
            'mrp' => 200,
            'purchase_price' => 150,
            'selling_price' => 180,
            'liquor_type' => 'Beer',
            'sku' => 'SKU-BREW-500',
            'status' => 'active',
        ]);

        $this->actingAs($this->adminUser)
            ->get(route('admin.products'));

        Livewire::actingAs($this->adminUser)
            ->test(ProductCatalog::class)
            ->call('exportExcel');

        Excel::assertDownloaded('products.xlsx');
    }

    public function test_product_import_excel_parsing(): void
    {
        $import = new ProductsImport;
        $row = [
            'name' => 'Imported Whisky Single Malt 750ml',
            'sku' => 'SKU-IMPT-WHI-750',
            'barcode' => '1234567890123',
            'liquor_type' => 'Spirit',
            'volume_ml' => '750',
            'alcohol_percentage' => '42.8',
            'mrp' => '4500',
            'selling_price' => '4200',
            'purchase_price' => '3200',
            'origin_country' => 'Scotland',
            'origin_region' => 'Highlands',
            'expiry_tracking' => 'No',
            'batch_tracking' => 'Yes',
            'serial_tracking' => 'No',
        ];

        $import->model($row);

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-IMPT-WHI-750',
            'name' => 'Imported Whisky Single Malt 750ml',
            'selling_price' => 4200.00,
            'batch_tracking' => true,
        ]);
    }
}
