import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        cart: [],
    },

    getters: { // this.$tore.getters.getter
        cartProducts: state => {
            return state.cart
        },
        cartTotal: state => {
            return state.cart.reduce((accumulator, currentValue) => Number(accumulator) + (Number(currentValue.quantity) * Number(currentValue.price)) , 0.0);
        }
    }, 

    mutations: { // this.$tore.commit("mutation")
        addCartProduct: (state, payload) => {
            var productDoesntExist = true
            state.cart.forEach(product => {
                if (product.product_id == payload.product_id) {
                    product.quantity = Number(product.quantity) + Number(payload.quantity)
                    productDoesntExist = false
                }
            })
            if (productDoesntExist) {
                state.cart.push(payload)
            }
        },
        removeCartProduct: (state, payload) => {
            if (state.cart.includes(payload)) {
                var index = state.cart.indexOf(payload)
                state.cart.splice(index, 1)
            }
        }
    }, 

    actions: { // this.$tore.dispatch("action") // asynchronous tasks
        addCartProduct: (context, payload) => {
            context.commit('addCartProduct', payload)
        },
        removeCartProduct: (context, payload) => {
            context.commit('removeCartProduct', payload)
        }
    } 
})