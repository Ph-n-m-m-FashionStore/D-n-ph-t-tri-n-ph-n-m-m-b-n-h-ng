<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'product_type',
        'color',
        'reference_id',
        'is_active',
        'stock',
        'brand',
        'size',
        'gender',
    ];

    public function carVariant()
    {
        return $this->belongsTo(CarVariant::class, 'reference_id');
    }

    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'reference_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->attributes['image_url']) {
            $url = $this->attributes['image_url'];
            // Nếu là URL ngoài
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
            // Nếu là đường dẫn /images/... thì trả về asset('images/...')
            if (strpos($url, '/images/') === 0) {
                return asset($url);
            }
            // Nếu là file trong storage
            return asset('storage/' . ltrim($url, '/'));
        }
        // Placeholder nếu chưa có ảnh
        $productName = $this->name ?? 'Product';
        $encodedName = urlencode($productName);
        return "https://via.placeholder.com/300x200/10b981/ffffff?text={$encodedName}";
    }
}
