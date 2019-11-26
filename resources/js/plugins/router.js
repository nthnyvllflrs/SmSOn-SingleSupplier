import Vue from 'vue'
import VueRouter from 'vue-router'

import Landing from '../components/LandingComponent.vue'

Vue.use(VueRouter)

const routes = [
    { 
        path: '/', 
        name: 'landing', 
        component: Landing,
    },

    { 
        path: '/landing', 
        name: 'landing', 
        component: Landing,
    },
]

const opts = {
    mode: 'history', 
    routes
}

export default new VueRouter(opts)