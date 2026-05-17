<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductsImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [new ProductsImportTemplateSheet(), new ProductsCategoryReferenceSheet()];
    }
}

class ProductsImportTemplateSheet implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\WithTitle
{
    public function title(): string
    {
        return 'Import Template Data Products';
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('dashboard.products.sheets.import_template');
    }
}

class ProductsCategoryReferenceSheet implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\WithTitle
{
    public function title(): string
    {
        return 'Daftar Referensi Products';
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        return view('dashboard.products.sheets.reference_template', compact('categories'));
    }
}
