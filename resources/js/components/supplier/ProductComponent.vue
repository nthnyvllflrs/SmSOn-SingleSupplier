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
                <template v-slot:item.action="{ item }">
                    <v-icon class="mx-1" @click="editProduct(item)">fa-pen</v-icon>
                    <v-icon class="mx-1" @click="deleteProduct(item)">fa-trash-alt</v-icon>
                </template>
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                supplierID: sessionStorage.getItem('user-information-id'),

                dialog: false, informationDialog: false,
                loading: false, search: '', editedIndex: -1,
                productTableHeaders: [
                    { text: 'Code', value: 'code' },
                    { text: 'Name', value: 'name' },
                    { text: 'Description', value: 'description' },
                    { text: 'Unit', value: 'unit' },
                    { text: 'Price', value: 'price' },
                    { text: 'Actions', value: 'action', sortable: false },
                ],
                products: [],

                defaultProductInformation: { code: null, name: null, description: null, unit: null, price: null, supplier_id: sessionStorage.getItem('user-information-id')},
                editedProductInformation: { code: null, name: null, description: null, unit: null, price: null, supplier_id: sessionStorage.getItem('user-information-id')},
                formErrors: { code: null, name: null, description: null, unit: null, price: null,},
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
                this.dialog = false; this.informationDialog = false
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
                    this.editedProductInformation.id = response.data.success.product.id
                    this.products.push(this.editedProductInformation)
                    this.cancel()
                    toastr.success("Product Created")
                    console.log(this.editedProductInformation.id)
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
                axios.put('/api/product/' + this.editedProductInformation.id, {
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
            }
        }
    }
</script>