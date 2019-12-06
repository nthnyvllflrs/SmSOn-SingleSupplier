<template>
    <v-container>
        <v-card>
            <v-tabs grow>
                <v-tab @click="retrieveOrderRequests('Pending')">Pending</v-tab>
                <v-tab @click="retrieveOrderRequests('Approved')">Approved</v-tab>
                <v-tab @click="retrieveReceivables()">Receivables</v-tab>
                <v-tab @click="retrieveOrderRequests('Delivered')">Delivered</v-tab>

                <!-- Pending Orders -->
                <v-tab-item>
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="table_headers" :items="order_requests">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
                                <v-icon color="error" class="mx-1" @click="cancelOrderRequest(item)">fa-ban</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Approved Orders -->
                <v-tab-item>
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="table_headers" :items="order_requests">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Reveivable Orders -->
                <v-tab-item>
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="reveivable_table_headers" :items="order_requests">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Delivered Orders -->
                <v-tab-item>
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="table_headers" :items="order_requests">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>

                <v-dialog max-width="750" v-model="order_request_information_dialog">
                    <v-card>
                        <v-card-title>Order Request Details</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols=12 md=6>
                                    <span class="ml-3 font-weight-bold">Supplier: {{ order_request_information.supplier }}</span>
                                </v-col>
                                <v-col cols=12 md=6>
                                    <span class="ml-3 font-weight-bold">Total: Php {{ Number(order_request_information.total).toLocaleString() }}</span>
                                </v-col>
                                <v-col cols=12>
                                    <v-row>
                                        <v-col cols=12 md=6 v-for="(order, index) in order_request_information.details" :key="index">
                                            <v-card>
                                                <v-card-title class="subtitle-2">
                                                    <span>{{ order.name }}</span><span class="caption ml-2">({{ order.code }})</span>
                                                    <v-spacer></v-spacer>
                                                    <span>Php {{ Number(order.total).toLocaleString() }}</span>
                                                    </v-card-title>
                                                <v-card-text class="body-2">
                                                    {{ order.quantity}} {{ order.unit }}(s)
                                                </v-card-text>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-dialog>
            </v-tabs>
        </v-card>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                order_request_information_dialog: false, loading: false,
                table_headers: [
                    {text: 'Code', value: 'code', align: 'center'},
                    {text: 'Supplier', value: 'supplier', align: 'center'},
                    {text: 'Date and Time', value: 'datetime', align: 'center'},
                    {text: 'Actions', value: 'action', align: 'center', sortable: false},
                ],

                reveivable_table_headers: [
                    {text: 'Code', value: 'code', align: 'center'},
                    {text: 'Delivery Date', value: 'delivery_date', align: 'center'},
                    {text: 'Supplier', value: 'supplier', align: 'center'},
                    {text: 'Logistic', value: 'logistic', align: 'center'},
                    {text: 'Actions', value: 'action', align: 'center', sortable: false},
                ],

                order_requests: [],
                order_request_information: {supplier: null, total: 0, datetime: null, details: []},
            }
        },

        mounted() {
            this.retrieveOrderRequests('Pending')
        },

        methods: {
            retrieveOrderRequests(status) {
                this.loading = true
                this.order_requests = []
                axios.get('/api/order-request', { params: { status}})
                .then( response => {
                    this.order_requests = response.data.success.order_requests
                })
                .catch(() => {})
                .finally(() => { this.loading = false})
            },

            retrieveReceivables() {
                axios.get('/api/order-request/receivables')
                .then( response => {
                    this.order_requests = response.data.success.order_requests
                })
                .catch( error => {
                    console.log(error.response.data)
                })
            },

            retrieveOrderRequestInformation(order_request) {
                axios.get('/api/order-request/' + order_request.id)
                .then( response => {
                    this.order_request_information = response.data.success.order_request_information
                    this.order_request_information_dialog = true
                })
                .catch(() => {})
            },

            cancelOrderRequest(order_request) {
                var itemDeletion = confirm('Are you sure you want to cancel order request?')
                if(itemDeletion == true) {
                     axios.delete('/api/order-request/' + order_request.id)
                    .then( response => {
                        const index = this.order_requests.indexOf(order_request)
                        this.order_requests.splice(index, 1)
                    })
                    .catch(() => {})
                }
            }
        }
    }
</script>