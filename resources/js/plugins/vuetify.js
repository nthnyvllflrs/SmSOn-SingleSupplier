import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import '@fortawesome/fontawesome-free/css/all.css'

Vue.use(Vuetify)

const opts = {
    icons: {
        iconfont: 'fa',
    },
    theme: {
        dark: false,
        themes: {
            light: {
                primary: '#EF6C00',
            },
            dark: {
                primary: '#FF9800',
            },
        },
    },
}

export default new Vuetify(opts)