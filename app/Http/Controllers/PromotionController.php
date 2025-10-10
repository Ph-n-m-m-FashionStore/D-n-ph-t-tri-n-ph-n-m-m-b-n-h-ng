<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::active()->get();
        return view('promotions', compact('promotions'));
    }
}
