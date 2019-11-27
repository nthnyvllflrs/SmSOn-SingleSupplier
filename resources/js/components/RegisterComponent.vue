<template>
    <v-content>
        <v-container fill-height align=center justify=center>
            <v-card class="mx-auto" width="600px">
                <v-container>
                    <v-card-title>
                        <span class="headline">Customer Registration</span>
                    </v-card-title>

                    <v-card-text>
                        <v-text-field color="orange darken-3" v-model="customer.username" label="Username"></v-text-field>
                        <v-text-field color="orange darken-3" v-model="customer.password" label="Password" type="password"></v-text-field>
                        <v-text-field color="orange darken-3" v-model="customer.password_confirmation" label="Password Confirmation" type="password"></v-text-field>
                        <v-text-field color="orange darken-3" v-model="customer.name" label="Fullname"></v-text-field>
                        <v-textarea color="orange darken-3" v-model="customer.address" label="Address"></v-textarea>
                        <v-text-field color="orange darken-3" v-model="customer.phone_number" label="Phone Number"></v-text-field>
                    </v-card-text>

                    <v-card-actions>
                        <div class="flex-grow-1"></div>
                        <v-btn dark rounded color="orange darken-3" class="px-10" @click="register" :loading="loading">Register</v-btn>
                    </v-card-actions>
                </v-container>
            </v-card>
        </v-container>
    </v-content>
</template>

<script>
export default {
    data() {
        return {
            loading: false,
            customer: {username: null, password: null, name: null, address: null, phone_number: null,}
        }
    },
    methods: {
        register() {
            this.loading = true
            axios.post('api/customer', {
                username: this.customer.username, password: this.customer.password, password_confirmation: this.customer.password,
                name: this.customer.name, address: this.customer.address, phone_number: this.customer.phone_number,
            })
            .then( response => {
                this.$router.push('signin')
            })
            .catch( error => { toastr.error("An Error Occurred")})
            .finally( x => { this.loading = false})
        }
    }
}
</script>
