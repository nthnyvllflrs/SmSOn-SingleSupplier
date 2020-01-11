<template>
    <v-container>
        <v-card>
            <v-card-title>
                Customers
                <v-spacer />
                <v-text-field
                    v-model="search"
                    append-icon="fa-search"
                    label="Search customers"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table :loading="loading" loading-text="Loading... Please wait" :headers="customerTableHeaders" :items="customers" :search="search">
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <v-toolbar-title class="headline">Customers</v-toolbar-title>
                        <div class="flex-grow-1"></div>
                        <v-dialog v-model="dialog" max-width="500px" persistent>
                            <template v-slot:activator="{ on }">
                                <v-btn small color="primary" v-on="on">
                                    <v-icon small left>fa-plus</v-icon> Add Customer
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
                                            <v-text-field :error-messages="formErrors.username" v-model="editedCustomerInformation.username" label="Username" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field type="password" :error-messages="formErrors.password" v-model="editedCustomerInformation.password" label="Password" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field type="password" :error-messages="formErrors.password_confirmation" v-model="editedCustomerInformation.password_confirmation" label="Password Confirmation" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.name" v-model="editedCustomerInformation.name" label="Name" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-textarea :error-messages="formErrors.address" v-model="editedCustomerInformation.address" label="Address">
                                                <template slot="append-outer">
                                                    <v-btn icon fab small color="primary" @click="openMapDialog">
                                                        <v-icon>fa-map-marker-alt</v-icon>
                                                    </v-btn>
                                                    <v-dialog v-model="mapDialog" max-width="750px">
                                                        <v-card>
                                                            <v-card-title>Customer Address</v-card-title>
                                                            <v-card-text>
                                                                <v-container>
                                                                    <GmapMap
                                                                        ref="mapRef"
                                                                        :options="mapOptions"
                                                                        :center="{
                                                                            lat: Number(editedCustomerInformation.latitude), 
                                                                            lng: Number(editedCustomerInformation.longitude)
                                                                        }"
                                                                        :zoom="15"
                                                                        map-type-id="terrain"
                                                                        style="width: 100%; height: 50vh;">
                                                                        <GmapMarker ref="mapMarker" :position="{
                                                                            lat: Number(editedCustomerInformation.latitude), 
                                                                            lng: Number(editedCustomerInformation.longitude)
                                                                        }" />
                                                                    </GmapMap>
                                                                </v-container>
                                                            </v-card-text>
                                                        </v-card>
                                                    </v-dialog>
                                                </template>
                                            </v-textarea>
                                        </v-col>
                                        <v-col cols=6>
                                            <v-text-field v-model="editedCustomerInformation.latitude" label="Latitude" class="mr-2" readonly/>
                                        </v-col>
                                        <v-col cols=6>
                                            <v-text-field v-model="editedCustomerInformation.longitude" label="Longitude" class="ml-2" readonly/>
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.contact_number" v-model="editedCustomerInformation.contact_number" label="Contact Number" />
                                        </v-col>
                                    </v-row>
                                </v-card-text>

                                <v-card-actions>
                                    <div class="flex-grow-1" />
                                    <v-btn @click="cancel()">Cancel</v-btn>
                                    <v-btn class="px-8" color="primary" @click="saveCustomer()">Save</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>
                <template v-slot:item.action="{ item }">
                    <v-icon small @click="sendCustomerMessage(item)">fa-sms</v-icon>

                    <v-dialog v-model="messageDialog" max-width="500px" persistent>
                        <v-card>
                            <v-overlay :value="loading">
                                <v-progress-circular :size="100" :width="5" color="light-green accent-4" indeterminate></v-progress-circular>
                            </v-overlay>
                            <v-card-title>
                                <span class="headline">Send Message</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-text-field v-model="message.phone_number" label="Phone Number" disabled=""></v-text-field>
                                    <v-textarea v-model="message.body" label="Body"></v-textarea>
                                </v-container>
                            </v-card-text>

                            <v-card-actions>
                                <div class="flex-grow-1"></div>
                                <v-btn text color="success" @click="sendMessage(item)">Send</v-btn>
                                <v-btn text color="error" @click="messageDialog = !messageDialog">Cancel</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-icon class="mx-1" @click="editCustomer(item)">fa-pen</v-icon>
                    <v-icon class="mx-1" @click="deleteCustomer(item)">fa-trash-alt</v-icon>
                </template>
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
    import {gmapApi} from 'vue2-google-maps'
    export default {
        data() {
            return {
                dialog: false, informationDialog: false, mapDialog: false, messageDialog: false,
                message: { phone_number: null, message: null},
                loading: false, search: '', editedIndex: -1,
                customerTableHeaders: [
                    { text: 'Code', value: 'code' },
                    { text: 'Username', value: 'username' },
                    { text: 'Name', value: 'name' },
                    { text: 'Address', value: 'address' },
                    { text: 'Contact Number', value: 'contact_number' },
                    { text: 'Actions', value: 'action', sortable: false },
                ],
                customers: [],

                defaultCustomerInformation: { username: null, name: null, address: null, latitude: 6.9161, longitude: 122.0866, contact_number: null, password: null, password_confirmation: null, customer_id: sessionStorage.getItem('user-information-id')},
                editedCustomerInformation: { username: null, name: null, address: null, latitude: 6.9161, longitude: 122.0866, contact_number: null, password: null, password_confirmation: null, customer_id: sessionStorage.getItem('user-information-id')},
                formErrors: { username: null, name: null, address: null, contact_number: null, password: null, password_confirmation: null},

                customerCoordinates: { lat: 6.9161, lng: 122.0866},
                mapOptions: {
                    zoomControl: false, mapTypeControl: false, scaleControl: false,
                    streetViewControl: false, rotateControl: false, fullscreenControl: false, disableDefaultUi: true
                },
            }
        },
        mounted() {
            this.retrieveCustomers()
            // this.reverseGeoCode()
        },
        computed: {
            google: gmapApi,
            formTitle () {
                return this.editedIndex === -1 ? 'New Customer' : 'Edit Customer'
            },
        },
        methods: {
            sendCustomerMessage(item) {
                this.messageDialog = !this.messageDialog
                this.message.phone_number = item.phone_number
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

            openMapDialog() {
                this.mapDialog = true

                this.$nextTick(() => {
                    this.$refs.mapRef.$mapPromise.then((map) => {
                        this.$refs.mapRef.$mapObject.addListener('click', this.changeMarkerPosition)
                    })
                })
            },

            changeMarkerPosition(event) {
                this.editedCustomerInformation.latitude = event.latLng.lat()
                this.editedCustomerInformation.longitude = event.latLng.lng()

                axios.get('/api/customer/reverse-geocode', {
                    params: {
                        latitude: event.latLng.lat(),
                        longitude: event.latLng.lng(),
                    }
                })
                .then( response => {
                    this.editedCustomerInformation.address = response.data.success.address
                })
                .catch( error => {
                    toastr.error("Reverse Geocoding Error")
                })
                .finally(() => {})
            },

            retrieveCustomers() {
                this.loading = true
                axios.get('/api/customer')
                .then( response => {
                    this.customers = response.data.success.customers
                })
                .catch( error => {
                    toastr.error("Retrieve Customers Error")
                })
                .finally(() => { this.loading = false})
            },

            deleteCustomer(customer) {
                var customerDeletion = confirm('Are you sure you want to delete this Customer?')
                if(customerDeletion == true) {
                    axios.delete('api/customer/' + customer.id)
                    .then( response => {
                        const index = this.customers.indexOf(customer)
                        this.customers.splice(index, 1)
                        toastr.success("Customer Deleted")
                    })
                    .catch( error => { 
                        toastr.error("Delete Customer Error")
                    })
                }
            },

            editCustomer(customer) {
                this.editedIndex = this.customers.indexOf(customer)
                // this.customerCoordinates.lat = customer.latitude
                // this.customerCoordinates.lng = customer.longitude
                this.editedCustomerInformation = Object.assign({}, customer)
                this.dialog = true
            },
            
            cancel() {
                this.dialog = false; this.informationDialog = false
                setTimeout(() => {
                    this.formErrors = { username: null, name: null, address: null, contact_number: null, password: null, password_confirmation: null}
                    this.editedCustomerInformation = Object.assign({}, this.defaultCustomerInformation)
                    this.editedIndex = -1
                }, 500)
            },

            saveCustomer() {
                if (this.editedIndex > -1) {
                    this.updateCustomer()
                } else {
                    this.createCustomer()
                }
            },

            createCustomer() {
                this.loading = true
                
                axios.post('/api/customer', {
                    ...(_.omit(this.editedCustomerInformation, 'code')),
                    // latitude: this.customerCoordinates.lat,
                    // longitude: this.customerCoordinates.lng,
                })
                .then( response => {
                    this.editedCustomerInformation.code = response.data.success.customer.code
                    this.editedCustomerInformation.id = response.data.success.user.id
                    this.customers.push(this.editedCustomerInformation)
                    this.cancel()
                    toastr.success("Customers Created")
                })
                .catch( error => {
                    if(error.response.status == 422) {
                        if(typeof error.response.data == 'object'){
                            this.formErrors = error.response.data.errors
                        } else {
                            this.errorMessage = error.response.data
                        }
                    } else {
                        toastr.error("Create Customer Error")
                    }
                })
                .finally( x => { this.loading = false})
            },

            updateCustomer() {
                this.loading = true
                _.omit(this.editedCustomerInformation, 'code')
                axios.put('/api/customer/' + this.editedCustomerInformation.id, {
                    ...(_.omit(this.editedCustomerInformation, 'code')),
                    // latitude: this.customerCoordinates.lat,
                    // longitude: this.customerCoordinates.lng,
                })
                .then( response => {
                    var customer = Object.assign(this.customers[this.editedIndex], this.editedCustomerInformation)
                    // costumer.latitude = this.customerCoordinates.lat
                    // costumer.longitude = this.customerCoordinates.lat
                    this.cancel()
                    toastr.success("Customers Updated")
                })
                .catch( error => {
                    if(error.response.status == 422) {
                        if(typeof error.response.data == 'object'){
                            this.formErrors = error.response.data.errors
                        } else {
                            this.errorMessage = error.response.data
                        }
                    } else {
                        toastr.error("Update Customer Error")
                    }
                })
                .finally( x => { this.loading = false})
            }
        }
    }
</script>