
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Vue from 'vue';
import Vuetify from 'vuetify';
import axios from 'axios';
export const EventBus = new Vue();

Vue.use(Vuetify);
window.axios = axios;

import VuetifyClipboard from 'vuetify-clipboard-input';

Vue.use(VuetifyClipboard);

Vue.component('supervisor-page', require('./components/SupervisorPage.vue'));

new Vue({
    el: '#app'
});
