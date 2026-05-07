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

// ─────────────────────────────────────────────────────────────
//  Sheet 1 — Template Import (kolom header kosong)
// ─────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────
//  Sheet 2 — Referensi Kategori
//  FIX: hanya ambil parent (spesies), child dimuat via relasi
//  Sebelumnya: ambil semua → child muncul dobel di loop
// ─────────────────────────────────────────────────────────────
class ProductsCategoryReferenceSheet implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\WithTitle
{
    public function title(): string
    {
        return 'Daftar Referensi Products';
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        // Hanya ambil parent (spesies), dengan relasi children
        // Jangan ambil semua kategori — itu yang menyebabkan duplikasi
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        return view('dashboard.products.sheets.reference_template', compact('categories'));
    }
}
