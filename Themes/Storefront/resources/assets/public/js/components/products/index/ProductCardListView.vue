<template>
    <div class="list-product-card">
        <div class="list-product-card-inner">
            <div class="product-card-left position-relative">
                <a :href="productUrl" class="product-image">
                    <img
                        :src="baseImage"
                        :class="{ 'image-placeholder': !hasBaseImage }"
                        :alt="product.name"
                    />
                </a>

                <ul class="list-inline product-badge">
                    <li class="badge badge-danger" v-if="item.is_out_of_stock">
                        {{ $trans("storefront::product_card.out_of_stock") }}
                    </li>

                    <li class="badge badge-primary" v-else-if="product.is_new">
                        {{ $trans("storefront::product_card.new") }}
                    </li>

                    <li
                        class="badge badge-success"
                        v-if="item.has_percentage_special_price"
                    >
                        -{{ item.special_price_percent }}%
                    </li>
                </ul>
            </div>

            <div class="product-card-right">
                <a :href="productUrl" class="product-name">
                    <h6>{{ product.name }}</h6>
                </a>

                <div class="clearfix"></div>

                <product-rating
                    :ratingPercent="product.rating_percent"
                    :reviewCount="product.reviews.length"
                >
                </product-rating>

                <div class="product-price" v-html="item.formatted_price"></div>

                <button
                    v-if="hasNoOption || item.is_out_of_stock"
                    class="btn btn-default btn-add-to-cart"
                    :class="{ 'btn-loading': addingToCart }"
                    :disabled="item.is_out_of_stock"
                    @click="addToCart"
                >
                    <i class="las la-cart-arrow-down"></i>
                    {{ $trans("storefront::product_card.add_to_cart") }}
                </button>

                <a
                    v-else
                    :href="productUrl"
                    class="btn btn-default btn-add-to-cart"
                >
                    <i class="las la-eye"></i>
                    {{ $trans("storefront::product_card.view_options") }}
                </a>

                <div class="product-card-actions">
                    <button
                        class="btn btn-wishlist"
                        :class="{ added: inWishlist }"
                        @click="syncWishlist"
                    >
                        <i
                            class="la-heart"
                            :class="inWishlist ? 'las' : 'lar'"
                        ></i>
                        {{ $trans("storefront::product_card.wishlist") }}
                    </button>

                    <button
                        class="btn btn-compare"
                        :class="{ added: inCompareList }"
                        @click="syncCompareList"
                    >
                        <i class="las la-sync-alt"></i>
                        {{ $trans("storefront::product_card.compare") }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProductCardMixin from "../../../mixins/ProductCardMixin";

export default {
    mixins: [ProductCardMixin],

    props: ["product"],

    computed: {
        item() {
            return {
                ...(this.product.variant ? this.product.variant : this.product),
            };
        },
    },
};
</script>
