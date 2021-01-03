<template>
  <page-component>
    <div class="container">
      <div class="justify-content-center">
        <searchable-table
          :items="items"
          :row-clicked="rowClicked"
          :page-changed="pageChanged"
          :meta-total="total"
        >

		<!-- For price fields, lets consider euro -->
        <template #cell(status)="data">
			<span :class="`pr-2 pl-2 badge badge-pill ${statusColor(data.item.status)}`">
				{{ statusName(data.item.status) }}
			</span>
        </template>

		<!-- For price fields, lets consider euro -->
        <template #cell(total_price)="data">
            {{ data.item.total_price }} €
        </template>

        </searchable-table>
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
import { UserModel, UserType } from "../../models/user";
import { AlertType, createAlert } from "../../utils/alert";

import { Params, LaravelResponse } from "../../stores/api";
import { AxiosPromise } from "axios";

import { namespace } from "vuex-class";
import { OrderModel, OrderStatus, statusName, statusColor } from "../../models/order";
import { deSnakeCase } from "../../utils/string";
const Cart = namespace("cart");
const Auth = namespace("auth");


@Component({
  components: {
    PageComponent,
    SearchableTable,
  },
  computed: {
    ...mapState({
      items(state: any) {
        return state.api.orders.data.map((o: OrderModel) => {
          let obj = {
			id: o.id,
            status: o.status,
			total_price: o.total_price
		  };

		  if(this.authUser.type == UserType.EMPLOYEE_MANAGER)
		  	return {
				  ...obj,
				  customer_name: o.customer_name,
			  }
			else return obj;
        });
      },

      total: (state: any) => state.api.orders.meta == undefined ? 0 : state.api.orders.meta.total,

      pending: (state: any) => state.api.pending,
      error: (state: any) => state.api.error,
    }),
  },
  methods: {
	...mapActions([
        "getOrdersCustomer",
        "getOrdersCook",
        "getOrdersDeliveryman",
        "getOrders",])
  }
})
export default class ProductListView extends Vue {
  getOrdersCustomer!: (obj: Params) => void;
  getOrders!: (obj: Params) => void;
  getOrdersDeliveryman!: (obj: Params) => void;
  getOrdersCook!: (obj: Params) => void;

@Auth.Getter
  private isAuthenticated!: boolean;
  @Auth.Getter
  public authUser!: UserModel;

  currentPage: number | string = 1;
  statusColor = statusColor;
  statusName = statusName;

  timer?: number;

	rowClicked(record: OrderModel, index: number, event: Event) {
        router.push({
          name: "view-order",
          params: { id: String(record?.id) },
        });
      }

getAPIOrders(page: number | string = 1) {
	switch(this.authUser.type) {
		case UserType.CUSTOMER:
            this.getOrdersCustomer({ params: { page: page } });
            break;
		case UserType.EMPLOYEE_MANAGER:
            this.getOrders({ params: { page: page } });
            break;
        case UserType.EMPLOYEE_COOK:
            this.getOrdersCook({ params: { page: page } });
            break;
        case UserType.EMPLOYEE_DELIVERYMAN:
            this.getOrdersDeliveryman({ params: { page: page } });
            break;
	}
}

  pageChanged(page: string) {
		this.currentPage = page;
		this.getAPIOrders(page);
  }

  triggerOrdersTimer()
  {
    let obj = this;
    this.timer = window.setTimeout(function()
    {
      obj.getAPIOrders(obj.currentPage);
      obj.triggerOrdersTimer();
    }, 3000);
  }

  mounted() {
    // get first page and repeat
    this.getAPIOrders();
    this.triggerOrdersTimer();
  }

  beforeDestroy() {
    clearTimeout(this.timer);
  }
}
</script>
