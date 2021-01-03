<template>
  <div class="container-fluid">
    <b-table
      striped
      responsive="sm"
      small sticky-header
      show-empty hover
		:fields="tableFields()"
		:items="items"
    :busy="awaitingSearch"
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

        <!-- Delete icon button -->
        <b-link
          v-if="deleteClicked"
          @click.prevent="deleteClicked(scope.item, scope.index, $event)"
          class="text-danger"
        >
          <i class="fas fa-minus"></i>
        </b-link>
      </div>
    </template>

    <!-- User slot -->
    <slot></slot>
  </b-table>
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
    /// callback when row delete button is clicked
    deleteClicked: Function,
    // boolean for defining header action label
    noActionLabel: Boolean,
    // boolean for disabling busy state
    disableBusyState: Boolean,
  },
  computed: {
    /// compute total number of rows
    rows(): number {
      return this.items.length;
    },
  },
  data() {
    let obj = this;
    return {
      // == pagination configurations == //

      /// table busy locker (to display loading template)
      awaitingSearch: <boolean>true,

      /// timeout handler id
      timeout: <number | undefined>undefined,

      /// custom table fields to include actions
      tableFields(): Array<any> | undefined {
        let fields : Array<any>;
        if (obj.fields) fields = obj.fields;
        else fields = Object.keys(<object>obj.items[0] ?? {});

        if (obj.deleteClicked == undefined)
            return fields;
          else
            return fields.concat(<any>{
                key: "__actions",
                name: "Actions",
              });
      },
    };
  },
  watch: {
    // every time items are updated, busy state is unlocked
    items: function (val) {
      this.awaitingSearch = false;
    },
  },
  mounted() {
    if(this.disableBusyState)
      this.awaitingSearch = false;
  }
});
</script>
