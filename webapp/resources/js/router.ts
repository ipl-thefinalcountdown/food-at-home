"use strict"

import Vue from "vue"
import VueRouter from "vue-router"

import IndexView from "./views/index.vue"
import ProductListView from "./views/products/list.vue"
import ProductView from "./views/products/view.vue"
import ProductAddEdit from "./views/products/addEdit.vue"
import LoginView from './views/auth/login.vue'
import RegisterView from './views/auth/register.vue'
import UserView from './views/user/view.vue'
import UserListView from './views/user/list.vue'
import UserAddEditView from './views/user/addEdit.vue'
import UserUploadPhotoView from './views/user/upload.vue';

import { UserType } from './models/user'

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

const ifAuthenticated = (to: any, from: any, next: Function) => {
	if (store.getters['auth/isAuthenticated']) {
		next()
		return
	}
	next('/login')
}

const authenticatedRole = (role: UserType) => {
	return (to: any, from: any, next: Function) => {
		if (store.getters['auth/isAuthenticated'] && store.getters['auth/authUser'].type == role) {
			next()
			return
		}
		next('/login')
	}
}

export default new VueRouter({
	mode: 'hash',

	routes: [
		// Index
		{ path: '/', name: 'index', component: IndexView },

		// Products
		{ path: '/products', name: 'list-products', component: ProductListView },
		{ path: '/products/new', name: 'post-product', component: ProductAddEdit },
		{ path: '/products/:id', name: 'view-product', component: ProductView },
		{ path: '/products/:id/edit', name: 'put-product', component: ProductAddEdit },

		// Auth
		{ path: '/login', name: 'login', component: LoginView, beforeEnter: ifNotAuthenticated },
		{ path: '/register', name: 'register', component: RegisterView, beforeEnter: ifNotAuthenticated },

		// Profile
		{ path: '/profile/', name: 'view-profile', component: UserView, beforeEnter: ifAuthenticated },
		{ path: '/profile/upload/', name: 'upload-photo-profile', component: UserUploadPhotoView, beforeEnter: ifAuthenticated },
		{ path: '/profile/edit/', name: 'edit-profile', component: UserAddEditView, beforeEnter: ifAuthenticated },

		// Manager routes
		{ path: '/users/', name: 'list-users', component: UserListView, beforeEnter: authenticatedRole(UserType.EMPLOYEE_MANAGER) },
		{ path: '/users/new/', name: 'new-user', component: UserAddEditView, beforeEnter: authenticatedRole(UserType.EMPLOYEE_MANAGER) },
		{ path: '/users/:id', name: 'view-user', component: UserView, beforeEnter: authenticatedRole(UserType.EMPLOYEE_MANAGER) },
		{ path: '/users/:id/edit/', name: 'edit-user', component: UserAddEditView, beforeEnter: authenticatedRole(UserType.EMPLOYEE_MANAGER) },
		{ path: '/users/:id/upload/', name: 'upload-photo-user', component: UserUploadPhotoView, beforeEnter: authenticatedRole(UserType.EMPLOYEE_MANAGER) },
	]
})
