<template>
    <v-container fill-height align=center justify=center>
        <v-card class="mx-auto" max-width="600" >
            <v-img src="../images/background.jpg" gradient="to bottom, rgba(1,87,155,.4), rgba(1,87,155,.5)" aspect-ratio="3"/>
            <v-card-text>
                <v-container>
                    <v-row justify="center" align="center">
                        <v-col cols=12 md=2>
                            Username
                        </v-col>
                        <v-col cols=12 md=10>
                            <v-text-field v-model="username" color="orange darken-3" label="Enter Username" name="username" id="username" type="text" :error-messages="formErrors.username"/>
                        </v-col>
                        <v-col cols=12 md=2>
                            Password
                        </v-col>
                        <v-col cols=12 md=10>
                            <v-text-field v-model="password" color="orange darken-3" label="Enter Password" id="password"  name="password" type="password" :error-messages="formErrors.password"/>
                        </v-col>
                        <v-col cols=12 md=10 offset-md=2>
                            <v-btn color="orange darken-3" min-width="200px" :loading="loading" dark rounded @click="login">Sign in</v-btn>
                            <span class="mx-3 error--text font-weight-bold" v-if="errorMessage"><v-icon color="error" class="mx-2">fa-exclamation-circle</v-icon>{{errorMessage}}</span>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            loading: false,
            username: null, password: null,
            errorMessage: null,
            formErrors: { username: null, password: null},
        }
    },
    methods: {
        login() {
            this.loading = true
            axios.post('api/login', {
                username: this.username, password: this.password
            })
            .then( response => {
                var data = response.data.success
                sessionStorage.setItem('user-token', data.token)
                sessionStorage.setItem('user-role', data.role)
                sessionStorage.setItem('user-information-id', data.information_id)
                this.$router.push('navigation')
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
            .finally( x => {this.loading = false})
        }
    }
}
</script>
