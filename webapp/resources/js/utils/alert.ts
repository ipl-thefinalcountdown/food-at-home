"use strict"

import * as $ from 'jquery';

export enum AlertType {
	Success = 'alert-success',
	Danger = 'alert-danger',
	Warning = 'alert-warning',
	Info = 'alert-info'
}

export function createAlert(type: AlertType, text: string, delay: number = 5000) {
	const hideSpan = document.createElement("span");
	hideSpan.setAttribute('aria-hidden', 'true');
	hideSpan.innerHTML = '&times;';

	const buttonNode = document.createElement("button");
	buttonNode.classList.add('close');
	buttonNode.setAttribute('data-dismiss', 'alert');
	buttonNode.setAttribute('aria-label', 'Close');
	buttonNode.appendChild(hideSpan)

	const alertNode = document.createElement("div");
	alertNode.classList.add('alert', 'alert-dismissible', 'fade', 'show', type);
	alertNode.setAttribute('role', 'alert');
	alertNode.innerHTML = text;
	alertNode.appendChild(buttonNode);

	document.getElementById("app-alert-box")?.appendChild(alertNode);

	setTimeout(function() {
		// need any due to bootstrap specific jQuery plugin
		(<any>$(alertNode)).alert('close');
	}, delay);
}
