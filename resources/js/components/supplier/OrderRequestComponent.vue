<template>
    <v-container>
        <v-card>
            <v-tabs grow>
                <v-tab>Pending</v-tab>
                <v-tab>Approved</v-tab>
                <v-tab>Receivable</v-tab>
                <v-tab>Delivered</v-tab>

                <!-- Pending Orders -->
                <v-tab-item>
                    <v-container>
                        <v-data-table :headers="actionHeaders"></v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Approved Orders -->
                <v-tab-item>
                    <v-container>
                        <v-data-table :headers="noActionHeaders"></v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Reveivable Orders -->
                <v-tab-item>
                    <v-container>
                        <v-data-table :headers="actionHeaders"></v-data-table>
                    </v-container>
                </v-tab-item>

                <!-- Delivered Orders -->
                <v-tab-item>
                    <v-container>
                        <v-data-table :headers="noActionHeaders"></v-data-table>
                    </v-container>
                </v-tab-item>
            </v-tabs>
        </v-card>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                actionHeaders: [
                    {text: 'Code', value: 'code', align: 'center'},
                    {text: 'Supplier', value: 'supplier', align: 'center'},
                    {text: 'Date and Time', value: 'datetime', align: 'center'},
                    {text: 'Actions', align: 'center', sortable: false},
                ],
                noActionHeaders: [
                    {text: 'Code', value: 'code', align: 'center'},
                    {text: 'Supplier', value: 'supplier', align: 'center'},
                    {text: 'Date and Time', value: 'datetime', align: 'center'},
                ],
            }
        }
    }
</script>