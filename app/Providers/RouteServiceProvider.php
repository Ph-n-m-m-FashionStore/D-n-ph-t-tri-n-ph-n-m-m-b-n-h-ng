<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class RouteServiceProvider extends ServiceProvider
{
    public static function redirectTo(): string
    {
        $user = Auth::user();
        if ($user && $user->role === 'admin') {
            return '/admin/dashboard';
        }

        return '/dashboard';
    }
}