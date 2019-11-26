<template>
    <div>
        {{ message }}, {{ appName }}
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