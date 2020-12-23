<template>
  <page-component>
    <div class="container">
      <b-row cols="1">
        <b-col>
          <item-details
            :items="items"
            :awaiting-items="pending.profile"
          >
          </item-details>
        </b-col>
      </b-row>
    </div>
  </page-component>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";
import { AxiosPromise } from "axios";
import PageComponent from "../../components/Page.vue";
import ItemDetails from "../../components/item/ItemDetails.vue";
import SearchableTable from "../../components/SearchableTable.vue";
import { mapState, mapActions } from "vuex";
import { UserModel } from "../../models/user";
import { AlertType, createAlert } from "../../utils/alert";
import { deSnakeCase } from "../../utils/string";
import { Params } from "../../stores/api";
import router from "../../router";
  import { namespace } from "vuex-class";
	const Auth = namespace("auth");
@Component({
  components: {
    PageComponent,
    ItemDetails,
  },
  computed: mapState({
    items: (state: any) => {
      return [state.api.profile].map((c: UserModel) => {
        return {
		  name: c.name,
		  email: c.email,
          address: c.address,
		  phoneNumber: c.phone,
		  nif: c.nif,
        };
      });
    },
    pending: (state: any) => state.api.pending,
  }),
  methods: {
    ...mapActions(["getProfile"]),
  },
})
export default class ProfileView extends Vue {
  getProfile!: () => void;
  @Auth.Getter
private isAuthenticated!: boolean;
@Auth.Getter
public authUser!: UserModel;
  data() {
    return {}
  }
  mounted() {
	  this.getProfile()
  }
}
</script>
