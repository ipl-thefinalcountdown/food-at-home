<template>
	<div>
		<b-container id="app-alert-box"/>
		<page-navbar/>
			<main id="app-content" class="py-4">
				<b-container v-if="$route.path != '/'">
					<b-breadcrumb :items="bcItems"></b-breadcrumb>
				</b-container>
				<slot></slot>
			</main>
		<page-footer/>
	</div>
</template>

<script lang="ts">
	import Vue from "vue"
	import Component from "vue-class-component"

	import PageNavbar from '../components/page/PageNavbar.vue'
	import PageFooter from '../components/page/PageFooter.vue'

	import {deCamelCase, toUppercase} from '../utils/string'

	import {RouteConfig} from "vue-router"

	interface BreadCrumbItem {
		path?: string
		to?: RouteConfig | string,
		text?: string
	}

	@Component({
		components: {
			PageNavbar,
			PageFooter
		},
		computed: {
			bcItems() {
				// get route path from Vue router
				let paths = this.$route.path.split("/");
				// remove empty root path entry
				paths.shift();

				// map breadcrumb items from splited paths
				let breadCrumbs = paths.reduce((breadcrumbArray: BreadCrumbItem[], path, idx) => {
					breadcrumbArray.push({
					path: path,
					to: breadcrumbArray[idx - 1]
						? "/" + breadcrumbArray[idx - 1].path + "/" + path
						: "/" + path,
					text: deCamelCase(path),
					});
					return breadcrumbArray;
				}, []);

				// concat with Home item
				return [<BreadCrumbItem>{text:'Home', to: '/'}].concat(breadCrumbs);
			}
		}
	})
	export default class PageComponent extends Vue {}
</script>

<style scoped>
#app-alert-box {
    position: fixed;
    top: 0;
	left: 0;
	margin-top: 25px;
	margin-left: 10%;
    margin-right: 10%;
    width: 80%;
	z-index: 1;
}
</style>
