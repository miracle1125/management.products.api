<template>

  <div class="swiper-container mb-1">
    <div class="swiper-wrapper">

<!--      <div class="swiper-slide error bg-success text-right">-->
<!--        <div class="swipe-action-container swipe-action-container&#45;&#45;right">-->
<!--          <div>PICK ALL</div>-->
<!--        </div>-->
<!--      </div>-->

      <div class="swiper-slide">
          <div class="row ml-1 mr-1 card">
              <div class="col p-2 pl-2 rounded">
                <div class="row">

                    <div class="col-5 col-md-4 col-lg-3">
                        <h5 class="text-primary"><a :href="'/orders/?search=' + order['order_number']"> #{{ order['order_number'] }} </a> </h5>
                        <div class="small"> <b> {{ order['status_code'] }} </b> </div>
                    </div>

                    <div class="col text-center" @click="toggleOrderDetails">
                        <div class="row">
                            <div class="col">
                                <small> age </small>
                                <h5> {{ order['age_in_days'] }}</h5>
                            </div>
                            <div class="col">
                                <small> products </small>
                                <h5> {{ order['product_line_count'] }} </h5>
                            </div>
                            <div class="col">
                                <small> quantity </small>
                                <h5> {{ order['total_quantity_ordered'] }} </h5>
                            </div>
                            <div class="col d-none d-sm-block">
                                <small> total </small>
                                <h5>{{ order['total'] }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 small" v-if="order['order_comments'].length > 0 && orderDetailsVisible === false">
                        <b>{{ order['order_comments'][0]['user']['name'] }}: </b>{{ order['order_comments'][0]['comment'] }}
                    </div>

                </div>


                <div class="row text-center text-secondary" v-if="!orderDetailsVisible" @click="toggleOrderDetails">
                    <div class="col">
                        <font-awesome-icon icon="chevron-down" class="fa fa-xs"></font-awesome-icon>
                    </div>
                </div>

                <div v-if="orderDetailsVisible">

                    <div class="row text-center" @click="toggleOrderDetails">
                        <div class="col">
                            <font-awesome-icon icon="chevron-up" class="fa fa-xs text-secondary"></font-awesome-icon>
                        </div>
                    </div>

                    <div class="row mb-2 mt-1">
                        <input ref="newCommentInput" v-model="input_comment" class="form-control" placeholder="Add comment here" @keypress.enter="addComment"/>
                    </div>

                    <template v-for="order_comment in order['order_comments']">
                        <div class="row mb-2">
                            <div class="col">
                                <b>{{ order_comment['user']['name'] }}: </b>{{ order_comment['comment'] }}
                            </div>
                        </div>
                    </template>
                    <div class="row tabs-container mb-2">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active p-0 pl-2 pr-2" data-toggle="tab" href="#" @click.prevent="currentTab = 'productsOrdered'" >
                                    Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link p-0 pl-2 pr-2" data-toggle="tab" href="#" @click.prevent="currentTab = 'orderDetails'" >
                                    Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link p-0 pl-2 pr-2" data-toggle="tab" href="#" @click.prevent="currentTab = 'orderActivities'" >
                                    Activity
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link p-0 pl-2 pr-2" target="_blank" :href="'/order/packsheet/' + order['order_number']">
                                    Packsheet
                                </a>
                            </li>
                            <li class="nav-item">
                                <a v-if="sharingAvailable()" class="nav-link p-0 pl-2 pr-2" @click.prevent="shareLink" href="#">
                                    <font-awesome-icon icon="share-alt" class="fas fa-sm"></font-awesome-icon>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="container" v-if="currentTab === 'productsOrdered'">
                        <template v-for="order_product in order_products">

                            <div class="row text-left mb-2">
                                <div class="col-md-4">
                                    <small>{{ order_product['name_ordered'] }} &nbsp;</small>
                                    <div class="small"><a target="_blank" :href="getProductLink(order_product)">{{ order_product['sku_ordered'] }}</a>&nbsp;</div>
                                </div>
                                <div class="col">
                                    <div class="row text-center">
                                        <div class="col">
                                            <small> ordered </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_ordered']) }}</h4>
                                        </div>
                                        <div class="col">
                                            <small> picked </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_picked']) }}</h4>
                                        </div>
                                        <div class="col" v-bind:class="{ 'bg-warning': Number(order_product['quantity_skipped_picking']) > 0 }">
                                            <small> skipped </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_skipped_picking']) }}</h4>
                                        </div>
                                        <div class="col d-none d-sm-block">
                                            <small> shipped </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_shipped'])  }}</h4>
                                        </div>
                                        <div class="col">
                                            <small> to ship </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_to_ship'])  }}</h4>
                                        </div>
                                        <div class="col" v-bind:class="{ 'bg-warning': ifHasEnoughStock(order_product) }">
                                            <small> inventory </small>
                                            <h4>{{ toNumberOrDash(getProductQuantity(order_product)) }}</h4>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <hr>
                        </template>
                    </div>

                    <div class="container" v-if="currentTab === 'orderDetails'">
                        <div class="row small">
                            <div class="col-4">
                                <div> date: <b> {{ order['order_placed_at'] | moment('MM/DD H:mm') }} </b> </div>
                                <div> paid: <b> {{ order['total_paid'] }} </b> </div>
                            </div>
                            <div class="col">
                                <div> picked at: <b> {{ order['picked_at'] | moment('MM/DD H:mm') }} </b> </div>
                                <div> packed at: <b> {{ order['packed_at'] | moment('MM/DD H:mm') }} </b> </div>
                                <div> packed by: <b> {{ order['packer'] ? order['packer']['name'] : '&nbsp' }} </b> </div>
                            </div>
                        </div>

                        <div class="row">
                          <div class="col-12">
                              Shipping Numbers:
                              <table>
                                  <template v-for="shipment in order_shipments">
                                      <tr>
                                          <td>
                                              {{ shipment['created_at'] | moment('MMM DD')  }} <small>@</small> {{ shipment['created_at'] | moment('H:mm')  }}:
                                          </td>
                                          <td>{{ shipment['shipping_number'] }}</td>
                                          <td>shipped by {{ shipment['user']['name'] }}</td>
                                      </tr>
                                  </template>
                              </table>
                          </div>
                        </div>
                    </div>

                    <div class="container" v-if="currentTab === 'orderActivities'">
                        <template v-for="activity in order_activities">
                            <div class="d-flex flex-column flex-md-row align-middle">
                                <div class="d-none d-md-block align-middle">
                                    {{ activity['created_at'] | moment('MMM DD')  }} <small>@</small> {{ activity['created_at'] | moment('H:mm')  }}:
                                </div>
                                <div class="small flex-row d-block d-md-none align-middle">
                                    {{ activity['created_at'] | moment('MMM DD')  }}
                                    {{ activity['created_at'] | moment('H:mm')  }}:
                                </div>
                                <div class="pl-sm-0 pl-md-1">
                                    <b>
                                        {{ activity['causer'] === null ? 'AutoStatus' : activity['causer']['name'] }}
                                    </b>
                                    {{ activity['description'] }} {{ activity['changes'] }}
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
              </div>
            </div>
          <div id="spacer-bottom" class="row mb-4 mt-4" v-if="orderDetailsVisible"></div>
      </div>
    </div>

<!--    <div class="swiper-slide bg-warning">-->
<!--    <div class="swipe-action-container swipe-action-container&#45;&#45;left text-black-50 font-weight-bold">-->
<!--      <div>PARTIAL PICK</div>-->
<!--    </div>-->
<!--    </div>-->

</div>

</template>

<script>
    import api from "../../mixins/api";
    import helpers from "../../mixins/helpers";
    import url from "../../mixins/url";

    export default {
        mixins: [api, helpers, url],
        name: "OrderCard",

        props: {
            order: Object,
            expanded: false,
        },


        data: function () {
            return {
                input_comment: '',
                orderDetailsVisible: false,

                currentTab: 'productsOrdered',

                order_products: [],
                order_comments: null,
                order_activities: null,
                order_shipments: null,
            }
        },

        mounted() {
            // we pre creating array of empty products so UI will display 4 empty rows
            // its simply more pleasant to eye and card doesnt "jump"
            for (let i = 0; i < this.order['product_line_count']; i++)
                this.order_products.unshift([]);
        },

        created: function () {
            if (this.expanded) {
                this.toggleOrderDetails();
            }
        },

        watch: {
            expanded() {
                if (this.expanded) {
                    this.toggleOrderDetails();
                }
            },
        },

        methods: {
            sharingAvailable() {
                return navigator.share;
            },

            shareLink() {
                navigator.share({
                    url: '/orders?search=' + this.order['order_number'],
                    title: document.title
                });
            },

            toggleOrderDetails() {
                if (this.orderDetailsVisible) {
                    this.orderDetailsVisible = false;
                    return;
                }

                this.loadOrderProducts()
                this.loadOrderActivities();
                this.loadOrderShipments();

                this.orderDetailsVisible = true;
            },

            loadOrderProducts() {
                let params = {
                    'filter[inventory_source_location_id]': this.getUrlParameter('inventory_source_location_id'),
                    'filter[order_id]': this.order['id'],
                    'include': 'product',
                    'per_page': '999',
                };

                this.apiGetOrderProducts(params)
                    .then(({data}) => {
                        this.order_products = data.data;
                    })

                return this;
            },

            loadOrderComments() {
                if (this.order_comments) {
                    return this;
                }

                let params = {
                    'filter[order_id]': this.order['id'],
                    'include': 'user',
                    'sort': '-id',
                    'per_page': '999',
                };

                this.apiGetOrderComments(params)
                    .then(({data}) => {
                        this.order_comments = data.data;
                    })

                return this;
            },

            loadOrderActivities() {
                if (this.order_activities) {
                    return this;
                }

                let params = {
                    'filter[subject_id]': this.order['id'],
                    'filter[subject_type]': 'App\\Models\\Order',
                    'include': 'causer',
                    'sort': '-id',
                    'per_page': '999',
                };

                this.apiGetActivityLog(params)
                    .then(({data}) => {
                        this.order_activities = data.data;
                    })

                return this;
            },

            loadOrderShipments() {
                if (this.order_shipments) {
                    return this;
                }

                let params = {
                    'filter[order_id]': this.order['id'],
                    'include': 'user',
                    'sort': '-id',
                    'per_page': '999',
                };

                this.apiGetOrderShipments(params)
                    .then(({data}) => {
                        this.order_shipments = data.data;
                    })

                return this;
            },

            hasSkippedPick(orderProduct) {
                return Number(orderProduct['quantity_skipped_picking']) > 0;
            },

            addComment() {
                let data = {
                    "order_id": this.order['id'],
                    "comment": this.input_comment
                };

                this.apiPostOrderComment(data)
                    .then(({data}) => {
                        // quick hack to immediately display comment
                        this.order['order_comments'].unshift(data['data'][0])
                        this.loadOrderComments();
                        this.input_comment = '';
                    })
            },

            getProductLink(orderProduct) {
                const searchTerm = orderProduct['product'] ? orderProduct['product']['sku'] : orderProduct['sku_ordered'];
                return '/products?search=' + searchTerm;
            },

            getProductQuantity(orderProduct) {
                if(this.getUrlParameter('inventory_source_location_id')) {
                    return orderProduct['inventory_source_quantity']
                }
                return orderProduct['product'] ? Number(orderProduct['product']['quantity']) : 0;
            },

            ifHasEnoughStock(orderProduct) {
                return this.getProductQuantity(orderProduct) < Number(orderProduct['quantity_to_ship']);
            }
        },
    }
</script>

<style scoped>

    .header-row > div, .col {
      /*border: 1px solid #76777838;*/
    }

    .col {
        background-color: #ffffff;
        /*border: 1px solid #76777838;*/
    }

    .nav-item {
        margin-right: unset;
    }
</style>
