const MODULES = {
    customers: true, suppliers: true, order_requests: true, products: true, logistics: true, manifests: true, logs: true, default: null
}
const PERMISSIONS = {
    Administrator: {
        ...MODULES,
        // order_requests: false, products: false, logistics: false, manifests: false, 
        default: 'customers'
    },
    Customer: {
        ...MODULES,
        customers: false, logistics: false, manifests: false, logs: false,
        default: 'products'
    },
    Supplier: {
        ...MODULES,
        customers: false, suppliers: false, logs: false,
        default: 'order_requests'
    },
    Logistic: {
        ...MODULES,
        customers: false, suppliers: false, order_requests: false, products: false, logistics: false, logs: false,
        default: 'manifests'
    }
}


import Vue from 'vue'
import VueRouter from 'vue-router'

import Login from '../components/LoginComponent.vue'
import Navigation from '../components/main/NavigationComponent.vue'

import Customer from '../components/administrator/CustomerComponent.vue'
import Supplier from '../components/main/SupplierComponent.vue'
import OrderRequests from '../components/main/OrderRequestComponent.vue'
import Products from '../components/main/ProductComponent.vue'
import Logistics from '../components/supplier/LogisticComponent.vue'
import Manifests from '../components/main/ManifestComponent.vue'
import Logs from '../components/main/SystemLogComponent.vue'


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
            { path: '/order-requests', name: 'order_requests', components: {content: OrderRequests} },
            { path: '/products', name: 'products', components: {content: Products} },
            { path: '/logistics', name: 'logistics', components: { content: Logistics } },
            { path: '/manifests', name: 'manifests', components: { content: Manifests } },
            { path: '/logs', name: 'logs', components: { content: Logs } },
        ],
    }
]

const opts = {
    mode: 'history', 
    routes
}

export const router = new VueRouter(opts)

router.beforeEach((to, from, next) => {
    if (sessionStorage.getItem('user-token')) {
        var userRole = sessionStorage.getItem('user-role')
        if (!PERMISSIONS[userRole][to.name]) {
            next({name: PERMISSIONS[userRole]['default']})
        } else { next()}
    } else { next()}
})