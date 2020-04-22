import './bootstrap';
import Vue from 'vue';
import App from "./components/App";
import vuetify from "./plugins/vuetify";

window.bus = new Vue();

const app = new Vue({
    el: '#app',
    vuetify,
    components: {
        App
    }
});
