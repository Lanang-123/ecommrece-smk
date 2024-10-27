<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderItem;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_cart',
        'alamat',
        'no_hp',
        'no_inv',
        'status_konfirmasi',
        'metode_pembayaran',
        'nama_akun',
        'total'
    ];

    public function orderItems(): HasMany
    {
        return $this->HasMany(OrderItem::class, 'id_order', 'id');
    }
}