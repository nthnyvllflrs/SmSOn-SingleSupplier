<template>
    <v-container>
        <v-card>
            <v-data-table :headers="dataTableHeaders" :items="manifests" show-expand single-expand expanded.sync>
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <v-toolbar-title class="headline">Manifests</v-toolbar-title>
                    </v-toolbar>
                </template>

                <template v-slot:expanded-item="{ item, headers }">
                    <td :colspan="headers.length">
                        <v-row align="center" justify="center">
                            <v-col cols=12 sm=6 md=3 v-for="(order_request, index) in item.order_requests" :key="index">
                                <v-card>
                                    <v-list-item two-line>
                                        <v-list-item-content>
                                            <v-list-item-title>{{ order_request.code }}</v-list-item-title>
                                            <v-list-item-subtitle>{{ order_request.customer }}, {{ order_request.address }}</v-list-item-subtitle>
                                        </v-list-item-content>
                                        <v-list-item-action v-if="item.delivery_date == new Date().toISOString().substr(0, 10)">
                                            <v-row no-gutters>
                                                <v-col cols=6>
                                                    <v-btn fab x-small color="primary" class="mx-1" @click="openMapDialog(order_request)">
                                                        <v-icon x-small>fa-map-marker-alt</v-icon>
                                                    </v-btn>
                                                    <v-dialog v-model="mapDialog" max-width="750">
                                                        <v-card>
                                                            <v-container>
                                                                <GmapMap
                                                                    :ref="'mapRef' + order_request.id"
                                                                    :options="mapOptions"
                                                                    :center="customerCoordinates"
                                                                    :zoom="15"
                                                                    map-type-id="terrain"
                                                                    style="width: 100%; height: 50vh;">
                                                                    <!-- <GmapMarker :position="logisticCoordinates" /> -->
                                                                    <GmapMarker :position="customerCoordinates" />
                                                                </GmapMap>
                                                                <v-btn block color="success" class="mt-3" @click="getDirection(order_request)">Show Directions</v-btn>
                                                                <div class="mt-3 text-center" id="directionPanel" width="100%"></div>
                                                            </v-container>
                                                        </v-card>
                                                    </v-dialog>
                                                </v-col>
                                                <v-col cols=6>
                                                    <v-btn fab x-small color="success" class="mx-1" @click="updatedRequestStatus(order_request)" :disabled="order_request.status == 'Delivered'">
                                                        <v-icon x-small>fa-check</v-icon>
                                                    </v-btn>
                                                </v-col>
                                            </v-row>
                                        </v-list-item-action>
                                    </v-list-item>
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
    import {gmapApi} from 'vue2-google-maps'
    export default {
        data () {
            return {
                mapDialog: false,
                dataTableHeaders: [
                    { text: 'Code', value: 'code', align: 'center'},
                    { text: 'Logistic', value: 'logistic', align: 'center'},
                    { text: 'Delivery Date', value: 'delivery_date', align: 'center'},
                    { value: 'data-table-expand'},
                ],
                manifests: [], 

                formErrors: { supplier_id: null, logistic_id: null, delivery_date: null},
                
                logisticCoordinates: { lat: 6.9214, lng: 122.0866},
                customerCoordinates: { lat: 6.9161, lng: 122.0866},
                mapOptions: {
                    zoomControl: false, mapTypeControl: false, scaleControl: false,
                    streetViewControl: false, rotateControl: false, fullscreenControl: false, disableDefaultUi: true
                },

                mapRef: null, directionsService: null, directionsDisplay: null,
            }
        },

        watch: {
            mapDialog (val) {
                val || this.resetMapDirections()
            },
        },

        computed: {
            google: gmapApi
        },

        mounted() {
            this.retrieveManifests()   
            this.ifLogistic() 
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

            updatedRequestStatus(order_request) {
                axios.put('/api/order-request/' + order_request.id + '/status', {
                    status: 'Delivered'
                })
                .then( response => {
                    order_request.status = 'Delivered'
                })
                .catch(() => {})
                .finally(() => {
                    console.log(order_request.status)
                })
            },

            ifLogistic() {
                if(sessionStorage.getItem('user-role') == 'Logistic') {
                    window.locationInterval = setInterval(() => this.getLogisticGeolocation(), 10000)
                }
            },

            getLogisticGeolocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(this.setLogisticGeolocation);
                } else {
                    window.clearInterval(window.locationInterval);
                    alert("Geolocation is not supported by this browser.");
                }
            },

            setLogisticGeolocation(position) {
                var logisticGeolocationLatitude = position.coords.latitude
                var logisticGeolocationLongitude = position.coords.longitude
                // console.log(logisticGeolocationLatitude, logisticGeolocationLongitude)
                axios.put('api/logistic/' + sessionStorage.getItem('user-id') + '/update-location', {
                    latitude: logisticGeolocationLatitude, longitude: logisticGeolocationLongitude,
                })
                .then( response => { /** console.log(response.data) **/}).catch( error => { toastr.error("An Error Occurred")})
            },

            openMapDialog(order_request) {
                this.mapDialog = true
                this.customerCoordinates = { lat: Number(order_request.latitude), lng: Number(order_request.longitude)}
            },

            resetMapDirections() {
                this.logisticCoordinates = { lat: 6.9214, lng: 122.079}
                this.directionsDisplay.setMap(null);
                this.directionsDisplay.setPanel(null);
            },

            getDirection(order_request) {
                this.mapRef = 'mapRef' + order_request.id

                this.directionsService = this.google && new google.maps.DirectionsService;
                this.directionsDisplay = this.google && new google.maps.DirectionsRenderer;
                this.directionsDisplay.setMap(this.$refs[this.mapRef][0].$mapObject);
                this.directionsDisplay.setPanel(document.getElementById('directionPanel'));

                //google maps API's direction service
                function calculateAndDisplayRoute(directionsService, directionsDisplay, start, destination) {
                    directionsService.route({
                        origin: start, destination: destination, travelMode: 'DRIVING'
                    }, 
                    function(response, status) {
                        if (status === 'OK') {
                            directionsDisplay.setDirections(response)
                        } else {
                            window.alert('Directions request failed due to ' + status);
                        }
                    });
                }

                calculateAndDisplayRoute(this.directionsService, this.directionsDisplay, this.logisticCoordinates, this.customerCoordinates);
            },
        }
    }
</script>