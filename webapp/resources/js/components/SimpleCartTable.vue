<template>
  <div class="container-fluid">
    <div class="d-flex flex-row-reverse bd-highlight">
      <!-- Add button -->
      <div v-if="addClicked" class="pl-3">
        <b-button
          @click.prevent="addClicked"
          variant="secondary"
        >
          <i class="fas fa-plus"></i>
        </b-button>
      </div>
    </div>

    <div class="pt-3 overflow-auto">

      <!-- Table -->
      <b-table
        :items="items"
        :fields="tableFields()"
        :no-select-on-click="true"
        :head-variant="headVariant"
        show-empty
        responsive="sm"
      >

        <!-- Head of action column template -->
        <template #head(__actions)="scope">
          <div class="text-nowrap text-right">
            {{ (rows && !noActionLabel) ? scope.label : '' }}
          </div>
        </template>

        <!-- For price fields, lets consider euro -->
        <template #cell(price)="data">
            {{ data.item.price * data.item.quantity }} €
        </template>

        <template #cell(quantity)="scope">
                <b-form-input class="col-md-3 col-lg-2"
                    type="number"
                    step="1"
                    :value="scope.item.quantity"
                    @change="quantityChanged(scope.item, scope.index, $event)"
                />
        </template>

        <!-- Cells of action column template -->
        <template #cell(__actions)="scope">
          <div class="text-nowrap text-right">

            <!-- Edit icon button -->
            <b-link
              v-if="editClicked"
              @click.prevent="editClicked(scope.item, scope.index, $event)"
              class="text-secondary"
            >
              <i class="fas fa-edit fa-lg"></i>
            </b-link>

            <!-- Delete icon button -->
            <b-link
              v-if="deleteClicked"
              @click.prevent="deleteClicked(scope.item, scope.index, $event)"
              class="text-danger"
            >
              <i class="fas fa-trash-alt fa-lg"></i>
            </b-link>
          </div>
        </template>

        <!-- User slot -->
        <slot></slot>
      </b-table>
    </div>
  </div>
</template>

<script lang="ts">
import Vue from "vue";

export default Vue.extend({
  props: {
    /// table items
    items: Array,
    /// table fields (table header and footer columns)
    fields: Array,
    /// callback when a row is clicked (not used, yet)
    rowClicked: Function,
    /// callback when add button is clicked
    addClicked: Function,
    /// callback when row edit button is clicked
    editClicked: Function,
    /// callback when row delete button is clicked
    deleteClicked: Function,
    /// callback when quantity field change
    quantityChanged: Function,
    // boolean for defining header action label
    noActionLabel: Boolean,
  },
  data() {
    let obj = this;
    return {
      // == table configurations == //
      headVariant: 'dark',

      /// filter text input field
      filteredText: <string>"",

      /// table busy locker (to display loading template)
      awaitingSearch: <boolean>true,

      /// timeout handler id
      timeout: <number | undefined>undefined,

      /// custom table fields to include actions
      tableFields(): Array<any> | undefined {
        if (obj.fields) return obj.fields;
        else if (
          obj.deleteClicked == undefined
          && obj.editClicked == undefined)
          return undefined;
        else
        {
            let fields = Object.keys(<object>obj.items[0] ?? {})
            .filter((val) => val != "id");
          return fields.concat(<any>{
            key: "__actions",
            name: "Actions",
          });
        }
      },
    };
  },
  computed: {
    /// compute total number of rows
    rows(): number {
      return this.items.length;
    },
  },
  watch: {
    // every time items are updated, busy state is unlocked
    items: function (val) {
      this.awaitingSearch = false;
    },
  },
});
</script>
