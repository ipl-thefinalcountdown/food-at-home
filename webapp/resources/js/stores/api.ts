"use strict"

import Vapi from "vuex-rest-api"
import { Store } from "vuex-rest-api/dist/Store"
import { ProductModel, ProductType } from "../models/product"
import { UserModel } from "../models/user";
import { OrderModel } from '../models/order';

export interface Params {
	params?: ParamsOptions
	data?: object,
	meta?: object
}

export interface ParamsOptions {
	id?: number | string,
	filter?: string,
	name?: string,
	type?: string,
	page?: number | string,
}

export interface LaravelResponse<T> {
	data?: T
}

const api: Store = new Vapi({
	baseURL: `/api/`,
	state: {
		products: <LaravelResponse<Array<ProductModel>>>{ data: [] },
		product: <LaravelResponse<ProductModel>>{ data: {} },

		users: <LaravelResponse<Array<UserModel>>>{ data: [] },
		user: <UserModel>{},

		orders: <LaravelResponse<Array<OrderModel>>>{ data: [] },
		order: <LaravelResponse<OrderModel>>{ data: {} },
	}
})
	.get({
		action: "getProduct",
		property: "product",
		path: (opt: ParamsOptions) => `/products/${opt.id}`
	})
	.get({
		action: "getProducts",
		property: "products",
		path: (opt: ParamsOptions) => {
			let ret = `/products/?name=${opt.name}&page=${opt.page}`;
			if (opt.type !== undefined)
				ret.concat(`&type=${<ProductType>opt.type}`)

			return ret;
		}
	})
	.delete({
		action: "deleteProduct",
		path: (opt: ParamsOptions) => `/products/${opt.id}`
	})
	.put({
		action: "putProduct",
		path: (opt: ParamsOptions) => `/products/${opt.id}`
	})
	.post({
		action: "postProduct",
		path: (opt: ParamsOptions) => `/products`
	})
	.get({
		action: "getProfile",
		property: "user",
		path: (opt: ParamsOptions) => `/user`
	})
	.get({
		action: "getUser",
		property: "user",
		path: (opt: ParamsOptions) => `/users/${opt.id}`
	})
	.get({
		action: "getUsers",
		property: "users",
		path: (opt: ParamsOptions) => {
			let ret = `/users/?name=${opt.name}&page=${opt.page}`;
			if (opt.type !== undefined)
				ret.concat(`&type=${<ProductType>opt.type}`)

			return ret;
		}
	})
	.post({
		action: "addUser",
		path: (opt: ParamsOptions) => `/users`
	})
	.put({
		action: "updateUser",
		path: (opt: ParamsOptions) => `/users/${opt.id}`
	})
	.put({
		action: "updateProfile",
		path: (opt: ParamsOptions) => `/user`
	})
    .put({
        action: "blockUser",
        path: (opt : ParamsOptions) => `/users/${opt.id}/block`
    })
	.delete({
		action: "deleteProfilePhoto",
		path: (opt: ParamsOptions) => `/user/photo`
	})
	.delete({
		action: "deleteUserPhoto",
		path: (opt: ParamsOptions) => `/users/${opt.id}/photo`
	})
	.delete({
		action: "deleteUser",
		path: (opt: ParamsOptions) => `/users/${opt.id}`
	})
	.post({
		action: "registerUser",
		path: (opt: ParamsOptions) => `/register`
	})
	.get({
		action: "getOrder",
		property: "order",
		path: (opt: ParamsOptions) => `/orders/${opt.id}`
	})
	.get({
		action: "getOrders",
		property: "orders",
		path: (opt: ParamsOptions) => `/orders/?page=${opt.page}`
	})
	.get({
		action: "getOrdersCustomer",
		property: "orders",
		path: (opt: ParamsOptions) => `/customer/orders?page=${opt.page}`
	})
	.get({
		action: "getOrdersCook",
		property: "orders",
		path: (opt: ParamsOptions) => `/cook/orders?page=${opt.page}`
	})
	.get({
		action: "getOrdersDeliveryman",
		property: "orders",
		path: (opt: ParamsOptions) => `/deliveryman/orders?page=${opt.page}`
	})
	.post({
		action: "prepareOrder",
		path: (opt: ParamsOptions) => `/cook/orders/${opt.id}/prepare`
	})
	.post({
		action: "pickupOrder",
		path: (opt: ParamsOptions) => `/deliveryman/orders/${opt.id}/pickup`
	})
	.post({
		action: "deliverOrder",
		path: (opt: ParamsOptions) => `/deliveryman/orders/${opt.id}/deliver`
	})
	.post({
		action: "cancelOrder",
		path: (opt: ParamsOptions) => `/orders/${opt.id}/cancel`
	})
	.post({
		action: "createOrderCustomer",
		path: (opt: ParamsOptions) => `/customer/orders`
	})
	.getStore();

export default api;
