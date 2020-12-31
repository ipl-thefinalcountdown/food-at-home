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

      <!-- Filter text input -->
      <div v-if="filterChanged" class="pr-0 col-md-3">
        <b-form-input
          v-model="filteredText"
          placeholder="Filter search"
        ></b-form-input>
      </div>
    </div>

    <div class="pt-3 overflow-auto">

      <!-- Table -->
      <b-table
        :items="items"
        :fields="tableFields()"
        :per-page="perPage"
        @row-clicked="rowClicked"
        :no-select-on-click="true"
        :busy="awaitingSearch"
        :head-variant="headVariant"
        hover
        show-empty
        responsive="sm"
      >
        <!-- Busy loading template -->
        <template #table-busy>
          <div class="text-center text-secondary my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>

        <!-- Head of action column template -->
        <template #head(__actions)="scope">
          <div class="text-nowrap text-right">
            {{ (rows && !noActionLabel) ? scope.label : '' }}
          </div>
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

        <template v-for="slotName in Object.keys($scopedSlots)" v-slot:[slotName]="slotScope">
            <slot :name="slotName" v-bind="slotScope"></slot>
        </template>

        <!-- User slot -->
        <slot></slot>
      </b-table>

      <!-- Pagination -->
      <b-pagination
        v-model="currentPage"
        :total-rows="metaTotal"
        :per-page="perPage"
        aria-controls="my-table"
        align="center"
        @change="pageChanged"
      />
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
    /// callback when a row is clicked
    rowClicked: Function,
    /// callback when a filter box change it's state
    filterChanged: Function,
    /// callback when add button is clicked
    addClicked: Function,
    /// callback when row edit button is clicked
    editClicked: Function,
    /// callback when row delete button is clicked
    deleteClicked: Function,
    // boolean for defining header action label
    noActionLabel: Boolean,

    pageChanged: Function,

    metaTotal: [Number, String],
  },
  data() {
    let obj = this;
    return {
      // == pagination configurations == //

      /// current table page
      currentPage: <number>1,
      /// number of rows per page
      perPage: <number>15,

      lastPage: <number | string | undefined>undefined,

      // == table configurations == //
      headVariant: 'dark',

      // == filtering related reactive data == //

      /// filter text input field
      filteredText: <string>"",

      /// table busy locker (to display loading template)
      awaitingSearch: <boolean>true,

      /// timeout handler id
      timeout: <number | undefined>undefined,

      /// custom table fields to include actions
      tableFields(): Array<any> | undefined {
        if (obj.fields) return obj.fields;
        else if (obj.deleteClicked == undefined && obj.editClicked == undefined)
          return undefined;
        else
          return Object.keys(<object>obj.items[0] ?? {}).concat(<any>{
            key: "__actions",
            name: "Actions",
          });
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
    // this is needed to reduce possible calls to the API
    filteredText: function (val) {
      this.awaitingSearch = true;
      if (this.timeout !== undefined) clearTimeout(this.timeout);

      // set a timeout before calling filterChanged callback
      this.timeout = window.setTimeout(() => {
        this.timeout = undefined;
        this.filterChanged(this.filteredText);
      }, 400);
    },

    // every time items are updated, busy state is unlocked
    items: function (val) {
      this.awaitingSearch = false;
    },
  },
});
</script>
