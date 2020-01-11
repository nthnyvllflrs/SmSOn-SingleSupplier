<template>
    <v-container>
        <v-card>
            <v-card-title>
                Logs
                <v-spacer />
                <v-text-field
                    v-model="search"
                    append-icon="fa-search"
                    label="Search log"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table calculate-widths :headers="productTableHeaders" :items="logs" :search="search" />
        </v-card>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                search: '',
                productTableHeaders: [
                    { text: 'Date and Time', value: 'created_at' },
                    { text: 'Type', value: 'type' },
                    { text: 'Remarks', value: 'remarks' },
                ],
                logs: []
            }
        },

        mounted() {
            this.retrieveLogs()
        },

        methods: {
            retrieveLogs() {
                axios.get('/api/system-log')
                .then( response => {
                    this.logs = response.data.success.logs
                })
                .catch( error => {
                    toastr.error("Retrieve Logs Error")
                })
            }
        }
    }
</script>