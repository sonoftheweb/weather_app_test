import Vue from 'vue';
import Vuetify from "vuetify";
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify);
const options = {
    theme: {
        dark: false
    }
}

export default new Vuetify(options);
