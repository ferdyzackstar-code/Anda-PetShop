<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductsImportSheet implements FromView, WithTitle
{
    public function view(): View
    {
        return view('dashboard.products.sheets.import_template');
    }

    public function title(): string
    {
        return 'Import Template Data Products';
    }
}