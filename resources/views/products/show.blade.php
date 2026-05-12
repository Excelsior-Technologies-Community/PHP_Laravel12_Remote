@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h1>{{ $product->name }}</h1>
                <p class="lead">{{ $product->description }}</p>
                <h3 class="text-primary">${{ number_format($product->price, 2) }}</h3>
                <p class="text-muted">Stock: {{ $product->stock }} units</p>
                
                <div class="mb-3">
                    <h5>Rating: {{ $averageRating }} / 5</h5>
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $averageRating)
                            <i class="fas fa-star text-warning fa-2x"></i>
                        @elseif($i - 0.5 <= $averageRating)
                            <i class="fas fa-star-half-alt text-warning fa-2x"></i>
                        @else
                            <i class="far fa-star text-warning fa-2x"></i>
                        @endif
                    @endfor
                    <p class="text-muted">Based on {{ $totalReviews }} reviews</p>
                </div>

                <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h4>Customer Reviews</h4>
            </div>
            <div class="card-body">
                @auth
                    @php
                        $userReview = $product->reviews->where('user_id', Auth::id())->first();
                    @endphp
                    
                    @if(!$userReview)
                        <form action="{{ route('reviews.store', $product) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating-input">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="me-2">
                                            <input type="radio" name="rating" value="{{ $i }}" required>
                                            {{ $i }} Star
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Comment</label>
                                <textarea name="comment" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    @endif
                @else
                    <div class="alert alert-info">
                        <a href="{{ route('login') }}">Login</a> to write a review.
                    </div>
                @endauth

                <hr>

                @forelse($product->reviews as $review)
                    <div class="review-item mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $review->user->name }}</strong>
                                <div class="mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p>{{ $review->comment }}</p>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                            
                            @auth
                                @if(Auth::id() === $review->user_id)
                                    <div>
                                        <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete review?')">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr>
                    @endif
                @empty
                    <p class="text-muted">No reviews yet. Be the first to review!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection