<template>
  <div>
    <div class="d-flex flex-row-reverse bd-highlight">
      <a class="btn btn-danger" v-if="deleteClicked" @click.prevent="deleteClicked" role="button">Delete</a>
      <div class="pr-1">
        <a class="btn btn-warning" v-if="editClicked" @click.prevent="editClicked" role="button">Edit</a>
      </div>
    </div>
    <b-table
      :busy="awaitingItems"
      :items="items"
      borderless
      stacked
    >
      <!-- Busy loading template -->
      <template #table-busy>
        <div class="text-center text-secondary my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
      </template>

      <caption>Item details:</caption>
    </b-table>
  </div>
</template>

<script lang="ts">
import Vue from "vue"

export default Vue.extend({
    props: {
        items: Array,
        awaitingItems: Boolean,
        deleteClicked: Function,
        editClicked: Function
    }
})
</script>
