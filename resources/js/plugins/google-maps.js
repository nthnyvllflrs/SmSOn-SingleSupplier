import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'
Vue.use(VueGoogleMaps, {
    load: {
      key: 'AIzaSyBLvHFeixDacvhmdX-L_0EoG4of6n0pM1A',
      // libraries: 'places',
    },
    autobindAllEvents: true,
    installComponents: true
})