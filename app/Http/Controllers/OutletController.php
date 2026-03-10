<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function index()
    {
        $outlets = Outlet::all();
        return view('dashboard.outlets.index', compact('outlets'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'address' => 'required']);
        Outlet::create($request->all());
        return redirect()->back()->with('success', 'Outlet berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $outlet = Outlet::findOrFail($id);
        $outlet->update($request->all());
        return redirect()->back()->with('success', 'Outlet berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Outlet::destroy($id);
        return redirect()->back()->with('success', 'Outlet berhasil dihapus!');
    }
}
