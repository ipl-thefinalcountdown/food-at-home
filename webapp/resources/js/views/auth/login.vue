<template>
	<page-component>
		<b-container>
			<div class="col-md-12">
				<div class="card card-container">
				<form class="p-3" name="form" @submit.prevent="handleLogin">
					<div class="form-group">
						<label for="email">Email</label>
						<input
							v-model="user.email"
							type="text"
							class="form-control"
							name="email"
						/>
					</div>
					<div class="form-group">
						<label for="password">Password</label>
						<input
							v-model="user.password"
							type="password"
							class="form-control"
							name="password"
						/>
					</div>
					<div class="form-group">
						<b-link to="register">Don't have an account yet?</b-link>
					</div>
					<div class="form-group">
						<button class="btn btn-primary btn-block">Login</button>
					</div>
				</form>
				</div>
			</div>
		</b-container>
	</page-component>
</template>

<script lang="ts">
	import Vue from "vue"
	import Component from "vue-class-component"
	import { namespace } from "vuex-class";
	const Auth = namespace("auth");
	import { AlertType, createAlert } from "../../utils/alert";
	import PageComponent from '../../components/Page.vue'
	import { UserAuthModel } from '../../models/auth'
	import { AxiosResponse } from "axios";
	@Component({
		components: {
			PageComponent
		}
	})
	export default class LoginView extends Vue {
		private user: UserAuthModel = { email: "", password: "" };
		@Auth.Getter
		private isAuthenticated!: boolean;
		@Auth.Action
		private makeAuthRequest!: (data: any) => Promise<AxiosResponse>;
		mounted() {
			if (this.isAuthenticated) {
				this.$router.push("/profile");
			}
		}
		handleLogin() {
			this.makeAuthRequest(this.user).then(
				(data) => {
					this.$router.push("/");
					createAlert(
						AlertType.Success,
						`Login in with success!`
					);
				}, (error) => {
					createAlert(
						AlertType.Danger,
						`Email or password incorrect`
					);
				}
			);
		}
	}
</script>
