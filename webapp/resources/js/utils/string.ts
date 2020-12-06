export const deCamelCase = (str : string) => str.replace(/[A-Z]/g, ' $&').replace(/^./, toUppercase);
export const toUppercase = (str : string) => str.toUpperCase();
