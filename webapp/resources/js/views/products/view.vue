<template>
  <page-component>
    <div class="container">
      <b-row cols="1">
        <b-col v-if="!isAuthenticated || authUser.type !== userTypeManager">
          <item-details
            :items="items"
            :awaiting-items="pending.product"
          >
            <template #cell(photo)="data">
              <b-img v-if="data.value" thumbnail rounded :src="`/storage/${data.value}`" fluid alt="Product photo"></b-img>
              <div class="text-secondary" v-else>No photo</div>
            </template>
          </item-details>
        </b-col>
        <b-col v-else>
          <item-details
            :items="items"
            :awaiting-items="pending.product"
            :edit-clicked="editClicked"
            :delete-clicked="deleteClicked"
          >
            <template #cell(photo)="data">
              <b-img v-if="data.value" thumbnail rounded :src="`/storage/${data.value}`" fluid alt="Product photo"></b-img>
              <div class="text-secondary" v-else>No photo</div>
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

import { LaravelResponse, Params } from "../../stores/api";
import router from "../../router";
import { ProductModel } from "../../models/product";
import { namespace } from "vuex-class";

const Auth = namespace("auth");


@Component({
  components: {
    PageComponent,
    ItemDetails,
  },
  computed: mapState({
    items: (state: any) => {
      return [state.api.product.data].map((p: ProductModel) => {
        return {
          id: p.id,
          name: p.name,
          price: `${p.price} €`,
          type: p.type,
          description: p.description,
          photo: p.photo_url,
        }
      });
    },
    pending: (state: any) => state.api.pending,
  }),
  methods: {
    ...mapActions([
        "getProduct",
        "putProduct",
        "deleteProduct",
    ]),
  },
})
export default class ProductView extends Vue {
  getProduct!: (obj: Params) => AxiosPromise;
  putProduct!: (obj: Params) => void;
  deleteProduct!: (obj: Params) => AxiosPromise;

  productId?: number | string;

  @Auth.Getter
  private isAuthenticated!: boolean;

  @Auth.Getter
  private authUser!: UserModel;

  userTypeManager: string = UserType.EMPLOYEE_MANAGER;

  items?: any;

  data() {
    return {}
  }

  editClicked() {
      router.push({ name: "put-product", params: { id: String(this.productId) } });
  }

  deleteClicked() {
      this
        .deleteProduct({ params: { id: String(this.productId) } })
        .then(() => {
            router.push({ name: "list-products" }).then(() => {
                createAlert(AlertType.Success, `Product "${this.items[0].name}" deleted successfuly!`);
            })
        }).catch((err) => {
            createAlert(AlertType.Danger, `Could not delete product "${this.items[0].name}": ${err}`)
        });
  }

  mounted() {
    this.productId = this.$route.params.id;
    this.getProduct({ params: { id: this.productId } })
      .catch((request) => {
          router.push({ name: "list-products" })
            .then(() => {
              if (request.response.data.errors) {
                let errors = request.response.data.errors;
                for (const error in errors) {
                  createAlert(AlertType.Danger, `Error fetching product (${this.productId}): ${error}: ${errors[error]}`);
                }
              } else {
                createAlert(AlertType.Danger, `Error fetching product (${this.productId}): ${request.response.data.message}`);
              }
          });
        })
  }
}
</script>
