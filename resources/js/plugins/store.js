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
                    var newQuantity = Number(product.quantity) + Number(payload.quantity)
                    if (newQuantity > payload.stock.available) {
                        toastr.info("Entered product quantity exceeds available product stock or is zero (0).")
                    } else {
                        product.quantity = Number(product.quantity) + Number(payload.quantity)
                        product.total = Number(product.quantity) * Number(product.price)
                        toastr.success("Product Added")
                    }
                    productDoesntExist = false
                }
            })
            if (productDoesntExist ) {
                payload.total = Number(payload.quantity) * Number(payload.price)
                state.cart.push(payload)
                toastr.success("Product Added")
            }
        },
        removeCartProduct: (state, payload) => {
            if (state.cart.includes(payload)) {
                var index = state.cart.indexOf(payload)
                state.cart.splice(index, 1)
            }
        },
        resetCart: (state) => {
            state.cart = []
        }
    }, 

    actions: { // this.$tore.dispatch("action") // asynchronous tasks
        addCartProduct: (context, payload) => {
            context.commit('addCartProduct', payload)
        },
        removeCartProduct: (context, payload) => {
            context.commit('removeCartProduct', payload)
        },
        resetCart: (context) => {
            context.commit('resetCart')
        }
    } 
})