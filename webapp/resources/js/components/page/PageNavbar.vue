<template>
  <div>
    <b-navbar toggleable="lg" variant="light">
      <b-navbar-brand to="/">Food@Home</b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav>
          <b-nav-item :to="{name: 'list-products'}">Products</b-nav-item>
          <div v-if="isAuthenticated">
            <b-nav-item v-if="authUser.type == 'EM'" :to="{name: 'list-users'}">Users</b-nav-item>
          </div>

        </b-navbar-nav>

        <!-- Right aligned nav items -->
        <b-navbar-nav class="ml-auto">
          <b-nav-form v-if="!isAuthenticated">
            <b-button size="sm" class="my-2 my-sm-0" :to="{name: 'login'}">Login</b-button>
          </b-nav-form>
          <b-nav-item-dropdown v-else right>
            <!-- Using 'button-content' slot -->
            <template #button-content>
              <em>{{ authUser.email }}</em>
            </template>
            <b-dropdown-item :to="{name: 'view-profile'}">Profile</b-dropdown-item>
            <b-dropdown-item @click.prevent="handleLogout()">Sign Out</b-dropdown-item>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>
  </div>
</template>

<script lang="ts">
import Vue from "vue"
	import Component from "vue-class-component"
  import { namespace } from "vuex-class";
	const Auth = namespace("auth");
	import { UserAuthModel } from '../../models/auth'
import router from "../../router";
import { UserModel } from "../../models/user";
	@Component({
		components: {}
  })
  export default class LoginView extends Vue {
		@Auth.Getter
    private isAuthenticated!: boolean;

    @Auth.Getter
    public authUser!: UserModel;

		@Auth.Action
    private makeAuthLogout!: () => Promise<void>;
		handleLogout() {
      this.makeAuthLogout().then(() => router.push('/login'))
		}
	}
</script>
