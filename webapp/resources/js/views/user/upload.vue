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
import axios from "axios";

import PageComponent from "../../components/Page.vue";
import ItemEdit from "../../components/item/ItemAddEdit.vue";
import FormField from "../../components/form/FormField.vue";
import FormSearchableSelect from "../../components/form/FormSearchableSelect.vue"

import { AlertType, createAlert } from "../../utils/alert";

import { Params } from "../../stores/api";

import router from "../../router"

@Component({
  components: {
    PageComponent,
    ItemEdit
  },
})
export default class UserUploadPhotoView extends Vue {
    file?: any = null

    onSubmit() {
        let formData = new FormData();
        formData.append('photo', this.file);
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
        }).catch(err => {
            createAlert(
                AlertType.Danger,
                `Error uploading files ${this.file.name}: ${err}`
            );
        });
    }
}
</script>
