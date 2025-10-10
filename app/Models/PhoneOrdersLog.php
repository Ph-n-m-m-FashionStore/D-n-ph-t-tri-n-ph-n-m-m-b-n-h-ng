<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneOrdersLog extends Model
{
    protected $table = 'phone_orders_logs';
    protected $fillable = ['user_id', 'order_id', 'phone'];
}
