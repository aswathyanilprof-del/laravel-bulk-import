<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class ProductCsvImportService
{
    public function import(string $path): array
    {
        $summary = [
            'total' => 0,
            'imported' => 0,
            'updated' => 0,
            'invalid' => 0,
            'duplicates' => 0,
        ];

        $seenSkus = [];

        LazyCollection::make(function () use ($path) {
            $handle = fopen($path, 'r');
            while (($row = fgetcsv($handle)) !== false) {
                yield $row;
            }
            fclose($handle);
        })
        ->skip(1)
        ->chunk(1000)
        ->each(function ($rows) use (&$summary, &$seenSkus) {
            DB::transaction(function () use ($rows, &$summary, &$seenSkus) {
                foreach ($rows as $row) {
                    $summary['total']++;

                    if (count($row) < 3) {
                        $summary['invalid']++;
                        continue;
                    }

                    [$sku, $name, $price] = $row;

                    if (isset($seenSkus[$sku])) {
                        $summary['duplicates']++;
                        continue;
                    }

                    $seenSkus[$sku] = true;

                    $product = Product::where('sku', $sku)
                        ->lockForUpdate()
                        ->first();

                    if ($product) {
                        $product->update([
                            'name' => $name,
                            'price' => $price,
                        ]);
                        $summary['updated']++;
                    } else {
                        Product::create([
                            'sku' => $sku,
                            'name' => $name,
                            'price' => $price,
                        ]);
                        $summary['imported']++;
                    }
                }
            });
        });

        return $summary;
    }
}
