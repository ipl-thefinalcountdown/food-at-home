"use strict"

export interface BootstrapField {
	key?: string,
	label?: string
};

export type BootstrapFields = Array<string | BootstrapField>

export const fieldKeys = (array: BootstrapFields) => {
	return array.map((val) => {
		if (typeof val === 'string') return val;
		if (typeof val === 'object') return val["key"];
	})
};

import { deCamelCase } from "./string";

export const prettyFields = (array: BootstrapFields) => {
	return array.map((val) => {
		if (typeof val === 'string') return deCamelCase(val);
		if (typeof val === 'object') return val["label"];
	})
};
