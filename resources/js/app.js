require('./bootstrap');

window.Vue = require('vue');

import App from './App.vue'
import { store } from './plugins/store.js'
import vuetify from './plugins/vuetify.js'
import {router} from './plugins/router.js'

import './plugins/google-maps.js'

Vue.filter('substr', function (value, start, end) {
    return value.substring(start, end)
})

const app = new Vue({
    vuetify, router, store,
    render: h => h(App),
}).$mount('#app')