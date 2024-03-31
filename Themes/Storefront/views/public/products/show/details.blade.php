<div class="product-details-info position-relative flex-grow-1">
    <div class="details-info-top">
        <h1 class="product-name">{{ $product->name }}</h1>
        
<template v-if="totalReviews >= 1">
    <product-rating
        :rating-percent="ratingPercent"
        :review-count="totalReviews"
    >
    </product-rating>
</template>

<template v-cloak v-if="item.is_in_stock && item.qty <= 5">
    <div class="availability in-stock">
        @{{ $trans('storefront::product.left_in_stock', { count: item.qty }) }}
    </div>
</template>
        <div
            v-cloak
            v-else-if="item.is_out_of_stock"
            class="availability out-of-stock"
        >
            {{ trans('storefront::product.out_of_stock') }}
        </div>

        <div class="details-info-top-actions">
            <button
                class="btn btn-wishlist"
                :class="{ 'added': inWishlist }"
                @click="syncWishlist"
            >
                <i class="la-heart" :class="inWishlist ? 'las' : 'lar'"></i>
                {{ trans('storefront::product.wishlist') }}
            </button>

            <button
                class="btn btn-compare"
                :class="{ 'added': inCompareList }"
                @click="syncCompareList"
            >
                <i class="las la-sync-alt"></i>
                {{ trans('storefront::product.compare') }}
            </button>
        </div>
    </div>

    <div class="details-info-middle">
        @if ($product->variant)
            <div v-if="isActiveItem" class="product-price" v-html="item.formatted_price">
                {!! $item->is_active ? $item->formatted_price : '' !!}
            </div>
        @else
            <div class="product-price" v-html="item.formatted_price">
                {!! $item->formatted_price !!}
            </div>
        @endif

        <form
            @input="errors.clear($event.target.name)"
            @submit.prevent="addToCart"
        >
            @if ($product->variant)
                <div class="product-variants">
                    @include('public.products.show.variations')
                </div>
            @endif

            <div class="product-variants">
                @foreach ($product->options as $option)
                    @includeIf("public.products.show.custom_options.{$option->type}")
                @endforeach
            </div>

            <div class="details-info-middle-actions">
                <div class="number-picker-lg">
                    <label for="qty">{{ trans('storefront::product.quantity') }}</label>

                    <div class="input-group-quantity">
                        <input
                            type="text"
                            :value="cartItemForm.qty"
                            min="1"
                            :max="maxQuantity"
                            id="qty"
                            class="form-control input-number input-quantity"
                            :disabled="isAddToCartDisabled"
                            @focus="$event.target.select()"
                            @input="updateQuantity(Number($event.target.value))"
                            @keydown.up="updateQuantity(cartItemForm.qty + 1)"
                            @keydown.down="updateQuantity(cartItemForm.qty - 1)"
                        >

                        <span class="btn-wrapper">
                            <button
                                type="button"
                                aria-label="quantity"
                                class="btn btn-number btn-plus"
                                :disabled="isQtyIncreaseDisabled"
                                @click="updateQuantity(cartItemForm.qty + 1)"
                            >
                                +
                            </button>

                            <button
                                type="button"
                                aria-label="quantity"
                                class="btn btn-number btn-minus"
                                :disabled="isQtyDecreaseDisabled"
                                @click="updateQuantity(cartItemForm.qty - 1)"
                            >
                                -
                            </button>
                        </span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-add-to-cart"
                    :class="{'btn-loading': addingToCart }"
                    :disabled="isAddToCartDisabled"
                    v-text="isActiveItem ? $trans('storefront::product.add_to_cart') : $trans('storefront::product.unavailable')"
                >
                    {{ trans($item->is_active ? 'storefront::product.add_to_cart' : 'storefront::product.unavailable') }}
                </button>
            </div>
            
<div class="shipping" style="font-size: 15px; padding-left: 40px; padding-bottom:15px; padding-top:15px;">
<script src="https://cdn.lordicon.com/lordicon.js"></script>
<lord-icon
    src="https://cdn.lordicon.com/oqdmuxru.json"
    trigger="loop"
    delay="1000"
    colors="primary:#109121"
    style="width:20px;height:20px; padding-top: 4px;">
</lord-icon> <b>In stock</b>, product is ready to be shipped<br>

<?php
// Current date
$currentDate = new DateTime();

// Add 2 days to the current date
$deliveryDate = clone $currentDate;
$deliveryDate->modify('+2 days');

// Check if delivery day is Sunday, if so, add one more day
if ($deliveryDate->format('l') === 'Sunday') {
    $deliveryDate->modify('+1 day');
}

// Format delivery date
$deliveryDateString = "<b>" . $deliveryDate->format('l j F') . "</b>";

// Check if current time is before 23:00
if ($currentDate->format('H') < 23) {
    echo "<i class='las la-dolly' style='font-size: 20px; padding-bottom: 10px; padding-top: 6px;'></i> We try to deliver on $deliveryDateString";
} else {
    // If current time is after 23:00, add one more day for delivery
    $deliveryDate->modify('+1 day');
    $deliveryDateString = "<b>" . $deliveryDate->format('l j F') . "</b>";

    // Check if delivery day is Sunday, if so, add one more day
    if ($deliveryDate->format('l') === 'Sunday') {
        $deliveryDate->modify('+1 day');
        $deliveryDateString = "<b>" . $deliveryDate->format('l j F') . "</b>";
    }
    echo "<i class='las la-dolly' style='font-size: 20px; padding-top 6px; padding-bottom: 10px;'></i> We try to deliver on $deliveryDateString";
}
?>
<br>

  <img src="https://cdn.shopify.com/s/files/1/2707/0176/files/0x0_ebdf7474-ea3c-4621-9bb8-4080f44e2cec.png?v=1634042367" alt="Buy now, pay later (or in 3 settlements)" style="margin-bottom: 2px; width: 19px; height: 19px;">
    Buy now, pay later (or in 3 settlements)<br>

</div>



        </form>
    </div>

    <div class="details-info-bottom">
        <ul class="list-inline additional-info">

            @if ($product->categories->isNotEmpty())
                <li>
                    <label>{{ trans('storefront::product.categories') }}</label>

                    @foreach ($product->categories as $category)
                        <a href="{{ $category->url() }}">{{ $category->name }}</a>{{ $loop->last ? '' : ',' }}
                    @endforeach
                </li>
                
                <img src="https://livaxl.digitalprofile.me/public/storage/media/qLeNlAa53j01juRb9AAWwPADYtDh6lRW4Dw5JSXE.webp" style="width: 80%;"><br>
            @endif

            @if ($product->tags->isNotEmpty())
                <li>
                    <label>{{ trans('storefront::product.tags') }}</label>

                    @foreach ($product->tags as $tag)
                        <a href="{{ $tag->url() }}">{{ $tag->name }}</a>{{ $loop->last ? '' : ',' }}
                    @endforeach
                </li>
            @endif
        </ul>
    </div>
</div>

