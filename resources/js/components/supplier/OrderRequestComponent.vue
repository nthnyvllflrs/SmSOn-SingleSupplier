<template>
    <v-container>
        <v-card>
            <v-card-title>
                Order Requests
                <v-spacer />
                <v-text-field
                    v-model="search"
                    append-icon="fa-search"
                    label="Search order request"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-tabs grow v-model="tabModel">
                <v-tab :href="`#disapproved-tab`" @click="retrieveOrderRequests('Disapproved')">Disapproved</v-tab>
                <v-tab :href="`#pending-tab`" @click="retrieveOrderRequests('Pending')" active-class>Pending</v-tab>
                <v-tab :href="`#aprroved-tab`" @click="retrieveOrderRequests('Approved')">Approved</v-tab>
                <v-tab :href="`#delivered-tab`" @click="retrieveOrderRequests('Delivered')">Delivered</v-tab>
            </v-tabs>

            <v-tabs-items v-model="tabModel">
                 <!-- Disapproved Orders -->
                <v-tab-item :value="`disapproved-tab`">
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="table_headers" :items="order_requests" :search="search">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
                                <v-icon color="success" class="mx-1" @click="updatedRequestStatus(item, 'Pending')">fa-check-square</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Pending Orders -->
                <v-tab-item :value="`pending-tab`">
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="table_headers" :items="order_requests" :search="search">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
                                <v-icon color="success" class="mx-1" @click="updatedRequestStatus(item, 'Approved')">fa-check-square</v-icon>
                                <v-icon color="error" class="mx-1" @click="updatedRequestStatus(item, 'Disapproved')">fa-times-circle</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Approved Orders -->
                <v-tab-item :value="`aprroved-tab`">
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="table_headers" :items="order_requests" :search="search">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
                                <v-icon color="success" class="mx-1" @click="updatedRequestStatus(item, 'Delivered')">fa-check-square</v-icon>
                                <v-icon color="error" class="mx-1" @click="updatedRequestStatus(item, 'Pending')">fa-times-circle</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Delivered Orders -->
                <v-tab-item :value="`delivered-tab`">
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="table_headers" :items="order_requests" :search="search">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
                                <v-icon color="error" class="mx-1" @click="updatedRequestStatus(item, 'Approved')">fa-times-circle</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>
            </v-tabs-items>

            <v-dialog max-width="750" v-model="order_request_information_dialog">
                <v-card>
                    <v-card-title>
                        Order Request Details
                        <v-spacer />
                        <span class="ml-3 font-weight-bold">Total: Php {{ Number(order_request_information.total).toLocaleString() }}</span>
                    </v-card-title>
                    <v-card-text>
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
                    </v-card-text>
                </v-card>
            </v-dialog>
        </v-card>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                tabModel: 'pending-tab', search: '',
                order_request_information_dialog: false, loading: false,
                table_headers: [
                    {text: 'Code', value: 'code', align: 'center'},
                    {text: 'Customer', value: 'customer', align: 'center'},
                    {text: 'Date and Time', value: 'datetime', align: 'center'},
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

            retrieveOrderRequestInformation(order_request) {
                axios.get('/api/order-request/' + order_request.id)
                .then( response => {
                    this.order_request_information = response.data.success.order_request_information
                    this.order_request_information_dialog = true
                })
                .catch(() => {})
            },

            updatedRequestStatus(order_request, status) {
                axios.put('/api/order-request/' + order_request.id + '/status', {
                    status
                })
                .then( response => {
                    const index = this.order_requests.indexOf(order_request)
                    this.order_requests.splice(index, 1)
                })
                .catch(() => {})
            }
        }
    }
</script>