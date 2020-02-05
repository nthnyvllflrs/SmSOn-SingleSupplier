<template>
    <v-container>
        <v-card-title>
            Suppliers
            <v-spacer />
            <v-text-field
                v-model="search"
                append-icon="fa-search"
                label="Search supplier"
                single-line
                hide-details
            ></v-text-field>
        </v-card-title>
        <v-card>
            <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="supplierTableHeaders" :items="suppliers" :search="search">
                <!-- <template v-slot:top>
                    <v-toolbar flat color="white">
                        <v-toolbar-title class="headline">Suppliers</v-toolbar-title>
                    </v-toolbar>
                </template> -->
                <template v-slot:item.action="{ item }">
                    <v-icon class="mx-1" @click="retrieveSupplierInformation(item)">fa-info-circle</v-icon>
                    
                    <v-dialog v-model="informationDialog" max-width="1200px" persistent>
                        <v-card>
                            <v-overlay :value="loading">
                                <v-progress-circular :size="100" :width="5" color="primary" indeterminate></v-progress-circular>
                            </v-overlay>
                            <v-card-title class="headline">
                                <img :src="supplierInformation.image_url" width="50px" height="50px" alt="Logo" v-if="supplierInformation.image_url" />
                                <span class="ml-3">{{ supplierInformation.name }}</span>
                                <v-spacer />
                                <v-btn x-small fab color="primary" @click="cancel()">
                                    <v-icon small>fa-times</v-icon>
                                </v-btn>
                            </v-card-title>
                            <v-card-text>
                                <v-row no-gutters>
                                    <v-col cols=12>
                                        <p class="subtitle-2">Description: {{ supplierInformation.description }}</p>
                                    </v-col>
                                    <v-col cols=12 md=4>
                                        <p class="subtitle-2">Address: {{ supplierInformation.address }}</p>
                                    </v-col>
                                    <v-col cols=6 md=4>
                                        <p class="subtitle-2">Product Count: {{ supplierInformation.product_count }}</p>
                                    </v-col>
                                    <v-col cols=6 md=4>
                                        <p class="subtitle-2">Logistic Count: {{ supplierInformation.logistic_count }}</p>
                                    </v-col>
                                </v-row>
                                <v-divider />
                                <v-row>
                                    <v-col cols=12 md=4 v-for="(product, index) in supplierInformation.products" :key="index">
                                        <v-card>
                                            <v-card-title>
                                                <span class="title">{{ product.name }}</span><span class="caption ml-2">({{ product.code }})</span>
                                                <v-spacer></v-spacer>
                                                <span class="subtitle-2">Php {{ Number(product.price).toLocaleString() }}</span>
                                                </v-card-title>
                                            <v-card-text class="body-2">
                                                <v-row no-gutters>
                                                    <v-col cols=12>
                                                        Discription: {{ product.description }}
                                                    </v-col>
                                                    <v-col cols=12>
                                                        Unit: {{ product.unit }}
                                                    </v-col>
                                                </v-row>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-dialog>
                </template>
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                dialog: false, informationDialog: false,
                loading: false, search: '', editedIndex: -1,
                supplierTableHeaders: [
                    { text: 'Code', value: 'code' },
                    { text: 'Username', value: 'username' },
                    { text: 'Name', value: 'name' },
                    { text: 'Address', value: 'address' },
                    { text: 'Contact Number', value: 'contact_number' },
                    { text: 'Actions', value: 'action', sortable: false },
                ],
                suppliers: [],
                
                supplierInformation: { name: null, description: null, address: null, image_url: null, product_count: 0, logistic_count: 0, products: []},
            }
        },
        mounted() {
            this.retrieveSuppliers()
        },
        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'New Supplier' : 'Edit Supplier'
            },
        },
        methods: {
            retrieveSuppliers() {
                this.loading = true
                axios.get('/api/supplier')
                .then( response => {
                    this.suppliers = response.data.success.suppliers
                })
                .catch( error => {
                    toastr.error("Retrieve Suppliers Error")
                })
                .finally(() => { this.loading = false})
            },

            retrieveSupplierInformation(supplier) {
                this.informationDialog = true
                this.loading = true
                axios.get('/api/supplier/' + supplier.id)
                .then( response => {
                    this.supplierInformation = response.data.success.supplier
                })
                .catch( error => {
                    toastr.error("Retrieve Supplier Information Error")
                })
                .finally( x => { this.loading = false})
            },
            
            cancel() {
                this.informationDialog = false
                this.supplierInformation = { name: null, description: null, address: null, product_count: 0, logistic_count: 0, products: []}
            },
        }
    }
</script>