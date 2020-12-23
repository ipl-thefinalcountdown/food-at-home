"use strict"

import Vapi from "vuex-rest-api"
import { Store } from "vuex-rest-api/dist/Store"
import {ProductModel, ProductType} from "../models/product"
import { UserModel } from "../models/user";

export interface Params {
	params?: ParamsOptions
	data?: object
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

const api : Store = new Vapi({
	baseURL: `/api/`,
	state: {
		products: <LaravelResponse<Array<ProductModel>>>{data:[]},
		product: <LaravelResponse<ProductModel>>{data:{}},

		profile: <UserModel>{},
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
	.get({
		action: "getProfile",
		property: "profile",
		path: (opt: ParamsOptions) => `/user`
	})
	.post({
		action: "registerUser",
		path: (opt: ParamsOptions) => `/register`
	})
	.getStore();

export default api;
