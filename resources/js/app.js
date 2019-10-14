require('./bootstrap');

window.Vue = require('vue');

import vuetify from './plugins/vuetify.js'
import router from './plugins/router.js'

const app = new Vue({
    vuetify, router,
}).$mount('#app')