import Vue from 'vue'
import App from './App.vue'

Vue.config.productionTip = false

import {LMap, LTileLayer, LMarker, LCircle} from 'vue2-leaflet';

import 'leaflet/dist/leaflet.css';
import './assets/css/materialize.min.css';

Vue.component('l-map', LMap);
Vue.component('l-tile-layer', LTileLayer);
Vue.component('l-marker', LMarker);
Vue.component('l-circle', LCircle);

import { Icon } from 'leaflet';

delete Icon.Default.prototype._getIconUrl;
Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});

new Vue({
  render: h => h(App),
}).$mount('#app')
