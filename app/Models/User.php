<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'role', 'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'admin_id');
    }

    public function phoneOrdersLog()
    {
        return $this->hasMany(PhoneOrdersLog::class);
    }

    // Bổ sung method isAdmin()
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Quan hệ với reviews (nếu có)
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}