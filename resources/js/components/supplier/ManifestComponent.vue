<template>
    <v-container>
        <v-card>
            <v-card-title>
                Manifests
                <v-spacer />
                <v-text-field
                    v-model="search"
                    append-icon="fa-search"
                    label="Search manifest"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table :headers="dataTableHeaders" :items="manifests" show-expand single-expand expanded.sync :search="search">
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <!-- <v-toolbar-title class="headline">Manifests</v-toolbar-title> -->
                        <div class="flex-grow-1"></div>
                        <v-dialog v-model="dialog" max-width="1000px" persistent>
                            <template v-slot:activator="{ on }">
                                <v-btn small color="primary" v-on="on">
                                    <v-icon small left>fa-plus</v-icon> Add Manifest
                                </v-btn>
                            </template>

                            <v-card>
                                <v-overlay :value="loading">
                                    <v-progress-circular :size="100" :width="5" color="primary" indeterminate></v-progress-circular>
                                </v-overlay>
                                <v-card-title>
                                    <span class="headline">{{ formTitle }}</span>
                                    <v-spacer />
                                    <v-btn fab dark x-small color="error" @click="dialog = false">
                                        <v-icon x-small dark>fa-times</v-icon>
                                    </v-btn>
                                </v-card-title>
                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols=12 md=4>
                                                <v-autocomplete v-model="editedManifest.logistic_id" :items="logistics" item-text="name" item-value="id" label="Logistic" :error-messages="formErrors.logistic_id" />
                                            </v-col>
                                            <v-col cols=12 md=4>
                                                <v-menu v-model="menu" 
                                                    :close-on-content-click="false"
                                                    transition="scale-transition" min-width="290px" >
                                                    <template v-slot:activator="{ on }">
                                                        <v-text-field v-model="editedManifest.delivery_date" label="Delivery Date" readonly v-on="on" :error-messages="formErrors.delivery_date" />
                                                    </template>
                                                    <v-date-picker color="primary" :min="new Date().toISOString().substr(0, 10)" v-model="editedManifest.delivery_date" @input="menu = false" />
                                                </v-menu>
                                            </v-col>
                                            <v-col cols=9 md=3>
                                                <v-autocomplete v-model="selectedOrderRequestId" :items="order_requests" item-text="code" item-value="id" label="Order Requests" />
                                            </v-col>
                                            <v-col cols=3 md=1 align="center" justify="center">
                                                <v-btn color="primary" dark small fab @click="selectOrderRequest()">
                                                    <v-icon dark small>fa-plus</v-icon>
                                                </v-btn>
                                            </v-col>
                                            <v-col cols=12 md=4 v-for="(order, index) in editedManifest.order_requests" :key="index">
                                                <v-card>
                                                    <v-card-title class="subtitle-2">
                                                        {{ order.code }}
                                                        <v-spacer />
                                                        <v-icon small dark color="error" @click="unselectOrderRequest(order)">fa-times</v-icon>
                                                    </v-card-title>
                                                    <v-card-text>{{ order.customer }}, {{ order.address }}</v-card-text>
                                                </v-card>
                                            </v-col>
                                            <v-col cols=12 sm=12 offset-sm=0 md=4 offset-md=8 v-if="editedManifest.order_requests.length > 0">
                                                <v-btn color="primary" block @click="saveManifest()">Submit</v-btn>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>

                <template v-slot:item.action="{ item }">
                    <v-icon class="mx-1" @click="editManifest(item)">fa-pen</v-icon>
                    <v-icon class="mx-1" @click="deleteManifest(item)">fa-trash-alt</v-icon>
                </template>

                <template v-slot:expanded-item="{ item, headers }">
                    <td :colspan="headers.length">
                        <v-row align="center" justify="center">
                            <v-col cols=12 sm=4 md=3 v-for="(order_request, index) in item.order_requests" :key="index">
                                <v-card>
                                    <v-card-title class="subtitle-2">
                                        <span>{{ order_request.code }}</span>
                                        </v-card-title>
                                    <v-card-text class="body-2">
                                        {{ order_request.customer }}, {{ order_request.address }}
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </td>
                </template>
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
    export default {
        data () {
            return {
                dialog: false, loading: false, editedIndex: -1, menu: false,
                expanded: [], search: '',
                singleExpand: false,
                dataTableHeaders: [
                    { text: 'Code', value: 'code', align: 'center'},
                    { text: 'Logistic', value: 'logistic', align: 'center'},
                    { text: 'Delivery Date', value: 'delivery_date', align: 'center'},
                    { text: 'Actions', value: 'action', align: 'center', sortable: false},
                    { value: 'data-table-expand'},
                ],
                manifests: [], logistics: [], order_requests: [],

                defaultManifest: { supplier_id: sessionStorage.getItem('user-information-id'), logistic_id: null, delivery_date: new Date().toISOString().substr(0, 10), order_requests: []},
                editedManifest: { supplier_id: sessionStorage.getItem('user-information-id'), logistic_id: null, delivery_date: new Date().toISOString().substr(0, 10), order_requests: []},
                selectedOrderRequestId: 0,

                formErrors: { supplier_id: null, logistic_id: null, delivery_date: null}
            }
        },

        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'New Manifest' : 'Edit Manifest'
            },
        },

        watch: {
            dialog (val) {
                val || this.cancel()
            }
        },

        mounted() {
            this.retrieveManifests()
            this.retrieveSupplierLogistics()
            this.retrieveSupplierOrderRequests()
        },
        
        methods: {
            retrieveManifests() {
                axios.get('/api/manifest')
                .then( response => {
                    this.manifests = response.data.success.manifests
                })
                .catch( error => {
                    console.log(error.response.data)
                })
            },

            retrieveSupplierLogistics() {
                axios.get('/api/manifest/logistics')
                .then( response => {
                    this.logistics = response.data.success.logistics
                })
                .catch(() => {})
            },
            
            retrieveSupplierOrderRequests() {
                axios.get('/api/manifest/order-requests')
                .then( response => {
                    this.order_requests = response.data.success.order_requests
                })
                .catch(() => {})
            },

            selectOrderRequest() {
                if(this.selectedOrderRequestId > 0) {
                    var index = this.order_requests.map(function(order) { return order.id; }).indexOf(this.selectedOrderRequestId);
                    this.editedManifest.order_requests.push(this.order_requests[index])
                    this.order_requests.splice(index, 1)
                    this.selectedOrderRequestId = 0
                }
            },

            unselectOrderRequest(order) {
                var index = this.editedManifest.order_requests.indexOf(order)
                this.order_requests.push(order)
                this.order_requests.sort((a, b) => a.code.localeCompare(b.code))
                this.editedManifest.order_requests.splice(index, 1)
            },

            cancel() {
                this.dialog = false;
                setTimeout(() => {
                    this.editedManifest = Object.assign({}, this.defaultManifest)
                    this.editedIndex = -1
                }, 300)
            },

            saveManifest() {
                if (this.editedIndex > -1) {
                    this.updateManifest()
                } else {
                    this.createManifest()
                }
            },

            createManifest() {
                this.loading = true
                axios.post('/api/manifest', {
                    ...this.editedManifest
                })
                .then( response => {
                    this.retrieveManifests()
                    this.retrieveSupplierOrderRequests()
                    this.cancel()   
                    toastr.success("New Manifest Created")
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
                .finally(() => {
                    this.loading = false
                })
            },

            updateManifest() {
                this.loading = true
                axios.put('/api/manifest/' + this.editedManifest.id, {
                    ...this.editedManifest
                })
                .then( response => {
                    this.retrieveManifests()
                    this.retrieveSupplierOrderRequests()
                    this.cancel()   
                    toastr.success("Manifest Updated")
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
                .finally(() => {
                    this.loading = false
                })
            },

            editManifest(manifest) {
                this.dialog = true; this.loading = true
                this.editedIndex = this.manifests.indexOf(manifest)
                axios.get('/api/manifest/' + manifest.id + '/edit')
                .then( response => {
                    this.editedManifest = Object.assign({}, response.data.success.manifest)
                })
                .catch( error => {
                    console.log(error.response.data)
                })
                .finally(() => {
                    this.loading = false
                })
            },

            deleteManifest(manifest) {
                console.log(manifest.id)
            }
        }
    }
</script>