<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductCsvImportService;

class ProductImportController extends Controller
{

    public function import(Request $request, ProductCsvImportService $service)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $fullPath = $file->getRealPath();

        return response()->json(
            $service->import($fullPath)
        );
    }

}
