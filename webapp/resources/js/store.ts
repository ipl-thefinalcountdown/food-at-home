"use strict"

import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import api from "./stores/api"

export default new Vuex.Store({
	modules: {
		api: <any>api
	}
});
