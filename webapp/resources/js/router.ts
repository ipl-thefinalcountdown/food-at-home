"use strict"

import Vue from "vue"
import VueRouter from "vue-router"

import IndexView from "./views/index.vue"
import ProductListView from "./views/products/list.vue"

Vue.use(VueRouter)

export default new VueRouter({
	mode: 'hash',

	routes: [
		{ path: '/', name: 'index', component: IndexView },
		{ path: '/products', name: 'list-products', component: ProductListView },
	]
})
