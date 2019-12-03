<template>
    <v-container>
        <v-card>
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
                                            <v-text-field :error-messages="formErrors.password" v-model="editedCustomerInformation.password" label="Password" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.password_confirmation" v-model="editedCustomerInformation.password_confirmation" label="Password Confirmation" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.name" v-model="editedCustomerInformation.name" label="Name" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-textarea :error-messages="formErrors.address" v-model="editedCustomerInformation.address" label="Address" />
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
                    <v-icon class="mx-1" @click="editCustomer(item)">fa-pen</v-icon>
                    <v-icon class="mx-1" @click="deleteCustomer(item)">fa-trash-alt</v-icon>
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
                customerTableHeaders: [
                    { text: 'Code', value: 'code' },
                    { text: 'Username', value: 'username' },
                    { text: 'Name', value: 'name' },
                    { text: 'Address', value: 'address' },
                    { text: 'Contact Number', value: 'contact_number' },
                    { text: 'Actions', value: 'action', sortable: false },
                ],
                customers: [],

                defaultCustomerInformation: { username: null, name: null, address: null, contact_number: null, password: null, password_confirmation: null, supplier_id: sessionStorage.getItem('user-information-id')},
                editedCustomerInformation: { username: null, name: null, address: null, contact_number: null, password: null, password_confirmation: null, supplier_id: sessionStorage.getItem('user-information-id')},
                formErrors: { username: null, name: null, address: null, contact_number: null, password: null, password_confirmation: null},
            }
        },
        mounted() {
            this.retrieveCustomers()
        },
        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'New Customer' : 'Edit Customer'
            },
        },
        methods: {
            retrieveCustomers() {
                this.loading = true
                axios.get('/api/customer')
                .then( response => {
                    this.customers = response.data.success.customers
                })
                .catch( error => {
                    toastr.error("An Error Occurred")
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
                    .catch( error => { alert(error)})
                }
            },

            editCustomer(customer) {
                this.editedIndex = this.customers.indexOf(customer)
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
                    ...(_.omit(this.editedCustomerInformation, 'code'))
                })
                .then( response => {
                    this.editedCustomerInformation.code = response.data.success.customer.code
                    this.editedCustomerInformation.id = response.data.success.user.id
                    this.customers.push(this.editedCustomerInformation)
                    this.cancel()
                    toastr.success("Customers Created")
                    console.log(this.editedCustomerInformation.id)
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

            updateCustomer() {
                this.loading = true
                _.omit(this.editedCustomerInformation, 'code')
                axios.put('/api/customer/' + this.editedCustomerInformation.id, {
                    ...(_.omit(this.editedCustomerInformation, 'code'))
                })
                .then( response => {
                    Object.assign(this.customers[this.editedIndex], this.editedCustomerInformation)
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
                        toastr.error("An Error Occurred")
                    }
                })
                .finally( x => { this.loading = false})
            }
        }
    }
</script>