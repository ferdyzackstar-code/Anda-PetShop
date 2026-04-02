<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsImportTemplateExport implements FromView, ShouldAutoSize
{
    protected $categories;
    protected $suppliers;
    protected $outlets;

    public function __construct($categories, $suppliers, $outlets)
    {
        $this->categories = $categories;
        $this->suppliers = $suppliers;
        $this->outlets = $outlets;
    }

    public function view(): View
    {
        return view('dashboard.products.import_template_excel', [
            'categories' => $this->categories,
            'suppliers' => $this->suppliers,
            'outlets' => $this->outlets,
        ]);
    }
}
