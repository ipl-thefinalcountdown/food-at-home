<template>
  <page-component>
    <div class="container">
      <item-edit :on-submit="onSubmit">
		<b-form-group
			id="fieldset-1"
			label="Enter profile photo"
		>
			<b-form-file
				v-model="file"
				required
				placeholder="Choose a file or drop it here..."
				drop-placeholder="Drop file here..."
			></b-form-file>
		</b-form-group>
      </item-edit>
    </div>
  </page-component>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";

import { mapState, mapActions } from "vuex";
import axios, { AxiosPromise } from "axios";

import PageComponent from "../../components/Page.vue";
import ItemEdit from "../../components/item/ItemAddEdit.vue";
import FormField from "../../components/form/FormField.vue";
import FormSearchableSelect from "../../components/form/FormSearchableSelect.vue"

import { AlertType, createAlert } from "../../utils/alert";

import { Params } from "../../stores/api";

import router from "../../router"
import { UserModel, UserType } from "../../models/user";
import { namespace } from "vuex-class";

const Auth = namespace("auth");

@Component({
  components: {
    PageComponent,
    ItemEdit
  },

  methods: {
    ...mapActions([
      "getUser",
    ]),
  },
})
export default class UserUploadPhotoView extends Vue {
    getUser!: (obj?: Params) => AxiosPromise;

    file?: any = null;

    @Auth.Getter
    private isAuthenticated!: boolean;

    @Auth.Getter
    public authUser!: UserModel;

    onSubmit() {
        let formData = new FormData();
        formData.append('photo_url', this.file);
        axios({
            url: (this.$route.params.id)
                ? `users/${this.$route.params.id}/photo`
                : `user/photo`,
            data: formData,
            headers: {
                'content-type': 'multipart/form-data'
            },
            method: 'POST'
        }).then(res => {
            // go back
            router.go(-1);
        }).catch((request) => {
            let errors = request.response.data.errors;
                for (const error in errors) {
                    createAlert(AlertType.Danger, `Error uploading photo (${this.file.name}): ${errors[error]}`);
                }
        });
    }

    mounted() {
        if (this.$route.params.id)
            this.getUser({ params: { id: this.$route.params.id } }).then((result) => {
                if (this.authUser?.type == UserType.EMPLOYEE_MANAGER && result.data?.type == UserType.CUSTOMER)
                    router.go(-1);
            }).catch((request) => {
            router.push({ name: "list-users" })
                .then(() => {
                if (request.response.data.errors) {
                    let errors = request.response.data.errors;
                    for (const error in errors) {
                        createAlert(AlertType.Danger, `Error fetching user (${this.$route.params.id}): ${error}: ${errors[error]}`);
                    }
                    } else {
                    createAlert(AlertType.Danger, `Error fetching user (${this.$route.params.id}): ${request.response.data.message}`);
                    }
                });
            })
    }
}
</script>
