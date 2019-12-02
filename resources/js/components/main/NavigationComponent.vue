<template>
    <div>
        <v-navigation-drawer app clipped right v-model="cartDrawer" width="300">
            <v-list>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title class="subtitle-2 font-weight-bold">Selected Products</v-list-item-title>
                        <v-list-item-subtitle class="subtitle-1">Total Amount: Php {{cartTotal}}</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item three-line v-for="(product, index) in cartProducts" :key="index">
                    <v-list-item-content>
                        <v-list-item-title>
                            <span class="font-weight-bold">{{product.name}}</span>
                            <v-chip small class="px-2" color="primary">{{product.quantity}}</v-chip>
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            <v-row no-gutters>
                                <v-col cols=6>
                                    <span class="body-2 font-weight-medium">{{product.supplier}}</span>
                                </v-col>
                                <v-col cols=6>
                                    <span class="body-2 font-weight-medium">Php {{product.price}}</span>
                                </v-col>
                            </v-row>
                        </v-list-item-subtitle>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-btn @click="removeCartProduct(product)" small icon color="error">
                            <v-icon small>fa-times</v-icon>
                        </v-btn>
                    </v-list-item-action>
                </v-list-item>
            </v-list>
            <template v-slot:append>
                <v-container>
                    <v-btn @click="confirmationDialog = true" block color="primary">Send Requests</v-btn>
                    <v-dialog scrollable v-model="confirmationDialog" max-width="750px" persistent>
                        <v-overlay :value="loading">
                            <v-progress-circular :size="100" :width="5" color="primary" indeterminate></v-progress-circular>
                        </v-overlay>
                        <v-card>
                            <v-card-title primary-title>
                                Order Request Confirmation
                            </v-card-title>
                            <v-card-text>
                                <v-row>
                                    <v-col cols=12 md=4 v-for="(product, index) in cartProducts" :key="index">
                                        <v-list-item three-line>
                                            <v-list-item-content>
                                                <v-list-item-title>
                                                    <span class="font-weight-bold">{{product.name}}</span>
                                                    <v-chip small class="px-2" color="primary">{{product.quantity}}</v-chip>
                                                </v-list-item-title>
                                                <v-list-item-subtitle>
                                                    <v-row no-gutters>
                                                        <v-col cols=6>
                                                            <span class="body-2 font-weight-medium">{{product.supplier}}</span>
                                                        </v-col>
                                                        <v-col cols=6>
                                                            <span class="body-2 font-weight-medium">Php {{product.price}}</span>
                                                        </v-col>
                                                    </v-row>
                                                </v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                            <v-card-actions>
                                <div class="flex-grow-1" />
                                <v-btn @click="confirmationDialog = false">Cancel</v-btn>
                                <v-btn class="px-8" color="primary" @click="submitOrderRequest()">Confirm Order Requests</v-btn>
                            </v-card-actions>
                        </v-card>
                        <v-dialog v-model="orderRequestCodeDialog" max-width="400px">
                            <v-card tile>
                                <v-card-title>Order Request Reference Codes</v-card-title>
                                <v-card-text>
                                    <v-row align="center" justify="center">
                                        <v-col cols=12 md=6 v-for="(code, index) in orderRequestCodes" :key="index">
                                            <v-chip class="font-weight-medium pa-5" color="primary">{{ code }}</v-chip>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                                <v-card-actions>
                                <div class="flex-grow-1" />
                                <v-btn @click="closeDialog()">Close</v-btn>
                            </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-dialog>
                </v-container>
            </template>
        </v-navigation-drawer>

        <v-app-bar app clipped-right>
            <v-app-bar-nav-icon @click.stop="sideNavigationBar = !sideNavigationBar"></v-app-bar-nav-icon>
            <v-toolbar-title class="username-letter-spacing">
                <span class="title font-weight-bold title-letter-spacing">SMS ON</span>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon>
                <v-icon>fa-user-circle</v-icon>
            </v-btn>
            <v-btn icon>
                <v-icon>fa-bell</v-icon>
            </v-btn>
            <v-btn icon @click.stop="cartDrawer = !cartDrawer" v-if="userRole == 'Customer'">
                <v-icon>fa-shopping-cart</v-icon>
            </v-btn>
        </v-app-bar>

        <v-navigation-drawer app v-model="sideNavigationBar" width="250">
            <v-list>
                <!-- Administrator -->
                <template v-if="userRole == 'Administrator'">
                    <v-list-item to="/customers">
                        <v-list-item-avatar>
                            <v-icon>fa-users</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Customers</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item to="/suppliers">
                        <v-list-item-avatar>
                            <v-icon>fa-boxes</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Suppliers</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
                <!-- Customer -->
                <template v-if="userRole == 'Customer'">
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-list</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Order Requests</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item to="/products">
                        <v-list-item-avatar>
                            <v-icon>fa-box</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Products</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-boxes</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Suppliers</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
                <!-- Supplier -->
                <template v-if="userRole == 'Supplier'">
                    <v-list-item >
                        <v-list-item-avatar>
                            <v-icon>fa-list</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Order Requests</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item to="/supplier-products">
                        <v-list-item-avatar>
                            <v-icon>fa-box</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Products</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item to="/logistics">
                        <v-list-item-avatar>
                            <v-icon>fa-truck</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Logistics</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-clipboard-list</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Manifest</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
            </v-list>
            <template v-slot:append>
                <v-list>
                    <v-list-item @click="logout()">
                        <v-list-item-avatar>
                            <v-icon>fa-sign-out-alt</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Logout</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </template>
        </v-navigation-drawer>

        <v-content>
            <v-container>
                <router-view name="content" />
            </v-container>
        </v-content>
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
    export default {
        data() {
            return {
                userRole: sessionStorage.getItem('user-role'),
                sideNavigationBar: true, cartDrawer: false, confirmationDialog: false, orderRequestCodeDialog: false, loading: false,
                orderRequestCodes: [],
            }
        },

        computed: {
            ...mapGetters(['cartProducts', 'cartTotal'])
        },

        methods: {
            ...mapActions(['removeCartProduct', 'resetCart']),

            closeDialog() {
                this.orderRequestCodeDialog = false
                this.confirmationDialog = false
            },

            submitOrderRequest() {
                if(this.cartProducts.length > 0) {
                    this.loading = true
                    axios.post('/api/order-request', {
                        ...this.cartProducts
                    })
                    .then( response => {
                        this.orderRequestCodes = response.data.success.order_request_codes
                        this.resetCart()
                        this.cartDrawer = false
                        this.orderRequestCodeDialog = true
                    })
                    .catch( error => {
                        console.error(error.response.data)
                    })
                    .finally(() => {
                        this.loading = false
                    })
                }
            },

            logout() {
                axios.get('api/logout')
                .then(() => {})
                .catch(() => {})
                .finally(() => {
                    sessionStorage.removeItem('user-token')
                    sessionStorage.removeItem('user-role')
                    sessionStorage.removeItem('user-information-id')
                    sessionStorage.clear()
                    this.$router.push('/login')
                    toastr.success("Logout Successfull")
                })
            },
        }
    }
</script>