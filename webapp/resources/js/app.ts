"use strict"

import './bootstrap'

import Vue from 'vue';

import VueSocketIO from "vue-socket.io"
import SocketIOClient from "socket.io-client"

Vue.use(
	new VueSocketIO({
		debug: true,
		connection: SocketIOClient('/', { path: '/ws/' })
	})
)

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

import router from "./router"
import store from "./store"

new Vue({
	router,
	store,
	el: '#app'
})
