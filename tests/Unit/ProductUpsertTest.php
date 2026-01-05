<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ProductCsvImportService;

class ProductUpsertTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_upsert_by_sku()
    {
        Product::create([
            'sku' => 'SKU1',
            'name' => 'Old',
            'price' => 50,
        ]);

        $csv = "sku,name,price\nSKU1,New,100";
        $path = storage_path('app/test.csv');
        file_put_contents($path, $csv);

        $service = new ProductCsvImportService();
        $service->import($path);

        $this->assertEquals(1, Product::count());
        $this->assertEquals('New', Product::first()->name);
    }
}
