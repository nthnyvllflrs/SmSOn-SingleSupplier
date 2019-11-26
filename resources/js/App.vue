<template>
    <div id="app">
        <app-header />
        <router-view />
    </div>
</template>

<script>
    import Header from './components/HeaderComponent.vue'
    export default {
        name: 'app',
        components: {
            'app-header': Header,
        }
    }
</script>