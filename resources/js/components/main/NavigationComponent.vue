<template>
    <div>
        <v-app-bar app clipped-right>
            <v-app-bar-nav-icon @click.stop="sideNavigationBar = !sideNavigationBar"></v-app-bar-nav-icon>
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
                <v-icon>fa-shopping-cart</v-icon>
            </v-btn>
        </v-app-bar>

        <v-navigation-drawer app v-model="sideNavigationBar" width="250">
            <v-list>
                <!-- Administrator -->
                <template v-if="userRole == 'Administrator'">
                    <v-list-item to="/customers">
                        <v-list-item-avatar>
                            <v-icon>fa-users</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Customers</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item to="/suppliers">
                        <v-list-item-avatar>
                            <v-icon>fa-boxes</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Suppliers</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
                <!-- Customer -->
                <template v-if="userRole == 'Customer'">
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-list</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Order Requests</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-box</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Products</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-boxes</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Suppliers</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
                <!-- Supplier -->
                <template v-if="userRole == 'Supplier'">
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-list</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Order Requests</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-box</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Products</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-truck</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Logistics</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-icon>fa-clipboard-list</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 font-weight-bold">Manifest</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
            </v-list>
            <template v-slot:append>
                <v-list>
                    <v-list-item @click="logout()">
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
                <router-view name="content" />
            </v-container>
        </v-content>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                userRole: sessionStorage.getItem('user-role'),
                sideNavigationBar: true,
            }
        },

        methods: {
            logout() {
                axios.get('api/logout')
                .then(() => {})
                .catch(() => {})
                .finally(() => {
                    sessionStorage.removeItem('user-token')
                    sessionStorage.removeItem('user-role')
                    sessionStorage.removeItem('user-information-id')
                    sessionStorage.clear()
                    this.$router.push('/login')
                    toastr.success("Logout Successfull")
                })
            }   
        }
    }
</script>