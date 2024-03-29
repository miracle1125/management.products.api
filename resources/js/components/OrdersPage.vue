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

        <div class="row" v-if="orders.length === 0 && !isLoading">
            <div class="col">
                <div class="alert alert-info" role="alert">
                    No orders found.
                </div>
            </div>
        </div>

        <template v-for="order in orders">
            <div class="row">
                <div class="col">
                    <order-card :order="order" :expanded="orders.length === 1"/>
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
    import OrderCard from "./Orders/OrderCard";
    import url from "../mixins/url";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            'order-card': OrderCard,
            'barcode-input-field': BarcodeInputField,
        },

        data: function() {
            return {
                lastPageLoaded: 1,
                lastPage: 1,
                searchText: '',

                orders: [],
            };
        },

        mounted() {
            this.searchText = this.getUrlParameter('search');

            window.onscroll = () => this.loadMore();

            this.reloadOrders();
        },

        methods: {
            findText() {
                this.setUrlParameter('search', this.searchText);
                this.reloadOrders();
            },

            reloadOrders(e) {
                this.orders = [];
                this.loadOrderList();
                this.setFocus(this.$refs.search, true, true);
            },

            loadOrderList: function(page = 1) {
                this.showLoading();

                this.page = page;
                this.last_page = 1;

                const params = {
                    'filter[status]': this.getUrlParameter('status'),
                    'filter[search]': this.getUrlParameter('search'),
                    'filter[has_tags]': this.getUrlParameter('has_tags'),
                    'filter[without_tags]': this.getUrlParameter('without_tags'),
                    'filter[age_in_days]': this.getUrlParameter('age_in_days'),
                    'filter[is_active]': this.getUrlParameter('is_active'),
                    'filter[packed_between]': this.getUrlParameter('packed_between'),
                    'filter[packer_user_id]': this.getUrlParameter('packer_user_id'),
                    'sort': this.getUrlParameter('sort','-order_placed_at'),
                    'per_page': this.getUrlParameter('per_page', 20),
                    'include': 'order_comments,order_comments.user',
                    'page': page,
                };

                this.apiGetOrders(params)
                    .then(({ data }) => {
                        this.orders = this.orders.concat(data.data);
                        this.lastPage = data.meta.last_page
                        this.lastPageLoaded = page;
                    })
                    .finally(() => {
                        this.hideLoading();
                    })

                return this;
            },

            loadMore: function () {
                if (this.isMoreThanPercentageScrolled(70) && this.hasMorePagesToLoad() && !this.isLoading) {
                    this.loadOrderList(++this.lastPageLoaded);
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
