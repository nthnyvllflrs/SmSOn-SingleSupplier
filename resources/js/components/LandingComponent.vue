<template>
    <div>
        <v-container>
            <v-btn small rounded color="success" to="/register">Register</v-btn>
            <v-btn small rounded color="success" to="/login">Login</v-btn>
        </v-container>
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'

    export default {
        data() {
            return {
                message: "Landing Component",
            }
        },
        mounted() {
            this.changeAppName(" Changed")
        },
        computed: {
            ...mapGetters([
                'appName'
            ])
        },
        methods: {
            ...mapActions([
                'changeAppName'
            ])
        }
    }
</script>