import Vue from 'vue'
import VueRouter from 'vue-router'

import Landing from '../components/LandingComponent.vue'
import Login from '../components/LoginComponent.vue'
import Register from '../components/RegisterComponent.vue'

import Home from '../components/main/HomeComponent.vue'
import Suppliers from '../components/main/SupplierComponent.vue'
import SupplierProfile from '../components/main/SupplierProfileComponent.vue'
import Orders from '../components/main/OrderComponent.vue'
import Products from '../components/main/ProductComponent.vue'

Vue.use(VueRouter)

const routes = [
    { 
        path: '/', 
        name: 'landing', 
        component: Landing,
    },
    { 
        path: '/login', 
        name: 'login', 
        component: Login,
    },
    { 
        path: '/register', 
        name: 'register', 
        component: Register,
    },
    { 
        path: '/home', 
        name: 'Home', 
        component: Home,
        children: [
            {path: '/suppliers', name: 'suppliers', components: {home: Suppliers}},
            {path: '/suppliers/:name', name: 'supplier-profile', components: {home: SupplierProfile}},
            {path: '/products', name: 'products', components: {home: Products}},
            {path: '/orders', name: 'orders', components: {home: Orders}},
        ]
    },
]

const opts = {
    mode: 'history', 
    routes
}

export default new VueRouter(opts)