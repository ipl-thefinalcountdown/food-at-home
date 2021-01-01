<template>
  <page-component>
    <div class="container">
      <item-edit v-if="itemLoaded" :on-submit="onSubmit" :on-reset="onReset">
        <form-field label="Name" placeholder="Enter name" v-model="form.name" />
        <form-field
          label="Email"
          placeholder="Enter email"
          v-model="form.email"
        />
        <form-searchable-select label="User type" placeholder="Select the user type" v-model="form.type" :options="types" />
        <div class="form-group">
          <label for="password">Password</label>
          <b-input
            v-model="password"
            type="password"
            class="form-control"
            :placeholder="isEdit ? `Enter new password` : `Enter password`"
            name="password"
          />
        </div>
        <div class="form-group">
          <label for="confirm-password">Confirm Password</label>
          <b-input
            :required="!!password"
            v-model="passwordConfirmation"
            :state="passwordValid"
            type="password"
            class="form-control"
            placeholder="Confirm password"
            name="password"
          />
        </div>
      </item-edit>
      <div v-else class="text-center text-secondary my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
    </div>
  </page-component>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";

import { mapState, mapActions } from "vuex";
import { AxiosPromise } from "axios";

import PageComponent from "../../components/Page.vue";
import ItemEdit from "../../components/item/ItemAddEdit.vue";
import FormField from "../../components/form/FormField.vue";
import FormSearchableSelect from "../../components/form/FormSearchableSelect.vue";

import { AlertType, createAlert } from "../../utils/alert";

import { Params } from "../../stores/api";

import router from "../../router";
import { UserModel, UserType } from "../../models/user";

import { namespace } from "vuex-class";
import { deSnakeCase } from "../../utils/string";
const Auth = namespace("auth");

@Component({
  components: {
    PageComponent,
    ItemEdit,
    FormField,
    FormSearchableSelect,
  },
  computed: {
    passwordValid() {
      return !(<any>this).password
        ? null
        : (<any>this).password == (<any>this).passwordConfirmation;
    },

    types: () => Object.entries(UserType)
        .map(tuple => {
            return {
                value: tuple[1],
                text: `${deSnakeCase(tuple[0])}`,
            };
        })
        .filter(tuple => tuple.value != UserType.CUSTOMER),

    ...mapState({
      user: (state: any) => state.api.user,
      pending: (state: any) => state.api.pending,
      error: (state: any) => state.api.error,
    }),
  },
  methods: {
    ...mapActions([
      "getProfile",
      "getUser",
      "updateUser",
      "updateProfile",
      "addUser",
    ]),
  },
})
export default class UserAddEditView extends Vue {
  getProfile!: () => AxiosPromise;
  getUser!: (obj?: Params) => AxiosPromise;
  updateUser!: (obj?: Params) => AxiosPromise;
  updateProfile!: (obj?: Params) => AxiosPromise;
  addUser!: (obj?: Params) => AxiosPromise;

  @Auth.Getter
  private isAuthenticated!: boolean;
  @Auth.Getter
  public authUser!: UserModel;

  user?: UserModel;
  form: UserModel = {};

  passwordValid?: boolean;
  password: string = "";
  passwordConfirmation: string = "";

  userId?: string | number;

  isEdit: boolean = false;
  isProfile?: boolean;
  itemLoaded?: boolean = false;

  onSubmit() {
    if (this.passwordValid === false) {
      createAlert(AlertType.Danger, `Password doesn't match!`);
      return;
    } else if (this.form && this.passwordValid === true) {
      this.form.password = this.password;
    }

    if (this.isEdit) {
      (this.isProfile ? this.updateProfile : this.updateUser)({
        params: {
          id: this.user?.id,
        },
        data: this.form,
      })
        .then(() => {
          // go back
          router.go(-1);
        })
        .catch((request) => {
          let errors = request.response.data.errors;
          for (const error in errors) {
              createAlert(AlertType.Danger, `Error editing user: ${error}: ${errors[error]}`);
          }
        });
    } else {
      this.addUser({ data: this.form })
        .then(() => {
          // go back
          router.go(-1);
        })
        .catch((request) => {
          let errors = request.response.data.errors;
          for (const error in errors) {
              createAlert(AlertType.Danger, `Error adding user: ${error}: ${errors[error]}`);
          }
        });
    }
  }
  onReset(ev: Event) {
    ev.preventDefault();
    this.setEditFields();
  }

  setEditFields() {
    if (this.isEdit) {
      (this.isProfile
        ? this.getProfile()
        : this.getUser({ params: { id: this.userId } })
      )
        .then(() => {
          if (this.authUser.type === UserType.EMPLOYEE_MANAGER && this.user?.type === UserType.CUSTOMER) {
              router.go(-1);
          }

          this.form = {
            id: this.user?.id,
            name: this.user?.name,
            email: this.user?.email,
            type: this.user?.type,
          };

          this.itemLoaded = true;
        })
        .catch((err) => {
          createAlert(
            AlertType.Danger,
            `Error on fetching user ${this.userId}: ${err}`
          );
        });
    }
  }

  mounted() {
    let pathArr = this.$route.path.split("/");
    if (pathArr[pathArr.length - 1] == "edit") this.isEdit = true;
    else this.itemLoaded = true;

    this.isProfile = this.$route.params.id == undefined;

    this.userId = !this.isProfile ? this.$route.params.id : this.authUser.id;

    this.setEditFields();
    this.getProfile();
  }
}
</script>
