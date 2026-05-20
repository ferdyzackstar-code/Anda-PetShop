<?php

namespace App\Http\Controllers;

use App\Models\SettingApp;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SettingApp::allAsArray();
        return view('dashboard.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|min:4|max:100',
            'app_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'auth_title_login' => 'nullable|string|max:100',
            'auth_subtitle_login' => 'nullable|string|max:255',
            'auth_title_register' => 'nullable|string|max:100',
            'auth_subtitle_register' => 'nullable|string|max:255',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:30',
        ],
        [
            'app_name.required' => 'Nama harus diisi!',
            'app_name.min' => 'Nama minimal 4 karakter!',
            'app_name.max' => 'Nama maksimal 200 karakter!',
            'app_image.mimes' => 'Foto harus dalam format jpg, jpeg, png atau webp!',
            'app_image.max' => 'Ukuran foto maksimal 4 mb!',
            'auth_title_login.max' => 'Judul panel login maksimal 100 karakter!',
            'auth_subtitle_login.max' => 'Judul panel login maksimal 255 karakter!',
            'auth_title_register.max' => 'Sub judul panel login maksimal 100 karakter!',
            'auth_subtitle_register.max' => 'Judul panel register maksimal 255 karakter!',
            'store_address.max' => 'Alamat toko maksimal 500 karakter!',
            'store_phone.max' => 'Nomor telepon toko maksimal 30 karakter!',
        ]);

        $textKeys = ['app_name', 'auth_title_login', 'auth_subtitle_login', 'auth_title_register', 'auth_subtitle_register', 'store_address', 'store_phone'];

        foreach ($textKeys as $key) {
            SettingApp::set($key, $request->input($key));
        }

        if ($request->hasFile('app_image')) {
            if (!empty($setting->app_image) && file_exists(public_path('storage/uploads/logos/' . $setting->app_image))) {
                unlink(public_path('storage/uploads/logos/' . $setting->app_image));
            }

            $file = $request->file('app_image');
            $filename = time() . '-' . $file->getClientOriginalName();
            $destinationPath = public_path('storage/uploads/logos');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            SettingApp::set('app_image', $filename);

        }

        return redirect()->route('dashboard.settings.index')->with('success', 'Pengaturan Berhasil Diperbarui.');
    }
}
