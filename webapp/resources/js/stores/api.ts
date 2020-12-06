"use strict"

import Vapi from "vuex-rest-api"
import { Store } from "vuex-rest-api/dist/Store"
import {ProductModel, ProductType} from "../models/product"

export interface Params {
	params?: ParamsOptions
	data?: object
}

export interface ParamsOptions {
	id?: number | string,
	filter?: string,
	name?: string,
	type?: string
}

export interface LaravelResponse<T> {
	data?: T
}

const api : Store = new Vapi({
	baseURL: `/api/`,
	state: {
		products: <LaravelResponse<Array<ProductModel>>>{data:[]},
		product: <LaravelResponse<ProductModel>>{},
	}
})
	.get({
		action: "getProducts",
		property: "products",
		path: (opt: ParamsOptions) => {
			let ret = `/products/?name=${opt.name}`;
			if (opt.type !== undefined)
				ret.concat(`&type=${<ProductType>opt.type}`)

			return ret;
		}
	})
	.getStore();

export default api;
