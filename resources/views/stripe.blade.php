<x-app-layout>  
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="container mt-2">
    <h1>Produits disponibles</h1>
    <div class="row">
        @if($products->isNotEmpty())
            @foreach($products as $product)
                <div class="col-md-4 mb-3 p-4 rounded h-fit w-fit">
                    <x-card-products :product="$product" />
                    <button class="btn btn-primary buy-now" data-product-id="{{ $product->id }}">
                                Acheter maintenant
                            </button>
                </div>
            @endforeach
        @else
            <p>Aucun produit disponible.</p>
        @endif
    </div>
</div>
</div>
</x-app-layout>

<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('pk_test_51QHM3BFg1vzIsRdlOOzNdzLzPDJm6UY9v0WwRrSqSFyFcd3KgCcHoP8Su4oXbHCsTPWxNqJR0om07Z2xk24wAVlx00lQFjwYKU');
    var buyButtons = document.getElementsByClassName('buy-now');

    Array.from(buyButtons).forEach(function(button) {
        button.addEventListener('click', function() {
            var productId = this.getAttribute('data-product-id');

            fetch('/payment/create-checkout-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(session) {
                return stripe.redirectToCheckout({ sessionId: session.id });
            })
            .then(function(result) {
                if (result.error) {
                    alert(result.error.message);
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
        });
    });
</script>
