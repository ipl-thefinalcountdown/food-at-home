<template>
  <page-component>
    <div class="container">
      <div class="justify-content-center">
        <searchable-table
          :items="items"
          :row-clicked="rowClicked"
          :filter-changed="filterChanged"
          :page-changed="pageChanged"
          :meta-total="total"
          :add-clicked="addClicked"
          :edit-clicked="editClicked"
          :delete-clicked="deleteClicked"
        />
      </div>
    </div>
  </page-component>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";
import { mapState, mapActions } from "vuex";

import PageComponent from "../../components/Page.vue";
import SearchableTable from "../../components/SearchableTable.vue";

import router from "../../router";
import { ProductModel } from "../../models/product";
import { AlertType, createAlert } from "../../utils/alert";

import { Params, LaravelResponse } from "../../stores/api";
import { AxiosPromise } from "axios";

@Component({
  components: {
    PageComponent,
    SearchableTable,
  },
  computed: {
    ...mapState({
      items: (state: any) => {
        return state.api.products.data.map((p: ProductModel) => {
          return {
            id: p.id,
            name: p.name,
            type: p.type,
            price: `${p.price} €`,
          };
        });
      },

      total: (state: any) => state.api.products.meta == undefined ? 0 : state.api.products.meta.total,

      pending: (state: any) => state.api.pending,
      error: (state: any) => state.api.error,
    }),
  },
  methods: {
    ...mapActions(["getProducts"]),
  },
})
export default class ProductListView extends Vue {
  getProducts!: (obj: Params) => void;

  filterText?: string = "";
  currentPage?: number | string;

  data() {
    let obj = this;

    return {
      rowClicked(record: ProductModel, index: number, event: Event) {
        router.push({
          name: "view-product",
          params: { id: String(record?.id) },
        });
      },

      filterChanged(text: string) {
        obj.getProducts({ params: { name: text } });
        obj.filterText = text;
      },

      pageChanged(page: string) {
          obj.currentPage = page;
          obj.getProducts({ params: { name: obj.filterText, page: page } });
      },

      addClicked(event: Event) {
          router.push({
              name: "new-product"
          });
      },

      editClicked(product: ProductModel, index: number, event: Event) {
          router.push({
              name: "edit-product"
          });
      },

      deleteClicked(product: ProductModel, index: number, event: Event) {
          router.push({
              name: "delete-product"
          });
      }
    };
  }

  mounted() {
    this.getProducts({ params: { name: "" } });
  }
}
</script>
