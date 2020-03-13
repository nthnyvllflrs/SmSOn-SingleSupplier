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
            <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="productTableHeaders" :items="products" :search="search">
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <!-- <v-toolbar-title class="headline">Products</v-toolbar-title> -->
                        <div class="flex-grow-1"></div>
                        <v-dialog v-model="dialog" max-width="500px" persistent>
                            <template v-slot:activator="{ on }">
                                <v-btn small color="primary" v-on="on">
                                    <v-icon small left>fa-plus</v-icon> Add Product
                                </v-btn>
                            </template>

                            <v-card>
                                <v-overlay :value="loading">
                                    <v-progress-circular :size="100" :width="5" color="primary" indeterminate></v-progress-circular>
                                </v-overlay>
                                <v-card-title>
                                    <span class="headline">{{ formTitle }}</span>
                                </v-card-title>
                                <v-card-text>
                                    <v-row no-gutters>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.code" v-model="editedProductInformation.code" label="Code" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.name" v-model="editedProductInformation.name" label="Name" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-textarea :error-messages="formErrors.description" v-model="editedProductInformation.description" label="Description" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.unit" v-model="editedProductInformation.unit" label="Unit" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.price" v-model="editedProductInformation.price" label="Price" />
                                        </v-col>
                                    </v-row>
                                </v-card-text>

                                <v-card-actions>
                                    <div class="flex-grow-1" />
                                    <v-btn @click="cancel()">Cancel</v-btn>
                                    <v-btn class="px-8" color="primary" @click="saveProduct()">Save</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>

                <template v-slot:item.stockStatus="{ item }">
                    <v-chip class="ma-2" color="red" text-color="white" v-if="item.stockStatus == 'Critical'">Critical</v-chip>
                    <v-chip class="ma-2" color="green" text-color="white" v-if="item.stockStatus == 'Good'">Good</v-chip>
                </template>

                <template v-slot:item.action="{ item }">
                    <v-icon class="mx-1" @click="openStockDialog(item)">fa-boxes</v-icon>
                    <v-icon class="mx-1" @click="editProduct(item)">fa-pen</v-icon>
                    <v-icon class="mx-1" @click="deleteProduct(item)">fa-trash-alt</v-icon>
                </template>
            </v-data-table>
        </v-card>

        <v-dialog v-model="stockDialog" max-width="500px">
            <v-card>
                <v-card-title>Available Stock</v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols=12 md=6 align="center" justify="center">
                            <v-text-field type="number" label="Stock(s) Threshold" v-model="productStock.threshold"/>
                        </v-col>
                        <v-col cols=12 md=6 align="center" justify="center">
                            <v-text-field type="number" label="Available Stock(s)" v-model="productStock.available"/>
                        </v-col>
                        <v-col cols=12 md=4 align="center" justify="center">
                            <v-text-field label="Pending Stock(s)" v-model="productStock.pending" readonly/>
                        </v-col>
                        <v-col cols=12 md=4 align="center" justify="center">
                            <v-text-field label="Approved Stock(s)" v-model="productStock.approved" readonly/>
                        </v-col>
                        <v-col cols=12 md=4 align="center" justify="center">
                            <v-text-field label="Delivered Stock(s)" v-model="productStock.delivered" readonly/>
                        </v-col>
                        <v-col cols=12 align="center" justify="center">
                            <!-- <v-btn color="primary" dark small fab @click="updateStock()" :loading="loading"> -->
                            <v-btn class="full-width" color="primary" dark @click="updateStock()" :loading="loading">
                                Update Product Stock
                                <!-- <v-icon dark small>fa-check</v-icon> -->
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                supplierID: sessionStorage.getItem('user-information-id'),

                dialog: false, informationDialog: false, stockDialog: false,
                loading: false, search: '', editedIndex: -1,
                productTableHeaders: [
                    { text: 'Code', value: 'code' },
                    { text: 'Name', value: 'name' },
                    { text: 'Description', value: 'description' },
                    { text: 'Unit', value: 'unit' },
                    { text: 'Price', value: 'price' },
                    { text: 'Stock Status', value: 'stockStatus' },
                    { text: 'Actions', value: 'action', sortable: false },
                ],
                products: [],

                defaultProductInformation: { code: null, name: null, description: null, unit: null, price: null, supplier_id: sessionStorage.getItem('user-information-id')},
                editedProductInformation: { code: null, name: null, description: null, unit: null, price: null, supplier_id: sessionStorage.getItem('user-information-id')},
                formErrors: { code: null, name: null, description: null, unit: null, price: null,},

                productStock: { available: 0, threshold: 0, pending: 0, approved: 0, delivered: 0, }
            }
        },
        mounted() {
            this.retrieveProducts()
        },
        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'New Product' : 'Edit Product'
            },
        },
        methods: {
            openStockDialog(product) {
                this.stockDialog = true
                // this.productStock = product.stock.available
                this.productStock = product.stock
                this.editedIndex = this.products.indexOf(product)
                this.editedProductInformation = Object.assign({}, product)
            },

            retrieveProducts() {
                this.loading = true
                axios.get('/api/product')
                .then( response => {
                    this.products = response.data.success.products
                })
                .catch( error => {
                    toastr.error("An Error Occurred")
                })
                .finally(() => {
                    this.loading = false
                })
            },

            deleteProduct(product) {
                var productDeletion = confirm('Are you sure you want to delete this Product?')
                if(productDeletion == true) {
                    axios.delete('api/product/' + product.id)
                    .then( response => {
                        const index = this.products.indexOf(product)
                        this.products.splice(index, 1)
                        toastr.success("Product Deleted")
                    })
                    .catch( error => { alert(error)})
                }
            },

            editProduct(product) {
                this.editedIndex = this.products.indexOf(product)
                this.editedProductInformation = Object.assign({}, product)
                this.dialog = true
            },
            
            cancel() {
                this.dialog = false; this.informationDialog = false; this.stockDialog = false;
                setTimeout(() => {
                    this.formErrors = { username: null, name: null, address: null, contact_number: null, password: null, password_confirmation: null}
                    this.editedProductInformation = Object.assign({}, this.defaultProductInformation)
                    this.editedIndex = -1
                }, 500)
            },

            saveProduct() {
                if (this.editedIndex > -1) {
                    this.updateProduct()
                } else {
                    this.createProduct()
                }
            },

            createProduct() {
                this.loading = true
                
                axios.post('/api/product', {
                    ...this.editedProductInformation
                })
                .then( response => {
                    this.editedProductInformation.product_id = response.data.success.product.id
                    this.editedProductInformation.stockStatus = 'Critical'
                    this.editedProductInformation.stock = response.data.success.product.stock
                    this.products.push(this.editedProductInformation)
                    this.cancel()
                    toastr.success("Product Created")
                    console.log(this.editedProductInformation.product_id)
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
                .finally( x => { this.loading = false})
            },

            updateProduct() {
                this.loading = true
                _.omit(this.editedProductInformation, 'code')
                axios.put('/api/product/' + this.editedProductInformation.product_id, {
                    ...this.editedProductInformation
                })
                .then( response => {
                    Object.assign(this.products[this.editedIndex], this.editedProductInformation)
                    this.cancel()
                    toastr.success("Product Updated")
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
                .finally( x => { this.loading = false})
            },

            updateStock() {
                this.loading = true
                axios.put('/api/product/' + this.editedProductInformation.product_id + '/stock', {
                    // available: this.productStock
                    ...this.productStock
                })
                .then( response => {
                    // this.editedProductInformation.stock.available = this.productStock
                    this.retrieveProducts()
                    this.cancel()
                    toastr.success("Product Stock Updated")
                })
                .catch( error => {
                    toastr.error("An Error While Updating Stocks")
                })
                .finally(() => this.loading = false)
            }
        }
    }
</script>