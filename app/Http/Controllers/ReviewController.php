<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, $product)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);
        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $product,
            'rating' => $request->rating ?? 5,
            'comment' => $request->comment,
        ]);
        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}
