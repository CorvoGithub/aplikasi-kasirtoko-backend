<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'user_id',
        'kode_transaksi',
        'total_harga',
        'uang_diberikan',
        'kembalian',
        'catatan',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'uang_diberikan' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke TransaksiDetail
     */
    public function transaksiDetails()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    /**
     * Relasi ke Produk melalui TransaksiDetail
     */
    public function produk()
    {
        return $this->hasManyThrough(Produk::class, TransaksiDetail::class, 'transaksi_id', 'id', 'id', 'produk_id');
    }

    /**
     * Scope untuk transaksi milik user tertentu
     */
    public function scopeMilikUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Generate kode transaksi
     */
    public static function generateKodeTransaksi()
    {
        $date = now()->format('Ymd');
        $lastTransaction = self::where('kode_transaksi', 'like', "TRX-$date-%")->latest()->first();
        
        $number = $lastTransaction ? (int) substr($lastTransaction->kode_transaksi, -4) + 1 : 1;
        
        return "TRX-{$date}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}