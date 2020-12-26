<template>
  <page-component>
    <div class="container">
      <b-row cols="1">
        <b-col>
          <item-details
            :items="items"
            :awaiting-items="pending.user"
            :edit-clicked="editClicked"
          >
            <template #cell(photo)="data">
              <div>
                <b-img v-if="data.value" thumbnail rounded :src="`/storage/fotos/${data.value}`" fluid alt="Product photo"></b-img>
                <span class="text-secondary" v-else>No photo</span>
                <b-button :to="uploadRoute" variant="outline-secondary" size="sm">Upload</b-button>
                <b-button v-if="data.value" @click.prevent="deletePhoto" variant="outline-danger" size="sm">Delete</b-button>
              </div>
            </template>
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
  computed: {
    ...mapState({
    items: (state: any) => {
      return [state.api.user].map((u: UserModel) => {
        let mappedUser = { ...u };
        delete mappedUser["name"];
        delete mappedUser["photo_url"];
        delete mappedUser["blocked"];

        delete mappedUser["email_verified_at"];
        delete mappedUser["created_at"];
        delete mappedUser["updated_at"];
        delete mappedUser["deleted_at"];
        delete mappedUser["logged_at"];
        delete mappedUser["available_at"];
        return {
          name: u.name,
          photo: u.photo_url,
          ...mappedUser
        };
      });
    },
    pending: (state: any) => state.api.pending,
  }),
  uploadRoute() {
    if((<any>this).$route.params.id)
      return {name: 'upload-photo-user'};
    else
      return {name: 'upload-photo-profile' };
  }
  },
  methods: {
    ...mapActions([
        "getProfile",
        "getUser",
        "deleteProfilePhoto",
        "deleteUserPhoto"
      ]),
  },
  watch: {
    $route(to, from) {
      let obj = (<any>this);

      obj.loadData();
    }
  }
})
export default class ProfileView extends Vue {
  getProfile!: () => void;
  getUser!: (obj: Params) => void;
  deleteProfilePhoto!: () => AxiosPromise;
  deleteUserPhoto!: (obj: Params) => AxiosPromise;

  @Auth.Getter
  private isAuthenticated!: boolean;
  @Auth.Getter
  public authUser!: UserModel;

  isProfile?: boolean;
  userId?: string;

  editClicked() {
    if(this.isProfile)
      router.push({name:'edit-profile'})
    else
      router.push({name:'edit-user', params: {id: this.userId || ''}});

  }

  deletePhoto() {
      (this.isProfile
        ? this.deleteProfilePhoto()
        : this.deleteUserPhoto({params:{id: this.userId}})).then((res) => {
        this.$store.state.api.user.photo_url = undefined;
      })
  }

  loadData()
  {
    let pathArr = this.$route.path.split("/");
    if(pathArr[pathArr.length - 1] == "profile")
    {
      this.isProfile = true;
      this.getProfile();
    } else {
      this.isProfile = false;
      this.userId = this.$route.params.id;
      this.getUser({params: {id: this.userId}});
    }
  }

  mounted() {
    this.loadData();
  }
}
</script>
