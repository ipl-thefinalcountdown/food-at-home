<template>
	<page-component>
		<b-container>
			<div class="col-md-12">
				<div class="card card-container">
				<form class="p-3" name="form" @submit.prevent="handleRegister">
					<div class="form-group">
						<label for="name">Name</label>
						<input
							v-model="user.name"
							type="text"
							class="form-control"
							name="name"
						/>
					</div>
					<div class="form-group">
						<label for="phone">Phone Number</label>
						<input
							v-model="user.phone"
							type="text"
							class="form-control"
							name="phone"
						/>
					</div>
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
						<label for="address">Address</label>
						<input
							v-model="user.address"
							type="text"
							class="form-control"
							name="address"
						/>
					</div>
					<div class="form-group">
						<label for="nif">NIF</label>
						<input
							v-model="user.nif"
							type="text"
							class="form-control"
							name="nif"
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
						<b-link to="login">Already have an account?</b-link>
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
	import PageComponent from '../../components/Page.vue'
	import { RegisterAuthModel } from '../../models/auth'
	import { AlertType, createAlert } from "../../utils/alert";
	import { mapState, mapActions } from "vuex";
	import { Params } from "../../stores/api";
	import { AxiosPromise, AxiosResponse } from "axios";
	@Component({
		components: {
			PageComponent
		},
		computed: {
			...mapState({
				pending: (state: any) => state.api.pending,
      			error: (state: any) => state.api.error,
			})
		},
		methods: {
			...mapActions(["registerUser"]),
		}
	})
	export default class RegisterView extends Vue {
		registerUser!: (obj: Params) => AxiosPromise;
		private user: RegisterAuthModel = {};
		private submitted: boolean = false;
		private successful: boolean = false;
		private message: string = "";
		@Auth.Getter
		private isAuthenticated!: boolean;
		@Auth.Action
		private makeAuthRequest!: (data: any) => Promise<AxiosResponse>;
		mounted() {
			if (this.isAuthenticated) {
				this.$router.push("/profile");
			}
		}
		onError(err : any) {
			createAlert(
				AlertType.Danger,
				`Error on register the user ${this.user?.email}: ${err}`
			);
		}
		handleRegister() {
			this.registerUser({
				data: this.user
			}).then((res) => {
				this.makeAuthRequest({
					email: this.user.email,
					password: this.user.password
				}).then(
					(data) => {
						this.$router.push("/");
					}, this.onError
				);
			}).catch(this.onError);
		}
	}
</script>
