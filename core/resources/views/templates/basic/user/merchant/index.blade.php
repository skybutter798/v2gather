@extends($activeTemplate . 'layouts.master')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    
        .product-box {
        position: relative;
        text-align: center;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column; /* Stack the content vertically */
        justify-content: space-between; /* Space out the content */
        height: 100%; /* Expand to fill the container */
    }
    
    .product-image-container {
        position: relative;
        overflow: hidden;
    }
    
    .product-image {
        display: block;
        width: 100%;
        transition: transform 0.5s ease;
    }
    
    .overlay {
        position: absolute !important;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        opacity: 0;
        transition: opacity 0.5s ease;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .cart-icon {
        display: inline-block;
        color: #a5d1ff;
        font-size: 30px;
        text-decoration: none;
    }
    
    .product-box:hover .overlay {
        opacity: 1;
    }
    
    .product-box:hover .product-image {
        transform: scale(1.05);
    }

    .product-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        border: solid;
        border-radius: 10px;
        margin-top: 10px;
        margin-bottom:50px;
        background: black;
    }
    
    .product-price, .product-buy {
        margin: 0;
        width: calc(50% - 5px);
        box-sizing: border-box; */
    }
    
    .buy-now-btn {
        display: block; /* Ensure it fills the .product-buy container */
        width: 100%; /* Full width of its container */
        background-color: #56b0f6;
        color: white;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    
    .price-btn {
        display: block;
        width: 100%;
        background-color: black;
        color: white;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 5px;
    }
    
    .buy-now-btn:hover {
        background-color: #c271f2;
        color: white;
    }
    
    .my-cart-btn {
        display: block;
        background-color: #56b0f6;
        color: white !important;
        text-decoration: none;
        padding: 20px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    
    .my-cart-btn:hover {
        background-color: #c271f2;
        color: white;
    }
    
    .swal2-confirm {
        background-color: #56b0f6 !important;
        color: white !important;
        width: 100px;
        border-radius: 20px;
        padding: 20px;
    }
    
</style>

@section('content')
<div class="container py-4">
    <div style="position: fixed; right: 20px; top: 120px; z-index: 1000;">
        <button id="my-cart-btn" class="my-cart-btn" onclick="showComingSoon()">
            <i class="fas fa-shopping-cart"></i> 
            <span id="cart-count" style="color:white">0</span> @lang('app.Items') - 
            @lang('app.Total EV'): <span id="total-amount" style="color:white">0</span>
        </button>
    </div>
    
    <h2 class="merchant-title">@lang('app.Merchants Showcase')</h2>
    <div class="product-showcase">
        <div class="row">
            @php
                $products = [
                    [
                        'name' => 'NMN',
                        'price' => 'EV 150',
                        'image' => 'https://images.squarespace-cdn.com/content/v1/64c0eda239e3b851046a61fe/a3280556-8f21-4a5d-9c64-e5783e7f7c30/NMN+1-min.jpg?format=2500w%20alt=',
                        'description' => 'NMN or nicotinamide mononucleotide is naturally-occurring in our bodies, but more of it is needed to support a longer healthier life.'
                    ],
                    [
                        'name' => 'Biological Age',
                        'price' => 'EV 90',
                        'image' => 'https://images.squarespace-cdn.com/content/v1/64c0eda239e3b851046a61fe/aa653984-c671-45b6-93cb-caa7a52ecd7d/clock+1-min.jpg?format=2500w%20alt=',
                        'description' => 'Ageing is cell damage and this damage can now be measured and repaired. Find your biological age with our saliva-based DNA methylation lab test.'
                    ],
                    [
                        'name' => 'Niagen® NR',
                        'price' => 'EV 170',
                        'image' => 'https://images.squarespace-cdn.com/content/v1/64c0eda239e3b851046a61fe/712fdccc-a7f8-420c-b796-9a5db9c51b13/The+Plus+Homepage+01-min.jpg?format=1500w',
                        'description' => 'The Plus - Niagen® NR is a science-backed anti-ageing supplement that boosts NAD+ levels by over 50% in just two weeks.'
                    ],
                    [
                        'name' => 'Resveratrol',
                        'price' => 'EV 125',
                        'image' => 'https://images.squarespace-cdn.com/content/v1/64c0eda239e3b851046a61fe/86b4fb3e-31a8-4c45-bf5f-40de2beb104c/resveratrol+1-min.jpg?format=2500w%20alt=',
                        'description' => 'Resveratrol is a science-backed anti-ageing supplement that is found naturally in grape skin.'
                    ],
                    [
                        'name' => 'Collagen',
                        'price' => 'EV 80',
                        'image' => 'https://images.squarespace-cdn.com/content/v1/64c0eda239e3b851046a61fe/486af6a3-ce6e-40af-94f6-daf8e2e4d7af/collagen+1-min.jpg?format=2500w%20alt=',
                        'description' => 'Hydrolysed marine collagen is a science-backed anti-ageing supplement, made from fish scale.'
                    ],
                    [
                        'name' => 'Green Tea',
                        'price' => 'EV 150',
                        'image' => 'https://images.squarespace-cdn.com/content/v1/64c0eda239e3b851046a61fe/9824b880-ee4b-43a8-b2f1-ad88e065da5a/the+lean+1.jpg?format=1500w',
                        'description' => 'Boost your metabolism without the caffeine spike: The Lean combines Green Tea'
                    ]
                ];
            @endphp

            @foreach($products as $product)
            <div class="col-md-4">
                <div class="product-box">
                    <div class="product-image-container">
                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="product-image">
                        <div class="overlay">
                            <a href="javascript:void(0);" class="cart-icon" onclick="addToCart('{{ $product['price'] }}')">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                        </div>
                    </div>
                    <p class="h1">{{ $product['name'] }}</p>
                    <p class="span">{{ $product['description'] }}</p>
                    <div class="product-info">
                        <p class="product-price"><span class="price-btn">{{ $product['price'] }}</span></p>
                        <p class="product-buy"><a href="javascript:void(0);" class="buy-now-btn" onclick="addToCart('{{ $product['price'] }}')">@lang('app.+ Cart')</a></p>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</div>

<script>
    let totalAmount = 0; // Initialize total amount

    function addToCart(price) {
        // Increment the cart count
        var cartCountElement = document.getElementById('cart-count');
        var currentCount = parseInt(cartCountElement.textContent);
        cartCountElement.textContent = currentCount + 1;

        // Update the total amount
        totalAmount += parseFloat(price.replace('EV ', '')); // Assuming price format is "EV XX"
        document.getElementById('total-amount').textContent = totalAmount.toFixed(2); // Format to 2 decimal places
    }
    
    function showComingSoon() {
        Swal.fire({
            title: "{{ __('app.Coming Soon!') }}",
            text: "{{ __('app.Merchant store will be launched soon, stay tuned.') }}",
            icon: 'info',
            confirmButtonText: "{{ __('app.Ok') }}",
            customClass: {
                confirmButton: 'swal2-confirm' // This class should match the one defined in your <style> tag
            },
            buttonsStyling: false // Disable SweetAlert2's default button styling
        });
    }
</script>

@endsection
