"use strict";
exports.__esModule = true;
exports.createAlert = exports.AlertType = void 0;
var $ = require("jquery");
var AlertType;
(function (AlertType) {
    AlertType["Success"] = "alert-success";
    AlertType["Danger"] = "alert-danger";
    AlertType["Warning"] = "alert-warning";
    AlertType["Info"] = "alert-info";
})(AlertType = exports.AlertType || (exports.AlertType = {}));
function createAlert(type, text, delay) {
    var _a;
    if (delay === void 0) { delay = 5000; }
    var hideSpan = document.createElement("span");
    hideSpan.setAttribute('aria-hidden', 'true');
    hideSpan.innerHTML = '&times;';
    var buttonNode = document.createElement("button");
    buttonNode.classList.add('close');
    buttonNode.setAttribute('data-dismiss', 'alert');
    buttonNode.setAttribute('aria-label', 'Close');
    buttonNode.appendChild(hideSpan);
    var alertNode = document.createElement("div");
    alertNode.classList.add('alert', 'alert-dismissible', 'fade', 'show', type);
    alertNode.setAttribute('role', 'alert');
    alertNode.innerHTML = text;
    alertNode.appendChild(buttonNode);
    (_a = document.getElementById("app-alert-box")) === null || _a === void 0 ? void 0 : _a.appendChild(alertNode);
    setTimeout(function () {
        // need any due to bootstrap specific jQuery plugin
        $(alertNode).alert('close');
    }, delay);
}
exports.createAlert = createAlert;
