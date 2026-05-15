<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Remove middleware for now
    // public function __construct()
    // {
    //     $this->middleware('auth')->except(['index', 'show']);
    // }

    public function index()
    {
        $products = Product::with('reviews')->get();
        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load('reviews.user');
        $averageRating = $product->averageRating();
        $totalReviews = $product->totalReviews();
        
        return view('products.show', compact('product', 'averageRating', 'totalReviews'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);

        Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }
}