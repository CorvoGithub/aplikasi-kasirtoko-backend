<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'user_id',
        'nama_produk',
        'foto',
        'harga_modal',
        'harga_jual',
        'stok',
        'deskripsi',
    ];

    protected $casts = [
        'harga_modal' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'stok' => 'integer',
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
     * Scope untuk produk milik user tertentu
     */
    public function scopeMilikUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}