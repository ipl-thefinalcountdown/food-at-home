<template>
  <page-component>
    <div class="container">
      <b-row cols="1">
        <b-col v-if="items[0].type === userTypeCustomer && authUser.type === userTypeManager">
          <item-details
            :items="items"
            :awaiting-items="pending.user"
          >
            <template #cell(photo)="data">
              <div>
                <b-img v-if="data.value" thumbnail rounded :src="`/storage/fotos/${data.value}`" fluid alt="Product photo"></b-img>
                <span class="text-secondary" v-else>No photo</span>
              </div>
            </template>
          </item-details>
        </b-col>
        <b-col v-else-if="authUser.type === userTypeManager && items[0].id === authUser.id">
          <item-details
            :items="items"
            :awaiting-items="pending.user"
            :editClicked="editClicked"
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
        <b-col v-else>
          <item-details
            :items="items"
            :awaiting-items="pending.user"
            :edit-clicked="editClicked"
            :delete-clicked="deleteClicked"
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
import { UserModel, UserType } from "../../models/user";
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
        "deleteUserPhoto",
        "deleteProfile"
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
  getUser!: (obj: Params) => AxiosPromise;
  deleteProfilePhoto!: () => AxiosPromise;
  deleteUserPhoto!: (obj: Params) => AxiosPromise;
  deleteProfile!: () => AxiosPromise;
  deleteUser!: (obj: Params) => AxiosPromise;

  @Auth.Getter
  private isAuthenticated!: boolean;
  @Auth.Getter
  public authUser!: UserModel;
  @Auth.Action
  private makeAuthDelete!: () => Promise<void>;

  isProfile?: boolean;
  userId?: string;

  items?: any;

  userTypeCustomer: string = UserType.CUSTOMER;
  userTypeManager: string = UserType.EMPLOYEE_MANAGER;

  editClicked() {
    if(this.isProfile)
      router.push({name:'edit-profile'})
    else
      router.push({name:'edit-user', params: {id: this.userId || ''}});
  }

  deleteClicked() {
    if (this.isProfile)
      this.makeAuthDelete().then(() => {
          router.push('/');
      });
    else
      this.deleteUser({ params: { id: this.userId } })
          .then(() => {
            // success deletion
            createAlert(AlertType.Success, `User ${this.items[0].name} deleted.`);
          })
          .catch((request) => {
            let errors = request.response.data.errors;
            for (const error in errors) {
                createAlert(AlertType.Danger, `Error deleting "${this.items[0].name}": ${error}: ${errors[error]}`);
            }
          });
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
      this.getUser({params: {id: this.userId}})
        .catch((request) => {
          router.push({ name: "list-users" })
            .then(() => {
              if (request.response.data.errors) {
                let errors = request.response.data.errors;
                for (const error in errors) {
                  createAlert(AlertType.Danger, `Error fetching user (${this.userId}): ${error}: ${errors[error]}`);
                }
              } else {
                createAlert(AlertType.Danger, `Error fetching user (${this.userId}): ${request.response.data.message}`);
              }
          });
        })
    }
  }

  mounted() {
    this.loadData();
  }
}
</script>
