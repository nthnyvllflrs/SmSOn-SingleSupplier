<template>
    <v-container>
        <v-card>
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
            <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="supplierTableHeaders" :items="suppliers" :search="search">
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <!-- <v-toolbar-title class="headline">Suppliers</v-toolbar-title> -->
                        <div class="flex-grow-1"></div>
                        <v-dialog v-model="dialog" max-width="500px" persistent>
                            <template v-slot:activator="{ on }">
                                <v-btn small color="primary" v-on="on">
                                    <v-icon small left>fa-plus</v-icon> Add Supplier
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
                                            <v-file-input :error-messages="formErrors.image" v-model="photoData" prepend-icon="fa-camera" label="Supplier Logo" accept="image/png, image/jpeg, image/bmp" @change="fileConvert()" messages="Uploading a new image will overwrite the existing image."/>
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.username" v-model="editedSupplierInformation.username" label="Username" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.password" v-model="editedSupplierInformation.password" label="Password" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.password_confirmation" v-model="editedSupplierInformation.password_confirmation" label="Password Confirmation" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.name" v-model="editedSupplierInformation.name" label="Name" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-textarea :error-messages="formErrors.description" v-model="editedSupplierInformation.description" label="Description" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-textarea :error-messages="formErrors.address" v-model="editedSupplierInformation.address" label="Address" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.contact_number" v-model="editedSupplierInformation.contact_number" label="Contact Number" />
                                        </v-col>
                                    </v-row>
                                </v-card-text>

                                <v-card-actions>
                                    <div class="flex-grow-1" />
                                    <v-btn @click="cancel()">Cancel</v-btn>
                                    <v-btn class="px-8" color="primary" @click="saveSupplier()">Save</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>
                <template v-slot:item.action="{ item }">
                    <v-icon class="mx-1" @click="sendCustomerMessage(item)">fa-sms</v-icon>

                    <v-dialog v-model="messageDialog" max-width="500px" persistent>
                        <v-card>
                            <v-overlay :value="loading">
                                <v-progress-circular :size="100" :width="5" color="primary" indeterminate></v-progress-circular>
                            </v-overlay>
                            <v-card-title>
                                <span class="headline">Send Message</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-text-field v-model="message.phone_number" label="Phone Number" disabled=""></v-text-field>
                                    <v-textarea v-model="message.message" label="Body"></v-textarea>
                                </v-container>
                            </v-card-text>

                            <v-card-actions>
                                <div class="flex-grow-1"></div>
                                <v-btn @click="messageDialog = !messageDialog">Cancel</v-btn>
                                <v-btn color="primary" @click="sendMessage(item)">Send</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-icon class="mx-1" @click="retrieveSupplierInformation(item)">fa-info-circle</v-icon>
                    <v-icon class="mx-1" @click="editSupplier(item)">fa-pen</v-icon>
                    <v-icon class="mx-1" @click="deleteSupplier(item)">fa-trash-alt</v-icon>

                    <v-dialog v-model="informationDialog" max-width="1200px" persistent>
                        <v-card>
                            <v-overlay :value="loading">
                                <v-progress-circular :size="100" :width="5" color="primary" indeterminate></v-progress-circular>
                            </v-overlay>
                            <v-card-title class="headline">
                                <img :src="supplierInformation.image_url" width="50px" height="50px" alt="Logo" v-if="supplierInformation.image_url"/>
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
                dialog: false, informationDialog: false,messageDialog: false,
                message: { phone_number: null, message: null},
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

                defaultSupplierInformation: { username: null, name: null, description: null, address: null, contact_number: null, password: null, password_confirmation: null},
                editedSupplierInformation: { username: null, name: null, description: null, address: null, contact_number: null, password: null, password_confirmation: null},
                formErrors: { username: null, name: null, description: null, address: null, contact_number: null, password: null, password_confirmation: null, image: null},

                supplierInformation: { name: null, description: null, address: null, image_url: null, product_count: 0, logistic_count: 0, products: []},

                photoData: null, photoByteData: null,
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
            sendCustomerMessage(item) {
                this.messageDialog = !this.messageDialog
                this.message.phone_number = item.contact_number
            },

            sendMessage() {
                this.loading = true
                axios.post('api/send-sms', { 
                    ...this.message
                })
                .then( response => {
                    this.messageDialog = !this.messageDialog
                    toastr.success("SMS Sent")
                })
                .catch( error => {
                    toastr.error("Send SMS Error")
                })
                .finally( x => { this.loading = false})
            },
            
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
                    toastr.error("Supplier Information Error")
                })
                .finally( x => { this.loading = false})
            },

            deleteSupplier(supplier) {
                var supplierDeletion = confirm('Are you sure you want to delete this supplier?')
                if(supplierDeletion == true) {
                    axios.delete('api/supplier/' + supplier.id)
                    .then( response => {
                        const index = this.suppliers.indexOf(supplier)
                        this.suppliers.splice(index, 1)
                        toastr.success("Supplier Deleted")
                    })
                    .catch( error => { 
                        toastr.error("Delete Supplier Error")
                    })
                }
            },

            editSupplier(supplier) {
                this.editedIndex = this.suppliers.indexOf(supplier)
                this.editedSupplierInformation = Object.assign({}, supplier)
                this.dialog = true
            },
            
            cancel() {
                this.dialog = false; this.informationDialog = false
                setTimeout(() => {
                    this.formErrors = { username: null, name: null, address: null, contact_number: null, password: null, password_confirmation: null}
                    this.supplierInformation = { name: null, description: null, address: null, product_count: 0, logistic_count: 0, products: []},
                    this.editedSupplierInformation = Object.assign({}, this.defaultSupplierInformation)
                    this.editedIndex = -1
                }, 500)
            },

            saveSupplier() {
                if (this.editedIndex > -1) {
                    this.updateSupplier()
                } else {
                    this.createSupplier()
                }
            },

            createSupplier() {
                this.loading = true
                axios.post('/api/supplier', {
                    ...(_.omit(this.editedSupplierInformation, 'code')),
                    image: this.photoByteData
                })
                .then( response => {
                    this.editedSupplierInformation.code = response.data.success.supplier.code
                    this.editedSupplierInformation.id = response.data.success.user.id
                    this.suppliers.push(this.editedSupplierInformation)
                    this.cancel()
                    toastr.success("Supplier Created")
                })
                .catch( error => {
                    if(error.response.status == 422) {
                        if(typeof error.response.data == 'object'){
                            this.formErrors = error.response.data.errors
                        } else {
                            this.errorMessage = error.response.data
                        }
                    } else {
                        toastr.error("Create Supplier Error")
                    }
                })
                .finally( x => { this.loading = false})
            },

            updateSupplier() {
                this.loading = true
                axios.put('/api/supplier/' + this.editedSupplierInformation.id, {
                    ...(_.omit(this.editedSupplierInformation, 'code')),
                    image: this.photoByteData
                })
                .then( response => {
                    Object.assign(this.suppliers[this.editedIndex], this.editedSupplierInformation)
                    this.cancel()
                    toastr.success("Supplier Updated")
                })
                .catch( error => {
                    if(error.response.status == 422) {
                        if(typeof error.response.data == 'object'){
                            this.formErrors = error.response.data.errors
                        } else {
                            this.errorMessage = error.response.data
                        }
                    } else {
                        toastr.error("Update Supplier Error")
                    }
                })
                .finally( x => { this.loading = false})
            },

            fileConvert() {
                try {
                    var reader = new FileReader()
                    reader.onload = () => {
                        this.photoByteData = reader.result
                        console.log(typeof(this.photoByteData))
                    }
                    reader.readAsDataURL(this.photoData)
                } catch(e) {
                    toastr.error("File Converter Error")
                }
            }
        }
    }
</script>