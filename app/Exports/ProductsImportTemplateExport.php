<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductsImportTemplateExport implements WithMultipleSheets
{

    protected $categories, $suppliers, $outlets;

    public function __construct($categories, $suppliers, $outlets)
    {
        $this->categories = $categories;
        $this->suppliers = $suppliers;
        $this->outlets = $outlets;
    }

    public function sheets(): array
    {
        return [
            new ProductsImportSheet(),
            new ReferenceProductSheet($this->categories, $this->suppliers, $this->outlets)
        ];
    }
}
