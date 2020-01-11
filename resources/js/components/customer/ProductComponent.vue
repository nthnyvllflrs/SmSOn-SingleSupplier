<template>
    <v-container>
        <v-card>
            <v-card-title>
                Products
                <v-spacer />
                <v-text-field
                    v-model="search"
                    append-icon="fa-search"
                    label="Search product"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table calculate-widths :loading=loading loading-text="Loading... Please wait" :headers="productTableHeaders" :items="products" :search="search">
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <v-toolbar-title class="headline">Products</v-toolbar-title>
                    </v-toolbar>
                </template>
                <template v-slot:item.price="{ item }">
                    Php {{ Number(item.price).toLocaleString() }}
                </template>
                <template v-slot:item.action="{ item }">
                    <v-btn @click="selectProduct(item)" x-small class="mx-2" fab dark color="primary">
                        <v-icon small dark>fa-plus</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
            <v-dialog v-model="quantityModal" max-width="300">
                <v-card>
                    <v-card-title primary-title>Product Quantity</v-card-title>
                    <v-card-text>
                        <v-row align="center" justify="center">
                            <v-col cols="9">
                                <v-text-field v-model="selectedProduct.quantity" type="number" autofocus/>
                                </v-col>
                            <v-col cols="3">
                                <v-btn @click="addCartItem()" x-small class="mx-2" fab dark color="primary">
                                    <v-icon small dark>fa-check</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </v-card>
    </v-container>
</template>

<script>
import {mapActions} from 'vuex'
    export default {
        data() {
            return {
                search: null, quantityModal: false, productQuantity: 0,
                selectedProduct: {}, loading: false,

                productTableHeaders: [
                    { text: 'Code', value: 'code' },
                    { text: 'Name', value: 'name' },
                    { text: 'Description', value: 'description' },
                    { text: 'Supplier', value: 'supplier' },
                    { text: 'Unit', value: 'unit' },
                    { text: 'Price', value: 'price' },
                    { text: 'Actions', value: 'action', align: 'center', sortable: false, width: 200 },
                ],

                products: []
            }
        },
        mounted() {
            this.retrieveProducts()
        },
        watch: {
            quantityModal (val) {
                val || (this.selectedProduct = {})
            },
        },
        methods: {
            ...mapActions(['addCartProduct']),

            retrieveProducts() {
                this.loading = true
                axios.get('/api/product')
                .then( response => {
                    this.products = response.data.success.products
                })
                .catch( error => {
                    toastr.error("Retrieve Products Error")
                })
                .finally(() => {
                    this.loading = false
                })
            },

            selectProduct(product) {
                this.selectedProduct = product
                this.quantityModal = true
            },

            addCartItem() {
                if(this.selectedProduct.quantity > 0) {
                    var product = Object.assign({}, this.selectedProduct)
                    this.addCartProduct(product)
                    toastr.success("Product Added")
                    this.quantityModal = false
                }
            }
        }
    }   
</script>