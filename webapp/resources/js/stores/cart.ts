"use strict"

import axios, { AxiosResponse } from 'axios';
import { VuexModule, Module, Mutation, Action } from 'vuex-module-decorators';
import { CartItemModel } from '../models/order';
import { ProductModel } from '../models/product';


@Module({ namespaced: true })
export class Cart extends VuexModule {
	public items: Array<CartItemModel> = JSON.parse(
		localStorage.getItem('shopping-cart') || '[]'
	);

	@Mutation
	public addCartProduct(product: ProductModel): void {
        for (let p of this.items)
            if (p.quantity && p.id == product.id)
            {
                p.quantity++;
                return;
            }

        this.items.push({
            id: product.id,
            productName: product.name,
            price: product.price,
            quantity: 1
        });
	}

	@Mutation
	public removeCartProduct(idx: number): void {
		this.items.splice(idx, 1);
	}

	@Mutation
	public removeAllCartProducts(): void {
		this.items = [];
    }

    @Mutation
    public changeCartQuantity(data: { index: number, quantity: number }): void {
        this.items[data.index].quantity = data.quantity;
    }

	@Action
	public addCartProductAction(product: ProductModel): void {
		this.context.commit('addCartProduct', product);
		localStorage.setItem('shopping-cart', JSON.stringify(this.items));
	}

	@Action
	public removeCartProductAction(idx: number): void {
		this.context.commit('removeCartProduct', idx);
		localStorage.setItem('shopping-cart', JSON.stringify(this.items));
    }

    @Action
    public changeProductQuantityAction(data: { index: number, quantity: number }): void {
        if (data.quantity <= 0)
            this.context.commit('removeCartProduct', data.index);
        else
            this.context.commit('changeCartQuantity', data);

        localStorage.setItem('shopping-cart', JSON.stringify(this.items));
    }

	@Action
	public removeAllCartProductsAction(): void {
		this.context.commit('removeAllCartProducts');
		localStorage.setItem('shopping-cart', JSON.stringify(this.items));
	}

	get cartLength(): number {
        let length = 0;
        for (let c of this.items)
            if (c.quantity)
                length += c.quantity;

        return length;
    }

    get cartPrice(): number {
        let price = 0;
        for (let c of this.items)
            if (c.price && c.quantity)
                price += Number(c.price) * c.quantity;

        return price;
    }

	get cartItems(): Array<CartItemModel> {
        return this.items;
	}
}

export default Cart;
