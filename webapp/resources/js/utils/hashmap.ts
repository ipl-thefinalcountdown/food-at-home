"use strict"

export const objToArray = (values: Map<any, any>, fields: string[]) => {
	return Object.entries(values).map((keyval: any) => {
		return {
			[fields[0]]: keyval[0],
			[fields[1]]: keyval[1],
		};
	});
};
