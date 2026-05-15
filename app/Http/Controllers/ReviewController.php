<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Remove middleware for now
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if user already reviewed
        $existingReview = Review::where('product_id', $product->id)
                                ->where('user_id', Auth::id())
                                ->exists();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product!');
        }

        $review = new Review([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        $review->save();

        return redirect()->route('products.show', $product)
                        ->with('success', 'Review added successfully!');
    }

    public function destroy(Review $review)
    {
        // Check if user owns the review
        if (Auth::id() !== $review->user_id) {
            return back()->with('error', 'Unauthorized action!');
        }
        
        $productId = $review->product_id;
        $review->delete();

        return redirect()->route('products.show', $productId)
                        ->with('success', 'Review deleted successfully!');
    }
}