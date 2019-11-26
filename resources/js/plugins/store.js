import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    strict: true,
    state: {
        appName: 'Laravel'
    },
    getters: { // this.$tore.getters.getter
        appName: state => {
            return state.appName
        }
    }, 
    mutations: { // this.$tore.commit("mutation")
        changeAppName: (state, payload) => {
            state.appName += payload
        }
    }, 
    actions: { // this.$tore.dispatch("action") // asynchronous tasks
        changeAppName: (context, payload) => {
            context.commit("changeAppName", payload)
        }
    } 
})