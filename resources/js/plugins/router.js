import Vue from 'vue'
import VueRouter from 'vue-router'

import Login from '../components/LoginComponent.vue'
import Navigation from '../components/main/NavigationComponent.vue'

import Customer from '../components/administrator/CustomerComponent.vue'
import Supplier from '../components/main/SupplierComponent.vue'
import OrderRequests from '../components/main/OrderRequestComponent.vue'
import Products from '../components/main/ProductComponent.vue'
import Logistics from '../components/supplier/LogisticComponent.vue'
import Manifests from '../components/supplier/ManifestComponent.vue'


Vue.use(VueRouter)

const routes = [
    { 
        beforeEnter(to, from, next) {
            if (!sessionStorage.getItem('user-token')) {
                next()
            } else {
                next({name: 'navigation'})
            }
        },
        path: '/login', 
        name: 'login', 
        component: Login,
    },
    {
        beforeEnter(to, from, next) {
            if (sessionStorage.getItem('user-token')) {
                next()
            } else {
                next({name: 'login'})
            }
        },
        path: '/navigation',
        name: 'navigation',
        component: Navigation,
        children: [
            { path: '/customers', name: 'customers', components: { content: Customer } },
            { path: '/suppliers', name: 'suppliers', components: { content: Supplier } },
            { path: '/order-requests', name: 'order-requests', components: {content: OrderRequests}},
            { path: '/products', name: 'products', components: {content: Products}},
            { path: '/logistics', name: 'logistics', components: { content: Logistics } },
            { path: '/manifests', name: 'manifests', components: { content: Manifests } },
        ]
    }
]

const opts = {
    mode: 'history', 
    routes
}

export default new VueRouter(opts)