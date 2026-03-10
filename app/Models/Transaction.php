<?php

namespace App\Models;

use App\Models\Outlet;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Field yang boleh diisi secara massal
    protected $fillable = ['invoice_number', 'outlet_id', 'user_id', 'total_price', 'paid_amount', 'change_amount'];

    // Relasi ke Outlet (Satu transaksi milik satu outlet)
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Relasi ke User/Kasir (Satu transaksi dicatat oleh satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Detail (Satu transaksi punya banyak barang)
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
