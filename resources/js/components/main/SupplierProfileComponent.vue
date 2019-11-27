<template>
    <v-container>
        <v-row>
            <v-col cols=12>
                <v-card>
                    <v-card-title class="header">Uniliver Inc.</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols=12 sm=12 md=6 lg=6>
                                <span class="subtitle-2">Description: Lorem ipsum dolor sit amet, consectetur adipisicing elit. Totam amet incidunt esse voluptatem? Quia, illum aliquam enim fugiat magnam placeat eum, minima architecto, minus voluptas aliquid.</span>
                            </v-col>
                            <v-col cols=12 sm=12 md=6 lg=6>
                                <span class="subtitle-2">Location: Lorem, ipsum dolor sit amet consectetur adipisicing elit. Eligendi, nesciunt.</span>
                            </v-col>
                            <v-col cols=12 sm=12 md=6 lg=6>
                                <span class="subtitle-2">Product Count: 6</span>
                            </v-col>
                            <v-col cols=12 sm=12 md=6 lg=6>
                                <span class="subtitle-2">Order Request This Month: 50</span>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols=12 sm=12 md=6 lg=4 offset-md=4 offset-lg=8>
                <v-text-field label="Search Product" append-icon="fa-search"/>
            </v-col>
            <v-col cols=12 sm=12 md=6 lg=4 v-for="(product, index) in products" v-bind:key="index">
                <v-card>
                    <v-card-title>
                        <span class="title">{{product.name}}</span><span class="caption ml-2">({{product.code}})</span>
                        <v-spacer></v-spacer>
                        <span class="subtitle-2">{{product.price}}</span>
                        </v-card-title>
                    <v-card-text class="body-2">
                        <v-row no-gutters>
                            <v-col cols=12>
                                Discription: {{product.description}}
                            </v-col>
                            <v-col>
                                Unit: {{product.unit}}
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn color="primary">Select Item</v-btn>
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
                products: [
                    {code: "PRD-ULR-0110", name: "Product 1", description: "Product 1 Description", unit:"Box", price: "Php 1,000.00", supplier: "Uniliver Inc."},
                    {code: "PRD-ULR-0110", name: "Product 2", description: "Product 2 Description", unit:"Box", price: "Php 2,000.00", supplier: "Uniliver Inc."},
                    {code: "PRD-ULR-0110", name: "Product 3", description: "Product 3 Description", unit:"Box", price: "Php 3,000.00", supplier: "Uniliver Inc."},
                    {code: "PRD-ULR-0110", name: "Product 4", description: "Product 4 Description", unit:"Box", price: "Php 4,000.00", supplier: "Uniliver Inc."},
                    {code: "PRD-ULR-0110", name: "Product 5", description: "Product 5 Description", unit:"Box", price: "Php 5,000.00", supplier: "Uniliver Inc."},
                    {code: "PRD-ULR-0110", name: "Product 6", description: "Product 6 Description", unit:"Box", price: "Php 6,000.00", supplier: "Uniliver Inc."},
                ]
            }
        },
        mounted() {
            
        }
    }
</script>