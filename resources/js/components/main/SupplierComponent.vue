<template>
    <v-container>
        <v-row>
            <v-col cols=12 sm=12 md=3 lg=2>
                <span class="headline">Suppliers List</span>
            </v-col>
            <v-col cols=12 sm=12 md=6 lg=4 offset-sm=0 offset-md=3 offset-lg=6>
                <v-text-field label="Search Supplier" append-icon="fa-search"/>
            </v-col>
            <!-- <v-col cols=12 sm=12 md=6 lg=4 offset-md=6 offset-lg=8>
                <v-text-field label="Search Supplier" append-icon="fa-search"/>
            </v-col> -->
        </v-row>
        <v-row>
            <v-col cols=12 sm=12 md=6 lg=4 v-for="(supplier, index) in suppliers" v-bind:key="index">
                <v-card>
                    <v-card-title class="title">{{supplier.name}}</v-card-title>
                    <v-card-text>
                        <span class="subtitle-2">{{supplier.location}}</span>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn to="/suppliers/uniliver" color="primary">See Profile & Products</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                suppliers: [
                    {name: "Supplier 1", description: "Supplier 1 Description", location: "Supplier 1 Location"},
                    {name: "Supplier 2", description: "Supplier 2 Description", location: "Supplier 2 Location"},
                    {name: "Supplier 3", description: "Supplier 3 Description", location: "Supplier 3 Location"},
                ]
            }
        }
    }
</script>