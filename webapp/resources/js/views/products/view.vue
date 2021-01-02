<template>
  <page-component>
    <div class="container">
      <b-row cols="1">
        <b-col>
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

import { LaravelResponse, Params } from "../../stores/api";
import router from "../../router";
import { ProductModel } from "../../models/product";


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
    ...mapActions(["getProduct"]),
  },
})
export default class ProductView extends Vue {
  getProduct!: (obj: Params) => AxiosPromise;

  productId?: number | string;

  data() {
    return {}
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
