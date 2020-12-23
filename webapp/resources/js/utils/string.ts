export const deCamelCase = (str: string) => str.replace(/[A-Z]/g, ' $&')
	.replace(/^./, toUppercase);

export const deSnakeCase = (str: string) => str.replace("_", " ")
	.toLowerCase()
	.replace(/^./, toUppercase);

export const toUppercase = (str : string) => str.toUpperCase();
