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
                                        <v-list-item-action>
                                            <v-row no-gutters>
                                                <v-col cols=6>
                                                    <v-dialog v-model="dialog" max-width="750">
                                                        <template v-slot:activator="{ on }">
                                                            <v-btn fab x-small color="primary" class="mx-1" v-on="on">
                                                                <v-icon x-small>fa-map-marker-alt</v-icon>
                                                            </v-btn>
                                                        </template>

                                                        <v-card>
                                                            <v-container>
                                                                <GmapMap
                                                                    ref="mapRef"
                                                                    :options="mapOptions"
                                                                    :center="logisticCoordinates"
                                                                    :zoom="15"
                                                                    map-type-id="terrain"
                                                                    style="width: 100%; height: 50vh;">
                                                                    <GmapMarker :position="logisticCoordinates" />
                                                                </GmapMap>
                                                                <v-btn block color="success" class="mt-3" @click="getDirection()">Show Directions</v-btn>
                                                                <div class="mt-3 text-center" id="directionPanel" width="100%"></div>
                                                            </v-container>
                                                        </v-card>
                                                    </v-dialog>
                                                </v-col>
                                                <v-col cols=6>
                                                    <v-btn fab x-small color="success" class="mx-1">
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
                dialog: false,
                dataTableHeaders: [
                    { text: 'Code', value: 'code', align: 'center'},
                    { text: 'Logistic', value: 'logistic', align: 'center'},
                    { text: 'Delivery Date', value: 'delivery_date', align: 'center'},
                    { value: 'data-table-expand'},
                ],
                manifests: [], 

                formErrors: { supplier_id: null, logistic_id: null, delivery_date: null},
                
                logisticCoordinates: { lat: -7.824374, lng: 110.262371},
                mapOptions: {
                    zoomControl: false, mapTypeControl: false, scaleControl: false,
                    streetViewControl: false, rotateControl: false, fullscreenControl: false, disableDefaultUi: true
                },

                coords: { lat: -7.824374, lng: 110.262371},
                destination: { lat: -7.925665, lng: 110.298115}
            }
        },

        computed: {
            google: gmapApi
        },

        mounted() {
            this.retrieveManifests()    
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

            sample() {
                console.log(this.google)
            },

            getDirection() {
                var directionsService = this.google && new google.maps.DirectionsService;
                var directionsDisplay = this.google && new google.maps.DirectionsRenderer;
                directionsDisplay.setMap(this.$refs.mapRef[0].$mapObject);
                directionsDisplay.setPanel(document.getElementById('directionPanel'));

                //google maps API's direction service
                function calculateAndDisplayRoute(directionsService, directionsDisplay, start, destination) {
                    directionsService.route({
                        origin: start, destination: destination, travelMode: 'DRIVING'
                    }, 
                    function(response, status) {
                        if (status === 'OK') {
                            directionsDisplay.setDirections(response);
                        } else {
                            window.alert('Directions request failed due to ' + status);
                        }
                    });
                }

                calculateAndDisplayRoute(directionsService, directionsDisplay, this.coords, this.destination);
            },
        }
    }
</script>