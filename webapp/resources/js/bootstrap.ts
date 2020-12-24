"use strict"

import * as _ from 'lodash'

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

import * as $ from 'jquery';
import * as Popper from 'popper.js';

import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// vue search select styles
import 'vue-search-select/dist/VueSearchSelect.css'
