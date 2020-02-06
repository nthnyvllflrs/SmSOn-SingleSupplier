<template>
    <div>
        <v-navigation-drawer app clipped right v-model="cartDrawer" width="300" v-if="userRole == 'Customer'">
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
                                <v-card-title>Order Request Successful</v-card-title>
                                <v-card-text>
                                    <v-row>
                                        <v-col cols=12>Order Request(s) Reference Code(s)</v-col>
                                        <v-col cols=12 md=6>
                                            <v-chip class="font-weight-medium" color="primary">{{ orderRequestCode }}</v-chip>
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
            <v-btn icon @click="profileDialog = !profileDialog" v-if="userRole == 'Supplier' && userProfile.image_url">
                <img :src="userProfile.image_url" width="25px" height="25px" style="border-radius: 50%;"/>
            </v-btn>
            <v-btn icon @click="profileDialog = !profileDialog" v-if="userRole != 'Supplier' || !userProfile.image_url">
                <v-icon>fa-user-circle</v-icon>
            </v-btn>
            <v-btn icon @click="openNotificationDialog()">
                <v-icon>fa-bell</v-icon>
            </v-btn>
            <v-btn icon @click.stop="cartDrawer = !cartDrawer" v-if="userRole == 'Customer'">
                <v-icon>fa-shopping-cart</v-icon>
            </v-btn>
        </v-app-bar>

        <v-navigation-drawer app v-model="sideNavigationBar" width="250">
            <v-list>
                    <v-list-item to="/customers" v-if="userPermission('customers')">
                        <v-list-item-avatar>
                            <v-icon>fa-users</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Customers</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <!-- <v-list-item to="/suppliers" v-if="userPermission('suppliers')">
                        <v-list-item-avatar>
                            <v-icon>fa-boxes</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Suppliers</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item> -->
                    <v-list-item to="/order-requests" v-if="userPermission('order_requests')">
                        <v-list-item-avatar>
                            <v-icon>fa-list</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Order Requests</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item to="/products" v-if="userPermission('products')">
                        <v-list-item-avatar>
                            <v-icon>fa-box</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Products</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item to="/logistics" v-if="userPermission('logistics')">
                        <v-list-item-avatar>
                            <v-icon>fa-truck</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Logistics</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item to="/manifests" v-if="userPermission('manifests')">
                        <v-list-item-avatar>
                            <v-icon>fa-clipboard-list</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Manifest</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
            </v-list>
            <template v-slot:append>
                <v-list>
                    <v-list-item to="/logs" v-if="userPermission('logs')">
                        <v-list-item-avatar>
                            <v-icon>fa-cog</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Logs</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
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

        <v-dialog v-model="profileDialog" max-width="600" persistent>
            <v-card>
                <v-overlay :value="loading">
                    <v-progress-circular :size="100" :width="5" color="light-green accent-4" indeterminate></v-progress-circular>
                </v-overlay>
                <v-card-title class="headline">Profile Information</v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols=12>
                            <v-file-input :error-messages="formErrors.image" v-model="photoData" prepend-icon="fa-camera" label="Supplier Logo" accept="image/png, image/jpeg, image/bmp" @change="fileConvert()" messages="Uploading a new image will overwrite the existing image." v-if="userRole == 'Supplier'"/>
                        </v-col>
                        <v-col cols=12 md=6>
                            <v-text-field :error-messages="formErrors.code" v-model="userProfile.code" label="User Code" readonly />
                        </v-col>
                        <v-col cols=12 md=6>
                            <v-text-field :error-messages="formErrors.username" v-model="userProfile.username" label="Username" />
                        </v-col>
                        <v-col cols=12 md=6>
                            <v-text-field :error-messages="formErrors.password" v-model="userProfile.password" label="Password" type="password" />
                        </v-col>
                        <v-col cols=12 md=6>
                            <v-text-field :error-messages="formErrors.password_confirmation" v-model="userProfile.password_confirmation" label="Password Confirmation" type="password" />
                        </v-col>
                        
                        <v-col cols=12 md=6 v-if="userRole == 'Customer' || userRole == 'Supplier' || userRole == 'Logistic'">
                            <v-text-field :error-messages="formErrors.name" v-model="userProfile.name" label="Fullname" />
                        </v-col>
                        <v-col cols=12 md=6 v-if="userRole == 'Customer' || userRole == 'Supplier' || userRole == 'Logistic'">
                            <v-text-field :error-messages="formErrors.contact_number" v-model="userProfile.contact_number" label="Phone Number" />
                        </v-col>
                        <v-col cols=12 v-if="userRole == 'Customer' || userRole == 'Supplier'">
                            <v-textarea :error-messages="formErrors.address" v-model="userProfile.address" label="Address" />
                        </v-col>
                        
                        <v-col cols=12 v-if="userRole == 'Supplier'">
                            <v-textarea :error-messages="formErrors.description" v-model="userProfile.description" label="Description" />
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="closeDialog()">Cancel</v-btn>
                    <v-btn class="px-8" color="primary" @click="updateUserProfile()">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="notificationDialog" max-width="600">
            <v-card>
                <v-overlay :value="loading">
                    <v-progress-circular :size="100" :width="5" color="light-green accent-4" indeterminate></v-progress-circular>
                </v-overlay>
                <v-card-title class="headline">Notifications</v-card-title>
                <v-card-text>
                    <v-list-item two-line v-for="notification in userNotifications" :key="notification.id">
                        <v-list-item-content>
                            <v-list-item-title>
                                <template v-if="userRole == 'Administrator'">
                                    {{ notification.data.code }}
                                </template>
                                <template v-if="userRole != 'Administrator'">
                                    {{ notification.data.status }}
                                </template>
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                <template v-if="userRole == 'Administrator'">
                                    U: {{ notification.data.username }} - N: {{ notification.data.name }} - R: {{ notification.data.role }}
                                </template>
                                <template v-if="userRole != 'Administrator'">
                                    Code: {{ notification.data.code }}
                                </template>
                            </v-list-item-subtitle>
                            <v-list-item-subtitle>@ {{ notification.created_at }}</v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-action>

                            <!-- Administrator Notifications -->
                            <v-chip small v-if="notification.type == 'App\\Notifications\\UserCreationNotification'" color="success">
                                {{ notification.type|substr(18) }}
                            </v-chip>

                            <v-chip small v-if="notification.type == 'App\\Notifications\\UserDeletionNotification'" color="error">
                                {{ notification.type|substr(18) }}
                            </v-chip>

                            <!-- Customer Notifications -->
                            <v-chip small v-if="notification.type == 'App\\Notifications\\OrderRequestStatusNotification'" color="info">
                                {{ notification.type|substr(18) }}
                            </v-chip>
                            
                            <!-- Supplier Notifications -->
                            <v-chip small v-if="notification.type == 'App\\Notifications\\OrderRequestCreationNotification'" color="success">
                                {{ notification.type|substr(18) }}
                            </v-chip>

                        </v-list-item-action>
                    </v-list-item>
                </v-card-text>
            </v-card>
        </v-dialog>

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
                userId: sessionStorage.getItem('user-id'),
                userInformationId: sessionStorage.getItem('user-information-id'),
                sideNavigationBar: true, cartDrawer: false, profileDialog: false,
                confirmationDialog: false, orderRequestCodeDialog: false, notificationDialog: false, loading: false,
                orderRequestCode: null, userNotifications: [],

                userProfile: {
                    code: null, username: null, password: null, password_confirmation: null,
                    name: null, address: null, contact_number: null,
                    description: null, image: null, image_url: null
                },

                formErrors: {
                    code: null, username: null, password: null, password_confirmation: null,
                    name: null, address: null, contact_number: null,
                    description: null, image: null, image_url: null
                },

                photoData: null, photoByteData: null,
            }
        },

        computed: {
            ...mapGetters(['cartProducts', 'cartTotal'])
        },

        mounted() {
            this.retrieveUserProfile()

            Echo.channel('order-request')
                .listen('OrderRequest', (data) => {
                    if(data.order_request.user_id == sessionStorage.getItem('user-id')) {
                        if(data.order_request.type == 'Created') {
                            toastr.info("New Order Request", data.order_request.code)
                        } else if(data.order_request.type == 'Deleted') {
                            toastr.info("Deleted Order Request". data.order_request.code)
                        }
                    }
                })
                .listen('OrderRequestStatus', (data) => {
                    if(data.order_request.user_id == sessionStorage.getItem('user-id')) {
                        toastr.info(data.order_request.status, data.order_request.code)
                    }
                })
        },

        methods: {
            ...mapActions(['removeCartProduct', 'resetCart']),

            retreiveUserNotification() {
                this.loading = true
                axios.get('/api/notifications')
                .then( response => {
                    this.userNotifications = response.data
                })
                .catch( error => {
                    toastr.error("An Error Occurred")
                })
                .finally(() => {
                    this.loading = false
                })
            },

            retrieveUserProfile() {
                axios.get('/api/' + this.userRole.toLowerCase() + '/' +  this.userId)
                .then( response => {
                    if(this.userRole == 'Supplier') {
                        this.userProfile = response.data.success.supplier.profile
                    } else {
                        this.userProfile = response.data.success.profile
                    }
                })
                .catch( error => {
                    console.log(error.response.data)
                })
            },

            updateUserProfile() {
                this.loading = true
                axios.put('/api/' + this.userRole.toLowerCase() + '/' +  this.userId, {
                    ...this.userProfile,
                    image: this.photoByteData
                })
                .then( response => {
                    this.profileDialog = false
                    this.retrieveUserProfile()
                    toastr.success("Profile Updated")
                })
                .catch( error => {
                    if(error.response.status == 422) {
                        if(typeof error.response.data == 'object'){
                            this.formErrors = error.response.data.errors
                        } else {
                            this.errorMessage = error.response.data
                        }
                    } else {
                        toastr.error("An Error Occurred")
                    }
                })
                .finally(() => { this.loading = false})
            },

            openNotificationDialog() {
                this.notificationDialog = true
                this.retreiveUserNotification()
            },

            closeDialog() {
                this.orderRequestCodeDialog = false
                this.confirmationDialog = false
                this.profileDialog = false
                this.retrieveUserProfile()
            },

            submitOrderRequest() {
                if(this.cartProducts.length > 0) {
                    this.loading = true
                    axios.post('/api/order-request', {
                        ...this.cartProducts
                    })
                    .then( response => {
                        this.orderRequestCode = response.data.success.order_request_code
                        this.resetCart()
                        this.cartDrawer = false
                        this.orderRequestCodeDialog = true
                    })
                    .catch( error => {
                        console.log(error.response.data)
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

                    if(window.locationInterval != undefined && window.locationInterval != 'undefined'){
                        window.clearInterval(window.locationInterval);
                    }
                })
            },
            
            userPermission(module) {
                var modules = {
                    customers: true, suppliers: true, order_requests: true, products: true, logistics: true, manifests: true, logs: true
                }
                var permissions = {
                    Administrator: {
                        ...modules,
                        // order_requests: false, products: false, logistics: false, manifests: false
                    },
                    Customer: {
                        ...modules,
                        customers: false, logistics: false, manifests: false, logs: false
                    },
                    Supplier: {
                        ...modules,
                        customers: false, suppliers: false, logs: false
                    },
                    Logistic: {
                        ...modules,
                        customers: false, suppliers: false, order_requests: false, products: false, logistics: false, logs: false
                    }
                }
                return permissions[this.userRole][module]
            },

            fileConvert() {
                try {
                    var reader = new FileReader()
                    reader.onload = () => {
                        this.photoByteData = reader.result
                    }
                    reader.readAsDataURL(this.photoData)
                } catch(e) {
                    toastr.error("File Converter Error")
                }
            }
        }
    }
</script>