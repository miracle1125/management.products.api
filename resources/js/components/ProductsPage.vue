<template>
    <div>

        <div class="row no-gutters mb-3 ml-1 mr-1">
            <div class="col">
                <input placeholder="Search"
                       class="form-control"
                       ref="search"
                       v-model="searchText"
                       @keyup.enter="findText" />
            </div>
        </div>

        <div class="row" v-if="products.length === 0 && !isLoading" >
            <div class="col">
                <div class="alert alert-info" role="alert">
                    No products found.
                </div>
            </div>
        </div>

        <template v-for="product in products">
            <div class="row">
                <div class="col">
                    <product-card :product="product" :expanded="products.length === 1"/>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
            </div>
        </div>

    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import ProductCard from "./Products/ProductCard";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import url from "../mixins/url";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            ProductCard,
            BarcodeInputField
        },

        data: function() {
            return {
                lastPageLoaded: 1,
                lastPage: 1,
                searchText: '',

                products: [],
            };
        },

        mounted() {
            this.searchText = this.getUrlParameter('search');

            window.onscroll = () => this.loadMore();

            this.loadProductList(1);
        },

        methods: {
            findText() {
                this.setUrlParameter('search', this.searchText);
                this.reloadProducts();
                this.setFocus(this.$refs.search, true, true)
            },

            reloadProducts() {
                this.products = [];
                this.loadProductList();
            },

            loadProductList: function(page = 1) {
                this.showLoading();

                const params = {
                    'filter[sku]': this.getUrlParameter('sku'),
                    'filter[search]': this.getUrlParameter('search'),
                    'filter[has_tags]': this.getUrlParameter('has_tags'),
                    'filter[without_tags]': this.getUrlParameter('without_tags'),
                    'sort': this.getUrlParameter('sort', '-quantity'),
                    'include': 'inventory,tags',
                    'per_page': this.getUrlParameter('per_page', 25),
                    'page': page
                }

                this.apiGetProducts(params)
                    .then(({data}) => {
                        if (page === 1) {
                            this.products = [];
                        }
                        this.products = this.products.concat(data.data);
                        this.lastPage = data.last_page;
                        this.lastPageLoaded = page;
                    })
                    .finally(() => {
                        this.hideLoading();
                    });
                this.setFocus(this.$refs.search, true, true);
                return this;
            },

            loadMore: function () {
                if (this.isMoreThanPercentageScrolled(70) && this.hasMorePagesToLoad() && !this.isLoading) {
                    this.loadProductList(++this.lastPageLoaded);
                }
            },

            hasMorePagesToLoad: function () {
                return this.lastPage > this.lastPageLoaded;
            },
        },
    }
</script>

<style lang="scss" scoped>
    .row {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
