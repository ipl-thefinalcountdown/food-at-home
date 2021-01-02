<template>
  <page-component>
    <div class="container">
      <div class="justify-content-center">
        <searchable-table
          :items="items"
          :row-clicked="rowClicked"
          :add-clicked="addClicked"
          :edit-clicked="editClicked"
          :delete-clicked="deleteClicked"
          :block-clicked="blockClicked"
          :filter-changed="filterChanged"
          :page-changed="pageChanged"
          :meta-total="total"
        >
          <template #cell(blocked)="data">
              <span v-if="data.item.blocked" :class="`pr-2 pl-2 badge badge-pill badge-danger`">{{ 'Blocked' }}</span>
              <span v-else :class="`pr-2 pl-2 badge badge-pill badge-success`">{{ 'Available' }}</span>
          </template>
          <template #cell(__actions)="scope">
              <div class="text-nowrap text-right">

              <!-- Block icon button -->
              <b-link
                  v-if="blockClicked"
                  @click.prevent="blockClicked(scope.item, scope.index, $event)"
              >
                <i v-if="scope.item.blocked" class="fas fa-ban fa-lg text-secondary"/>
                <i v-else class="fas fa-ban fa-lg text-danger"/>
              </b-link>

              <!-- Edit icon button -->
              <b-link
                v-if="editClicked && scope.item.type != 'Customer'"
                @click.prevent="editClicked(scope.item, scope.index, $event)"
                class="text-secondary"
              >
                <i class="fas fa-edit fa-lg"></i>
              </b-link>
              <i v-else v-visible="false" class="fas fa-edit fa-lg"></i>

              <!-- Delete icon button -->
              <b-link
                v-if="deleteClicked && scope.item.type != 'Customer'"
                @click.prevent="deleteClicked(scope.item, scope.index, $event)"
                class="text-danger"
              >
                <i class="fas fa-trash-alt fa-lg"></i>
              </b-link>
              <i v-else v-visible="false" class="fas fa-trash-alt fa-lg"></i>
            </div>
          </template>
        </searchable-table>
      </div>
    </div>
  </page-component>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";
import VueVisible from 'vue-visible';
import { mapState, mapActions } from "vuex";

import PageComponent from "../../components/Page.vue";
import SearchableTable from "../../components/SearchableTable.vue";

import router from "../../router";
import { UserModel, UserType } from "../../models/user";
import { AlertType, createAlert } from "../../utils/alert";

import { Params, LaravelResponse } from "../../stores/api";
import { AxiosPromise } from "axios";

import { namespace } from "vuex-class";
import { deSnakeCase } from "../../utils/string";
const Auth = namespace("auth");

Vue.use(VueVisible);

@Component({
  components: {
    PageComponent,
    SearchableTable,
  },
  computed: {
    ...mapState({
      items(state: any) {
        return state.api.users.data.map((u: UserModel) => {
          return {
            id: u.id,
            name: u.name,
            blocked: u.blocked,
            type: (() => Object.entries(UserType)
                .filter(t => t[1] == u.type)
                .map(arr => deSnakeCase(arr[0])))()[0]
          };
        }).filter((u: UserModel) => u.id !== this.authUser.id);
      },

      total: (state: any) => state.api.users.meta == undefined ? 0 : state.api.users.meta.total,

      pending: (state: any) => state.api.pending,
      error: (state: any) => state.api.error,
    }),
  },
  methods: {
    ...mapActions(["getUsers", "deleteUser", "blockUser"]),
  },
})
export default class UserListView extends Vue {
  getUsers!: (obj: Params) => void;
  deleteUser!: (obj: Params) => AxiosPromise;
  blockUser!: (obj: Params) => AxiosPromise;

  @Auth.Getter
  private isAuthenticated!: boolean;

  @Auth.Getter
  public authUser!: UserModel;

  filterText?: string = "";
  currentPage?: number | string;

  excludeType: string = UserType.CUSTOMER;

  addClicked()
  {
    router.push({name: "new-user"});
  }

  rowClicked(record: UserModel, index: number, event: Event) {
    router.push({
      name: "view-user",
      params: { id: String(record?.id) },
    });
  }
  editClicked(record: UserModel, index: number, event: Event) {
    router.push({
      name: "edit-user",
      params: { id: String(record?.id) },
    });
  }
  deleteClicked(record: UserModel, index: number, event: Event) {
     // perform delete on the API
     if(record.id == this.authUser.id)
      {
        createAlert(
          AlertType.Danger,
          `Can't delete yourself!`
        );
        return;
      }
        this
          .deleteUser({ params: { id: record.id } })
          .then(() => {
            // success deletion
            createAlert(AlertType.Success, `User ${record.name} deleted.`);
            // splice directly from the store state objectect
            (<Array<UserModel>>this.$store.state.api.users.data).splice(index, 1);
          })
          .catch((err) => {
            // error on delete
            createAlert(
              AlertType.Danger,
              `Error deleting user ${record.id}: ${err}`
            );
          });
  }

  blockClicked(record: UserModel, index: number, event: Event) {
      console.log(!record.blocked);
      this.blockUser({
          params: { id: record.id },
          data: { blocked: !record.blocked }
      })
      .then((response) => {
          createAlert(AlertType.Success, `User "${record.name}" ${!record.blocked ? 'blocked' : 'unblocked'}.`);
          record.blocked = response.data.blocked;
      })
      .catch((request) => {
          let errors = request.response.data.errors;
          for (const error in errors) {
              createAlert(AlertType.Danger, `Error blocking "${record.name}": ${error}: ${errors[error]}`);
          }
      })
  }

  filterChanged(text: string) {
    this.getUsers({ params: { name: text } });
    this.filterText = text;
  }

  pageChanged(page: string) {
      this.currentPage = page;
      this.getUsers({ params: { name: this.filterText, page: page } });
  }

  mounted() {
    this.getUsers({ params: { name: "" } });
  }
}
</script>
