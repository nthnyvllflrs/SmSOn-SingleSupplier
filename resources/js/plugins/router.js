import Vue from 'vue'
import VueRouter from 'vue-router'

// import Home from '../components/main/HomeComponent.vue'
// import Landing from '../components/LandingComponent.vue'
// import Register from '../components/RegisterComponent.vue'
// import Suppliers from '../components/main/SupplierComponent.vue'
// import SupplierProfile from '../components/main/SupplierProfileComponent.vue'
// import Orders from '../components/main/OrderComponent.vue'
// import Products from '../components/main/ProductComponent.vue'

import Login from '../components/LoginComponent.vue'
import Navigation from '../components/main/NavigationComponent.vue'

// Administrator
import Customer from '../components/administrator/CustomerComponent.vue'
import Supplier from '../components/administrator/SupplierComponent.vue'

// Suppliers
import SupplierProducts from '../components/supplier/ProductComponent.vue'
import Logistics from '../components/supplier/LogisticComponent.vue'

// Customers
import Products from '../components/customer/ProductComponent.vue'

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
            // Administrator
            { path: '/customers', name: 'customers', components: {content: Customer}},
            { path: '/suppliers', name: 'suppliers', components: { content: Supplier } },
            
            // Suppliers
            { path: '/supplier-products', name: 'supplier-products', components: {content: SupplierProducts}},
            { path: '/logistics', name: 'logistics', components: { content: Logistics } },
            
            // Customers
            { path: '/products', name: 'products', components: {content: Products}},
        ]
    }
]

const opts = {
    mode: 'history', 
    routes
}

export default new VueRouter(opts)