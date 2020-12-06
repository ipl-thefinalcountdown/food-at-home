"use strict";
exports.__esModule = true;
var vuex_rest_api_1 = require("vuex-rest-api");
var api = new vuex_rest_api_1["default"]({
    baseURL: "/api/",
    state: {
        products: [],
        product: {}
    }
})
    .get({
    action: "getProducts",
    property: "products",
    path: function (opt) {
        var ret = "/products/?name=" + opt.name;
        if (opt.type !== undefined)
            ret.concat("&type=" + opt.type);
        return ret;
    }
})
    .getStore();
exports["default"] = api;
