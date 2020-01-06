<template>
    <v-container>
        <v-card>
            <v-data-table :headers="logisticTableHeaders" :items="logistics" :search="search">
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <v-toolbar-title class="headline">Logistics</v-toolbar-title>
                        <div class="flex-grow-1"></div>
                        <v-dialog v-model="dialog" max-width="500px" persistent>
                            <template v-slot:activator="{ on }">
                                <v-btn small color="primary" v-on="on">
                                    <v-icon small left>fa-plus</v-icon> Add Logistic
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
                                            <v-text-field :error-messages="formErrors.username" v-model="editedLogisticInformation.username" label="Username" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.password" v-model="editedLogisticInformation.password" label="Password" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.password_confirmation" v-model="editedLogisticInformation.password_confirmation" label="Password Confirmation" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.name" v-model="editedLogisticInformation.name" label="Name" />
                                        </v-col>
                                        <v-col cols=12>
                                            <v-text-field :error-messages="formErrors.contact_number" v-model="editedLogisticInformation.contact_number" label="Contact Number" />
                                        </v-col>
                                    </v-row>
                                </v-card-text>

                                <v-card-actions>
                                    <div class="flex-grow-1" />
                                    <v-btn @click="cancel()">Cancel</v-btn>
                                    <v-btn class="px-8" color="primary" @click="saveLogistic()">Save</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>
                <template v-slot:item.action="{ item }">
                    <v-icon class="mx-1" @click="openMapDialog(item)">fa-map-marker-alt</v-icon>
                    <v-dialog v-model="mapDialog" max-width="750">
                        <v-card>
                            <v-container>
                                <GmapMap
                                    :ref="'mapRef' + item.id"
                                    :options="mapOptions"
                                    :center="logisticCoordinates"
                                    :zoom="15"
                                    map-type-id="terrain"
                                    style="width: 100%; height: 50vh;">
                                    <GmapMarker :position="logisticCoordinates" />
                                </GmapMap>
                            </v-container>
                        </v-card>
                    </v-dialog>
                    <v-icon class="mx-1" @click="editLogistic(item)">fa-pen</v-icon>
                    <v-icon class="mx-1" @click="deleteLogistic(item)">fa-trash-alt</v-icon>
                </template>
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                mapOptions: {
                    zoomControl: false, mapTypeControl: false, scaleControl: false,
                    streetViewControl: false, rotateControl: false, fullscreenControl: false, disableDefaultUi: true
                },

                dialog: false, informationDialog: false,
                loading: false, search: '', editedIndex: -1,
                logisticTableHeaders: [
                    { text: 'Code', value: 'code' },
                    { text: 'Username', value: 'username' },
                    { text: 'Name', value: 'name' },
                    { text: 'Contact Number', value: 'contact_number' },
                    { text: 'Actions', value: 'action', sortable: false },
                ],
                logistics: [],

                defaultLogisticInformation: { username: null, name: null, contact_number: null, password: null, password_confirmation: null, supplier_id: sessionStorage.getItem('user-information-id')},
                editedLogisticInformation: { username: null, name: null, contact_number: null, password: null, password_confirmation: null, supplier_id: sessionStorage.getItem('user-information-id')},
                formErrors: { username: null, name: null, contact_number: null, password: null, password_confirmation: null},

                mapDialog: false, currentLogisticBeingViewed: {},
                logisticCoordinates: { lat: 6.9214, lng: 122.079},
            }
        },
        mounted() {
            this.retrieveLogistics()

            Echo.channel('logistic')
                .listen('UpdateLogisticLocation', (data) => {
                    if(data.logistic.code == this.currentLogisticBeingViewed.code) {
                        this.logisticCoordinates.lat = Number(data.logistic.latitude)
                        this.logisticCoordinates.lng = Number(data.logistic.longitude)
                    }
                })
        },
        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'New Logistic' : 'Edit Logistic'
            },
        },
        methods: {
            openMapDialog(item) {
                this.mapDialog = true
                this.currentLogisticBeingViewed = item
                this.logisticCoordinates.lat = Number(item.latitude)
                this.logisticCoordinates.lng = Number(item.longitude)
            },

            retrieveLogistics() {
                axios.get('/api/logistic')
                .then( response => {
                    this.logistics = response.data.success.logistics
                })
                .catch( error => {
                    toastr.error("An Error Occurred")
                })
            },

            deleteLogistic(logistic) {
                var logisticDeletion = confirm('Are you sure you want to delete this Logistic?')
                if(logisticDeletion == true) {
                    axios.delete('api/logistic/' + logistic.id)
                    .then( response => {
                        const index = this.logistics.indexOf(logistic)
                        this.logistics.splice(index, 1)
                        toastr.success("Logistic Deleted")
                    })
                    .catch( error => { alert(error)})
                }
            },

            editLogistic(logistic) {
                this.editedIndex = this.logistics.indexOf(logistic)
                this.editedLogisticInformation = Object.assign({}, logistic)
                this.dialog = true
            },
            
            cancel() {
                this.dialog = false; this.informationDialog = false
                setTimeout(() => {
                    this.formErrors = { username: null, name: null, address: null, contact_number: null, password: null, password_confirmation: null}
                    this.editedLogisticInformation = Object.assign({}, this.defaultLogisticInformation)
                    this.editedIndex = -1
                }, 500)
            },

            saveLogistic() {
                if (this.editedIndex > -1) {
                    this.updateLogistic()
                } else {
                    this.createLogistic()
                }
            },

            createLogistic() {
                this.loading = true
                
                axios.post('/api/logistic', {
                    ...(_.omit(this.editedLogisticInformation, 'code'))
                })
                .then( response => {
                    this.editedLogisticInformation.code = response.data.success.logistic.code
                    this.editedLogisticInformation.id = response.data.success.user.id
                    this.logistics.push(this.editedLogisticInformation)
                    this.cancel()
                    toastr.success("Logistics Created")
                    console.log(this.editedLogisticInformation.id)
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

            updateLogistic() {
                this.loading = true
                _.omit(this.editedLogisticInformation, 'code')
                axios.put('/api/logistic/' + this.editedLogisticInformation.id, {
                    ...(_.omit(this.editedLogisticInformation, 'code'))
                })
                .then( response => {
                    Object.assign(this.logistics[this.editedIndex], this.editedLogisticInformation)
                    this.cancel()
                    toastr.success("Logistics Updated")
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