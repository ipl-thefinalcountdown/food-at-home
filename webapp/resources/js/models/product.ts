export enum ProductType {
	Drink = 'drink',
	Dessert = 'dessert',
	HotDish = 'hot dish',
	ColdDish = 'cold dish'
}

export interface ProductModel {
	id?: number
	name?: string
	type?: ProductType
	description?: string
	photo_url?: string
	price?: number | string
}
