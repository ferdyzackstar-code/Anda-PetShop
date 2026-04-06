<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReferenceProductSheet implements FromView, WithTitle, ShouldAutoSize
{
    protected $categories, $suppliers, $outlets;

    public function __construct($categories, $suppliers, $outlets)
    {
        $this->categories = $categories;
        $this->suppliers = $suppliers;
        $this->outlets = $outlets;
    }

    public function view(): View
    {
        return view('dashboard.products.sheets.reference_template', [
            'categories' => $this->categories,
            'suppliers' => $this->suppliers,
            'outlets' => $this->outlets,
        ]);
    }

    public function title(): string
    {
        return 'Daftar Referensi Products'; 
    }
}