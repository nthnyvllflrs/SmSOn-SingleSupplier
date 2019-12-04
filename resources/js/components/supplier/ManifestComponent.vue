<template>
    <v-container>
        <v-card>
            <v-data-table :headers="dataTableHeaders" :items="manifests" single-expand show-expand >
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <v-toolbar-title class="headline">Manifests</v-toolbar-title>
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
                                            <v-select v-model="editedManifest.logistic" :items="logistics" item-text="name" item-value="id" label="Logistic" />
                                            </v-col>
                                            <v-col cols=12 md=4>
                                                <v-menu v-model="menu" 
                                                    :close-on-content-click="false"
                                                    transition="scale-transition" min-width="290px" >
                                                    <template v-slot:activator="{ on }">
                                                        <v-text-field v-model="editedManifest.delivery_date" label="Delivery Date" readonly v-on="on" />
                                                    </template>
                                                    <v-date-picker color="primary" :min="new Date().toISOString().substr(0, 10)" v-model="editedManifest.delivery_date" @input="menu = false" />
                                                </v-menu>
                                            </v-col>
                                            <v-col cols=9 md=3>
                                                <v-select v-model="selectedOrderRequestId" :items="order_requests" item-text="code" item-value="id" label="Order Requests" />
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
                                                <v-btn color="primary" block>Submit</v-btn>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>

                <template v-slot:expanded-item="{ item }">
                    <td>{{ item }}</td>
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
                expanded: [],
                singleExpand: false,
                dataTableHeaders: [
                    { text: 'Code', value: 'code', align: 'center'},
                    { text: 'Logistic', value: 'logistic', align: 'center'},
                    { text: 'Delivery Date', value: 'datetime', align: 'center'},
                    { text: 'Actions', value: 'action', align: 'center', sortable: false},
                ],
                manifests: [], logistics: [], order_requests: [],

                defaultManifest: { logistic: null, delivery_date: new Date().toISOString().substr(0, 10), order_requests: []},
                editedManifest: { logistic: null, delivery_date: new Date().toISOString().substr(0, 10), order_requests: []},
                selectedOrderRequestId: 0,
            }
        },

        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'New Manifest' : 'Edit Manifest'
            },
        },

        mounted() {
            this.retrieveSupplierLogistics()
            this.retrieveSupplierOrderRequests()
        },
        
        methods: {
            retrieveSupplierLogistics() {
                axios.get('/api/supplier/logistics')
                .then( response => {
                    this.logistics = response.data.success.logistics
                })
                .catch(() => {})
            },
            
            retrieveSupplierOrderRequests() {
                axios.get('/api/supplier/order-requests')
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
            }
        }
    }
</script>