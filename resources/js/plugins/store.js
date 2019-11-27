import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    strict: true,
    state: {
        appName: 'Laravel',
        cart: [
            {name: "Product 1", supplier: "Uniliver", price: "Php 1200", quantity: 5}
        ],
    },
    getters: { // this.$tore.getters.getter
        appName: state => {
            return state.appName
        },
        cartItems: state => {
            return state.cart
        }
    }, 
    mutations: { // this.$tore.commit("mutation")
        changeAppName: (state, payload) => {
            state.appName += payload
        },
        addCartItem: (state, payload) => {
            state.cart.push(payload)
        }
    }, 
    actions: { // this.$tore.dispatch("action") // asynchronous tasks
        changeAppName: (context, payload) => {
            context.commit("changeAppName", payload)
        },
        addCartItem: (context, payload) => {
            context.commit('addCartItem', payload)
        }
    } 
})