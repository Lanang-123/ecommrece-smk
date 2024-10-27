<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_product',
        'title',
        'banner',
        'description',
        'start_date',
        'end_date',
        'discount_percentage'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
