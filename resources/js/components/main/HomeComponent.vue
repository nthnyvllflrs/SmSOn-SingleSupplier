<template>
    <div>
        <v-navigation-drawer app clipped right v-model="cart" width="300">
            <v-list>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title class="subtitle-2 font-weight-bold">Selected Items</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item three-line v-for="(item, index) in cartItems" :key="index">
                    <v-list-item-content>
                        <v-list-item-title>
                            <span class="font-weight-bold">{{item.name}}</span>
                            <v-chip small class="px-2" color="primary">{{item.quantity}}</v-chip>
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            <v-row no-gutters>
                                <v-col cols=6>
                                    <span class="body-2 font-weight-medium">{{item.supplier}}</span>
                                </v-col>
                                <v-col cols=6>
                                    <span class="body-2 font-weight-medium">{{item.price}}</span>
                                </v-col>
                            </v-row>
                        </v-list-item-subtitle>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-btn small icon>
                            <v-icon small>fa-edit</v-icon>
                        </v-btn>
                        <v-btn small icon color="error">
                            <v-icon small>fa-times</v-icon>
                        </v-btn>
                    </v-list-item-action>
                </v-list-item>
            </v-list>
            <template v-slot:append>
                <v-container>
                    <v-btn block color="primary">Send Requests</v-btn>
                </v-container>
            </template>
        </v-navigation-drawer>

        <v-app-bar app clipped-right>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title class="username-letter-spacing">
                <span class="title font-weight-bold title-letter-spacing">SMS ON</span>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon>
                <v-icon>fa-user-circle</v-icon>
            </v-btn>
            <v-btn icon>
                <v-icon>fa-bell</v-icon>
            </v-btn>
            <v-btn icon>
                <v-icon @click.stop="cart = !cart">fa-shopping-cart</v-icon>
            </v-btn>
        </v-app-bar>

        <v-navigation-drawer app v-model="drawer" width="250">
            <v-list>
                <v-list-item to="/orders">
                    <v-list-item-avatar>
                        <v-icon>fa-list</v-icon>
                    </v-list-item-avatar>
                    <v-list-item-content>
                        <v-list-item-title class="subtitle-2 font-weight-bold">My Order Requests</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item to="/products">
                    <v-list-item-avatar>
                        <v-icon >fa-cube</v-icon>
                    </v-list-item-avatar>
                    <v-list-item-content>
                        <v-list-item-title class="subtitle-2 font-weight-bold">Products</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item to="/suppliers">
                    <v-list-item-avatar>
                        <v-icon >fa-boxes</v-icon>
                    </v-list-item-avatar>
                    <v-list-item-content>
                        <v-list-item-title class="subtitle-2 font-weight-bold">Suppliers</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
            <template v-slot:append>
                <v-list>
                    <v-list-item to="/home">
                        <v-list-item-avatar>
                            <v-icon>fa-sign-out-alt</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Logout</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </template>
        </v-navigation-drawer>

        <v-content>
            <v-container>
                <router-view name="home" />
            </v-container>
        </v-content>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
    export default {
        data() {
            return {
                drawer: true,
                cart: false,
            }
        },
        computed: {
            ...mapGetters([
                'cartItems'
            ])
        },
    }
</script>

<style scoped>
    .title-letter-spacing {
        letter-spacing: 4px !important; 
    }

    .username-letter-spacing {
        letter-spacing: 2px !important; 
    }
</style>