"use strict";
exports.__esModule = true;
exports.toUppercase = exports.deCamelCase = void 0;
exports.deCamelCase = function (str) { return str.replace(/[A-Z]/g, ' $&').replace(/^./, exports.toUppercase); };
exports.toUppercase = function (str) { return str.toUpperCase(); };
