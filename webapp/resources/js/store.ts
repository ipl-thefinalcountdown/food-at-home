"use strict"

import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import api from "./stores/api"
import auth from "./stores/auth"
import axios from "axios"


axios.defaults.withCredentials = true
axios.defaults.baseURL = '/api/';

const token = localStorage.getItem('user-token')

if (token) {
  axios.defaults.headers.common['Authorization'] = 'Bearer ' + token
}

export default new Vuex.Store({
	modules: {
		api: <any>api,
		auth: auth
	}
});
