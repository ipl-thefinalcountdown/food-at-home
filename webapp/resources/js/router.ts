"use strict"

import Vue from "vue"
import VueRouter from "vue-router"

import IndexView from "./views/index.vue"
import ProductListView from "./views/products/list.vue"
import LoginView from './views/auth/login.vue'
import RegisterView from './views/auth/register.vue'
import ProfileView from './views/auth/profile.vue'

Vue.use(VueRouter)

import store from './store'
import { Params } from "./stores/api"

const ifNotAuthenticated = (to: any, from: any, next: Function) => {
	if (!store.getters['auth/isAuthenticated']) {
		next()
		return
	}
	next('/')
}

const ifAuthenticated = (to: any, from: any, next : Function) => {
	if (store.getters['auth/isAuthenticated']) {
		next()
		return
	}
	next('/login')
}

export default new VueRouter({
	mode: 'hash',

	routes: [
		{ path: '/', name: 'index', component: IndexView },
		{ path: '/products', name: 'list-products', component: ProductListView },

		{ path: '/login', name: 'login', component: LoginView, beforeEnter: ifNotAuthenticated },
		{ path: '/register', name: 'register', component: RegisterView, beforeEnter: ifNotAuthenticated },
		{ path: '/profile/', name: 'view-profile', component: ProfileView, beforeEnter: ifAuthenticated },
	]
})
