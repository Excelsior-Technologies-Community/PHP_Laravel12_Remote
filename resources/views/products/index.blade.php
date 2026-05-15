@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Products</h1>
            @auth
                <a href="{{ route('products.create') }}" class="btn btn-primary">Add New Product</a>
            @endauth
        </div>

        <div class="row">
            @forelse($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                            <h6 class="text-primary">{{ number_format($product->price, 2) }}</h6>
                            
                            <div class="mb-2">
                                @php $avg = $product->averageRating(); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $avg)
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($i - 0.5 <= $avg)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                                <span class="text-muted">({{ $product->totalReviews() }} reviews)</span>
                            </div>
                            
                            <a href="{{ route('products.show', $product) }}" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No products found.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection