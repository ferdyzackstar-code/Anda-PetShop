<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class SuppliersExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    protected $suppliers;

    public function __construct($suppliers)
    {
        $this->suppliers = $suppliers;
    }

    public function view(): View
    {
        return view('dashboard.suppliers.export_excel', [
            'suppliers' => $this->suppliers,
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'F' => '@',
        ];
    }
}
