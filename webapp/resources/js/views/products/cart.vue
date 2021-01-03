<template>
  <page-component>
    <div class="container">
      <div class="justify-content-center">
        <simple-cart-table
          :items="cartItems"
          :delete-clicked="deleteClicked"
          :quantity-changed="quantityChanged"
        />
      </div>
      <div v-if="cartItems.length != 0">
        <b-row class="p-3">
          <b-col>
            <b-form-group
              id="fieldset-1"
              description="Let us know your request."
              label="Observations"
            >
              <b-form-textarea
                v-model="observationsText"
                placeholder="Enter something..."
                rows="3"
                max-rows="6"
              />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row class="p-3">
          <b-col>
            <h5>Total price: {{ cartPrice }} €</h5>
          </b-col>
          <b-col>
            <div class="d-flex flex-row-reverse bd-highlight">
              <b-button v-b-modal.checkout-modal variant="primary"
                >Checkout</b-button
              >
            </div>
          </b-col>
        </b-row>

        <b-modal
          id="checkout-modal"
          title="Checkout confirmation"
          @ok="orderOkClicked">
        <p><b>Selected Items:</b></p>
          <b-list-group v-for="item in cartItems" :key="item.id">
            <b-list-group-item
              class="d-flex justify-content-between align-items-center"
            >
              {{ item.productName }}
              <b-badge variant="primary" pill>{{ item.quantity }}</b-badge>
            </b-list-group-item>
          </b-list-group>
          <p v-if="observationsText" class="my-2"><b>Observations: </b>{{ observationsText }}</p>
          <p class="my-4"><b>Total: </b>{{ cartPrice }} €</p>
        </b-modal>
      </div>
    </div>
  </page-component>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";
import { mapState, mapActions } from "vuex";

import PageComponent from "../../components/Page.vue";
import SimpleCartTable from "../../components/SimpleCartTable.vue";

import router from "../../router";
import { ProductModel } from "../../models/product";
import { AlertType, createAlert } from "../../utils/alert";

import { Params, LaravelResponse } from "../../stores/api";
import { AxiosPromise } from "axios";

import { namespace } from "vuex-class";
import { CartItemModel } from "../../models/order";
const Cart = namespace("cart");

@Component({
  components: {
    PageComponent,
    SimpleCartTable,
  },
  methods: {
    ...mapActions(["createOrderCustomer"]),
  },
})
export default class CartView extends Vue {
  createOrderCustomer!: (obj: Params) => AxiosPromise;

  @Cart.Getter
  public cartItems!: Array<CartItemModel>;

  @Cart.Getter
  public cartPrice!: number;

  @Cart.Action
  public removeCartProductAction!: (index: number) => void;

  @Cart.Action
  public removeAllCartProductsAction!: () => void;

  @Cart.Action
  public changeProductQuantityAction!: (data: {
    index: number;
    quantity: number;
  }) => void;

  observationsText: string = "";

  orderOkClicked()
  {
    this.createOrderCustomer({
      data: {
        items: this.cartItems,
        notes: this.observationsText || ''
      }
    }).then(()=> {
      this.removeAllCartProductsAction();
      router.push({
          name: "index"});
    }).catch((err) => {
      createAlert(AlertType.Danger, `Could not create the order: ${err}`)
    })
  }

  deleteClicked(record: CartItemModel, index: number, event: Event) {
    this.removeCartProductAction(index);
  }

  quantityChanged(
    record: CartItemModel,
    index: number,
    event: string | number
  ) {
    this.changeProductQuantityAction({ index, quantity: Number(event) });
  }

  mounted() {}
}
</script>
