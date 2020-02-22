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
                <v-tab :href="`#disapproved-tab`"  @click="retrieveOrderRequests('Disapproved')">Disapproved</v-tab>
                <v-tab :href="`#pending-tab`" @click="retrieveOrderRequests('Pending')">Pending</v-tab>
                <v-tab :href="`#approved-tab`" @click="retrieveOrderRequests('Approved')">Approved</v-tab>
                <v-tab :href="`#receivable-tab`" @click="retrieveReceivables()">Receivables</v-tab>
                <v-tab :href="`#delivered-tab`" @click="retrieveOrderRequests('Delivered')">Delivered</v-tab>
            </v-tabs>

            <v-tabs-items v-model="tabModel">
                <!-- Approved Orders -->
                <v-tab-item :value="`disapproved-tab`">
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="table_headers" :items="order_requests" :search="search">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
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
                                <v-icon color="error" class="mx-1" @click="cancelOrderRequest(item)">fa-ban</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Approved Orders -->
                <v-tab-item :value="`approved-tab`">
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="table_headers" :items="order_requests" :search="search">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Reveivable Orders -->
                <v-tab-item :value="`receivable-tab`">
                    <v-container>
                        <v-data-table :loading=loading loading-text="Loading... Please wait" :headers="reveivable_table_headers" :items="order_requests" :search="search">
                            <template v-slot:item.action="{ item }">
                                <v-icon class="mx-1" @click="showCurrentLocation(item)" v-if="item.delivery_date == new Date().toISOString().substr(0, 10)">fa-map-marker-alt</v-icon>
                                <v-dialog v-model="mapDialog" max-width="750px" min-heigth="500px">
                                    <v-card>
                                        <v-card-text>
                                            <v-container>
                                                <GmapMap
                                                    :options="mapOptions"
                                                    :center="logisticCoordinates"
                                                    :zoom="15"
                                                    map-type-id="terrain"
                                                    style="width: 100%; height: 50vh;">
                                                    <GmapMarker :position="logisticCoordinates" />
                                                </GmapMap>
                                            </v-container>
                                        </v-card-text>
                                    </v-card>
                                </v-dialog>
                                <v-icon class="mx-1" @click="retrieveOrderRequestInformation(item)">fa-info-circle</v-icon>
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
                            </template>
                        </v-data-table>
                    </v-container>
                </v-tab-item>
            </v-tabs-items>
                
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
            
        </v-card>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                mapDialog: false,
                logisticCoordinates: { lat: 6.9214, lng: 122.0790}, locationLastUpdatedAt: null, logisticBeingViewed: null,
                mapOptions: {
                    zoomControl: false, mapTypeControl: false, scaleControl: false,
                    streetViewControl: false, rotateControl: false, fullscreenControl: false, disableDefaultUi: true
                },

                tabModel: 'pending-tab', search: '',
                order_request_information_dialog: false, loading: false,
                table_headers: [
                    {text: 'Code', value: 'code', align: 'center'},
                    // {text: 'Supplier', value: 'supplier', align: 'center'},
                    {text: 'Date and Time', value: 'datetime', align: 'center'},
                    {text: 'Actions', value: 'action', align: 'center', sortable: false},
                ],

                reveivable_table_headers: [
                    {text: 'Code', value: 'code', align: 'center'},
                    {text: 'Delivery Date', value: 'delivery_date', align: 'center'},
                    // {text: 'Supplier', value: 'supplier', align: 'center'},
                    {text: 'Logistic', value: 'logistic', align: 'center'},
                    {text: 'Actions', value: 'action', align: 'center', sortable: false},
                ],

                order_requests: [],
                order_request_information: {supplier: null, total: 0, datetime: null, details: []},
            }
        },

        mounted() {
            this.retrieveOrderRequests('Pending')

            Echo.channel('logistic')
                .listen('UpdateLogisticLocation', (data) => {
                    if(data.logistic.code == this.currentLogisticBeingViewed.logistic_code) {
                        this.logisticCoordinates.lat = Number(data.logistic.latitude)
                        this.logisticCoordinates.lng = Number(data.logistic.longitude)
                    }
                })
        },

        methods: {
            showCurrentLocation(item) {
                this.currentLogisticBeingViewed = item
                this.logisticCoordinates.lat = Number(item.latitude)
                this.logisticCoordinates.lng = Number(item.longitude)
                this.mapDialog = true
            },

            retrieveOrderRequests(status) {
                this.loading = true
                this.order_requests = []
                axios.get('/api/order-request', { params: { status}})
                .then( response => {
                    this.order_requests = response.data.success.order_requests
                })
                .catch( error => {
                    toastr.error("Retrieve Order Requests Error")
                })
                .finally(() => { this.loading = false})
            },

            retrieveReceivables() {
                axios.get('/api/order-request/receivables')
                .then( response => {
                    this.order_requests = response.data.success.order_requests
                })
                .catch( error => {
                    toastr.error("Retrieve Receivables Error")
                })
            },

            retrieveOrderRequestInformation(order_request) {
                axios.get('/api/order-request/' + order_request.id)
                .then( response => {
                    this.order_request_information = response.data.success.order_request_information
                    this.order_request_information_dialog = true
                })
                .catch( error => {
                    toastr.error("Retrieve Order Request Information Error")
                })
            },

            cancelOrderRequest(order_request) {
                var itemDeletion = confirm('Are you sure you want to cancel order request?')
                if(itemDeletion == true) {
                     axios.delete('/api/order-request/' + order_request.id)
                    .then( response => {
                        const index = this.order_requests.indexOf(order_request)
                        this.order_requests.splice(index, 1)
                    })
                    .catch( error => {
                        toastr.error("Cancel Order Request Error")
                    })
                }
            }
        }
    }
</script>