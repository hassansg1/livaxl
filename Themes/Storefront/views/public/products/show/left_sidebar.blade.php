<aside class="left-sidebar">
    @if ($upSellProducts->isEmpty())
        <div class="vertical-products">
            <div class="vertical-products-header">
                <h4 class="section-title">{{ trans('storefront::products.shop') }}</h4>
            </div>

            <div class="vertical-products-slider" ref="Products">
                @php
                    // Shuffle the products
                    $randomProducts = $upSellProducts->shuffle()->take(5);
                @endphp

                <div class="vertical-products-slide">
                    @foreach ($randomProducts as $randomProduct)
                        <product-card-vertical :product="{{ $randomProduct }}"></product-card-vertical>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</aside>
