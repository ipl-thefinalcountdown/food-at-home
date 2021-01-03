<template>
  <page-component>
    <div class="container">
      <b-row>
        <b-col>
          <small-scrollable-table :items="order_items" />
        </b-col>
        <b-col cols="8">
          <item-details
            :items="items"
            :awaiting-items="pending.order"
            :custom-clicked="customClicked"
            :custom-text="customText"
            :custom-type="customType"
          >
            <!-- For price fields, lets consider euro -->
            <template #cell(status)="data">
              <span
                v-if="data.item.status"
                :class="`pr-2 pl-2 badge badge-pill ${statusColor(
                  data.item.status
                )}`"
              >
                {{ statusName(data.item.status) }}
              </span>
            </template>

            <!-- For price fields, lets consider euro -->
            <template #cell(total_price)="data">
              {{ data.item.total_price }} €
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
import {
  OrderModel,
  statusName,
  statusColor,
  OrderItemModel,
  OrderStatus,
} from "../../models/order";
import { objToArray } from "../../utils/hashmap";
import SmallScrollableTable from "../../components/SmallScrollableTable.vue";
import { namespace } from "vuex-class";

const Auth = namespace("auth");

@Component({
  components: {
    PageComponent,
    ItemDetails,
    SmallScrollableTable,
  },
  computed: mapState({
    items: (state: any) => {
      return [state.api.order.data].map((o: OrderModel) => {
        return {
          id: o.id,
          date: o.date,
          status: o.status,
          customer_name: o.customer_name,
          total_price: o.total_price,
          prepared_by: o.prepared_by,
          delivered_by: o.delivered_by,
          notes: o.notes,
        };
      });
    },
    order_items: (state: any) => {
      let val = state.api.order.data.items;
      if (val != undefined)
        return val.map((oi: OrderItemModel) => {
          return {
            product_name: oi.product_name,
            quantity: oi.quantity,
          };
        });
      return [];
    },
    pending: (state: any) => state.api.pending,
  }),
  methods: {
    ...mapActions([
      "getOrder",
      "prepareOrder",
      "pickupOrder",
      "deliverOrder",
      "cancelOrder",
      ]),
  },
  watch: {
    items: function () {
      (<any>this).updateCustomButton();
    },
  },
})
export default class OrderView extends Vue {
  getOrder!: (obj: Params) => AxiosPromise;
  prepareOrder!: (obj: Params) => AxiosPromise;
  pickupOrder!: (obj: Params) => AxiosPromise;
  deliverOrder!: (obj: Params) => AxiosPromise;
  cancelOrder!: (obj: Params) => AxiosPromise;

  @Auth.Getter
  private isAuthenticated!: boolean;
  @Auth.Getter
  public authUser!: UserModel;

  statusColor = statusColor;
  statusName = statusName;

  orderId?: number | string;

  customClicked: any = null;

  customType: string | null = null;
  customText: string | null = null;

  updateCustomButton() {
    let order: OrderModel = this.$store.state.api.order.data;

    if (!order) return;

    if (this.authUser.type == UserType.EMPLOYEE_MANAGER) {
      if (
        order.status != OrderStatus.DELIVERED &&
        order.status != OrderStatus.CANCELLED
      ) {
        // cancel order
        this.customText = "Cancel";
        this.customType = "danger";

        this.customClicked = this.cancelButton;
      } else {
        this.customClicked = null;
      }

      return;
    }

    if (this.authUser.type == UserType.EMPLOYEE_COOK) {
      switch (order.status) {
        case OrderStatus.PREPARING:
          this.customText = "Prepare";
          this.customType = "primary";
          this.customClicked = this.prepareButton;
          break;
        default:
          this.customClicked = null;
      }
      return;
    }

    if (this.authUser.type == UserType.EMPLOYEE_DELIVERYMAN) {
      switch (order.status) {
        case OrderStatus.READY:
          // transport order
          this.customText = "Pick-up";
          this.customType = "info";
          this.customClicked = this.pickupButton;
          break;
        case OrderStatus.TRANSIT:
          // deliver order
          this.customText = "Deliver";
          this.customType = "success";
          this.customClicked = this.deliverButton;
          break;
        default:
          this.customClicked = null;
      }
      return;
    }

    // default case
    this.customClicked = null;
  }

  cancelButton() {
    this.cancelOrder({ params: { id: this.orderId } })
      .then(res => {
        router.push({name:'list-orders'});
      })
      .catch(error => {
        createAlert(AlertType.Danger, `Can't cancel the order ! ${error}`);
      })
  }
  prepareButton() {
    this.prepareOrder({ params: { id: this.orderId } })
      .then(res => {
        router.push({name:'list-orders'});
      })
      .catch(error => {
        createAlert(AlertType.Danger, `Can't prepare the order ! ${error}`);
      })
  }
  pickupButton() {
    this.pickupOrder({ params: { id: this.orderId } })
      .then(res => {
        createAlert(AlertType.Success, `You picked the order!`);
        this.getOrderAPI();
      })
      .catch(error => {
        createAlert(AlertType.Danger, `Can't pick the order ! ${error}`);
      })
  }
  deliverButton() {
    this.deliverOrder({ params: { id: this.orderId } })
      .then(res => {
        router.push({name:'list-orders'});
      })
      .catch(error => {
        createAlert(AlertType.Danger, `Can't deliver the order ! ${error}`);
      })
  }

  getOrderAPI()
  {
    this.getOrder({ params: { id: this.orderId } }).catch((error) => {
      createAlert(AlertType.Danger, `Order ${this.orderId} not found!`);
    });
  }

  mounted() {
    this.orderId = this.$route.params.id;
    this.getOrderAPI();
  }
}
</script>
