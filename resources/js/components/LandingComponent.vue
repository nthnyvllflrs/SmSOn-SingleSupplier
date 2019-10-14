<template>
    <div>
        <h1>{{ message }}</h1>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                message: null,
            }
        },
        mounted() {
            Echo.channel('home')
                .listen('NewMessage', (e) => {
                    this.message = e.message
                })
        },
    }
</script>