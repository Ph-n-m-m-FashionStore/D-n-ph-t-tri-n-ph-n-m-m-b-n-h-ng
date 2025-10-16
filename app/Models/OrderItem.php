<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'color_id',
        'quantity',
        'price',
        // snapshot fields
        'product_name',
        'product_image',
        'product_type',
        'product_reference',
        'product_color_name',
        'product_size',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo('App\\Models\\CarVariantColor', 'color_id');
    }

    /**
     * Return the display name for the ordered product: prefer snapshot value saved at order time
     */
    public function getDisplayNameAttribute()
    {
        return $this->product_name ?? optional($this->product)->name;
    }

    /**
     * Return image url to show in order: prefer snapshot then live product image
     */
    public function getDisplayImageAttribute()
    {
        if ($this->product_image) {
            // snapshot stored could be full url or storage path
            $url = $this->product_image;
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
            return asset('storage/' . ltrim($url, '/'));
        }
        return optional($this->product)->image_url;
    }

    /**
     * Return display product type
     */
    public function getDisplayTypeAttribute()
    {
        return $this->product_type ?? optional($this->product)->product_type;
    }
}