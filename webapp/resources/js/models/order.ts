import { deSnakeCase } from "../utils/string";
import { ProductModel } from "./product";

export interface OrderItemModel {
	product_id?: number | string;
	product_name?: string;
	quantity?: number;
};

export interface CartItemModel {
	id?: number | string;
	productName?: string;
    quantity?: number;
    price?: number | string;
};

export enum OrderStatus {
	HOLDING = 'H',
	PREPARING = 'P',
	READY = 'R',
	TRANSIT = 'T',
	DELIVERED = 'D',
	CANCELLED = 'C',
};

// hack to invert object key:value
export var InvertedOrderStatus: {[key: string]: string} =
	Object.assign({}, ...Object.entries(OrderStatus)
		.map(([a,b]) => ({ [b]: a })));

export function statusName(status: OrderStatus): string {
	return `${deSnakeCase(InvertedOrderStatus[String(status)])}`;
}

export function statusColor(status: OrderStatus): string {
	switch(status) {
		case OrderStatus.HOLDING: return "badge-secondary";
		case OrderStatus.PREPARING: return "badge-warning";
		case OrderStatus.READY: return "badge-primary";
		case OrderStatus.TRANSIT: return "badge-info";
		case OrderStatus.DELIVERED: return "badge-success";
		case OrderStatus.CANCELLED: return "badge-danger";
	}
}

export interface OrderModel {
    id?: number | string;
    date?: string;
    notes?: string;
	customer_id?: number | string;
	customer_name?: string;
	status?: OrderStatus;
    total_price?: number | string;
    prepared_by?: string;
    delivered_by?: string;
    items?: Array<OrderItemModel>;
}
