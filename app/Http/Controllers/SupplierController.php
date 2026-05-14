<?php

namespace App\Http\Controllers;

use App\Imports\SuppliersImport;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:supplier.index|supplier.create|supplier.edit|supplier.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:supplier.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:supplier.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:supplier.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Supplier::latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $color = $row->status == 'active' ? 'success' : 'danger';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' .
                        route('dashboard.suppliers.edit', $row->id) .
                        '" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="' .
                        route('dashboard.suppliers.destroy', $row->id) .
                        '" method="POST" style="display:inline">
                            ' .
                        csrf_field() .
                        method_field('DELETE') .
                        '
                            <button type="submit" class="btn btn-danger btn-sm show_confirm">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('dashboard.suppliers.index');
    }

    public function create()
    {
        return view('dashboard.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'name' => 'required',
                'status' => 'required',
                'email' => 'nullable|email',
                'city' => 'nullable',
                'phone' => 'nullable',
                'address' => 'nullable',
            ],
            [
                'name.required' => 'Nama harus diisi!',
                'status.required' => 'Status harus diisi!',
                'email.email' => 'Email harus dalam format email!',
            ],
        );

        Supplier::create($data);
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier Berhasil Ditambah!');
    }

    public function edit(Supplier $supplier)
    {
        return view('dashboard.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate(
            [
                'name' => 'required',
                'status' => 'required',
                'email' => 'nullable|email',
                'city' => 'nullable',
                'phone' => 'nullable',
                'address' => 'nullable',
            ],
            [
                'name.required' => 'Nama harus diisi!',
                'status.required' => 'Status harus diisi!',
                'email.email' => 'Email harus dalam format email!',
            ],
        );

        $supplier->update($data);
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier Berhasil Diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier Berhasil Dihapus!');
    }

    public function downloadImportTemplate()
    {
        return Excel::download(new \App\Exports\SuppliersImportTemplateExport(), 'template_import_data_suppliers.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'file' => 'required|mimes:xlsx,xls,csv',
            ],
            [   
                'file.required' => 'File harus diisi!',
                'file.mimes' => 'File harus dalam format xlsx, xls atau csv!',
            ],
        );

        $import = new SuppliersImport();
        Excel::import($import, $request->file('file'));

        $failures = $import->failures();
        $imported = $import->getImportedCount();

        if ($imported === 0 && $failures->isEmpty()) {
            return back()->withErrors(['file' => 'File kosong atau tidak mengandung data yang valid!']);
        }

        $failureMessages = $failures->isNotEmpty()
            ? $failures
                ->map(function ($failure) {
                    return $failure->errors()[0] ?? 'Unknown error';
                })
                ->toArray()
            : [];

        if ($imported > 0 && $failures->isNotEmpty()) {
            return back()
                ->with('success', "Berhasil import {$imported} supplier!")
                ->with('import_failures', $failureMessages);
        }

        if ($imported === 0 && $failures->isNotEmpty()) {
            return back()->with('import_failures', $failureMessages);
        }

        return redirect()
            ->route('dashboard.suppliers.index')
            ->with('success', "Berhasil import {$imported} supplier!");
    }

    public function export()
    {
        $suppliers = Supplier::all();

        $fileName = 'data_suppliers_anda_petshop_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new \App\Exports\SuppliersExport($suppliers), $fileName);
    }
}
