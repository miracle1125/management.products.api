<template>
        <div class="swiper-container mb-3">
            <div class="swiper-wrapper">

                <div class="swiper-slide error bg-success text-right rounded">
                    <div class="swipe-action-container swipe-action-container--right rounded">
                        <div>PICK ALL</div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="row ml-1 mr-1">
                        <div class="col p-2 pl-3 rounded">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="text-primary h5">{{ pick['name_ordered'] }}</div>
                                    <div>sku: <b> {{ pick['sku_ordered'] }} </b></div>
                                    <div>product: <b> <a target="_blank" :href="'/products?sku=' + product_sku ">{{ product_sku }}</a></b></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-6 small ">
                                            <div>stock: <b> {{ Number(pick['inventory_source_quantity']) }} </b></div>
                                        </div>
                                        <div class="col-2 text-center" v-bind:class="{ 'bg-warning': Number(pick['total_quantity_to_pick']) !== 1 }">
                                            <small>to pick</small>
                                            <h3>{{ Number(this.pick['total_quantity_to_pick']) }}</h3>
                                        </div>
                                        <div class="col-4 text-center">
                                            <small>shelf</small>
                                            <h3>{{ pick['inventory_source_shelf_location'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide bg-warning rounded">
                    <div class="swipe-action-container swipe-action-container--left text-black-50 font-weight-bold">
                        <div>PARTIAL PICK</div>
                    </div>
                </div>

            </div>
        </div>


</template>

<script>
import Swiper from "swiper";

export default {
    name: "PickCard",

    props: {
        pick: Object,
    },

    data() {
        return {
            swiper: null,
        }
    },

    computed: {
        product_sku() {
            return  this.pick['product'] ? this.pick['product']['sku'] : '';
        },
    },

    mounted() {
        this.swiper = new Swiper('#' + this.$attrs.id, {
            initialSlide: 1,
            shortSwipes: false,
            longSwipes: true,
            longSwipesRatio: 0.30,
            longSwipesMs: 150,
            resistanceRatio: 0,
            speed: 150
        });

        this.swiper.on('transitionEnd', this.transitionEnd);
    },

    methods: {
        transitionEnd() {
            if (this.swiper.activeIndex === 1) {
                return;
            }

            if (this.swiper.activeIndex === 0) {
                this.$emit('swipeRight', this.pick);
            }

            if (this.swiper.activeIndex === 2) {
                this.$emit('swipeLeft', this.pick);
            }

            this.swiper.slideTo(1,0,false);
        },
    }

}
</script>

<style scoped>
    .col {
        background-color: #ffffff;
    }

    .header-row > div, .col {
        border: 1px solid #76777838;
    }

    .header-row > div {
        background-color: #76777838;
    }

    .swiper-container {
        overflow: hidden;
    }

    .swiper-slide {
        height: auto;
    }
</style>
