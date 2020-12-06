"use strict"

import './bootstrap'

import Vue from 'vue';

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

import router from "./router"
import store from "./store"

new Vue({
	router,
	store,
	el: '#app'
})
