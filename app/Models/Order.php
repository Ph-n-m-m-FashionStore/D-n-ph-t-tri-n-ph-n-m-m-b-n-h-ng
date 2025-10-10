<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone', 
        'address',
        'note',
        'status',
        'total',
        'payment_type'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Alias for orderItems để tương thích
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'shipping' => 'primary',
            'completed' => 'success',
            'canceled' => 'danger'
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getCustomerNameAttribute()
    {
        return $this->name ?: $this->user->name;
    }

    public function getCustomerPhoneAttribute()
    {
        return $this->phone ?: $this->user->phone;
    }
}