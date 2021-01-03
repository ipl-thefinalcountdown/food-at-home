<template>
  <div>
    <div class="d-flex flex-row-reverse bd-highlight">
        <div v-if="deleteClicked" class="pr-1">
        <a class="btn btn-danger" @click.prevent="deleteClicked" role="button">Delete</a>
      </div>
      <div v-if="editClicked" class="pr-1">
        <a class="btn btn-warning" @click.prevent="editClicked" role="button">Edit</a>
      </div>
      <div v-if="customClicked" class="pr-1">
          <a :class="`btn btn-${customType}`" @click.prevent="customClicked" role="button">{{ customText }}</a>
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

      <template v-for="slotName in Object.keys($scopedSlots)" v-slot:[slotName]="slotScope">
        <slot :name="slotName" v-bind="slotScope"></slot>
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
        editClicked: Function,
        customClicked: Function,
        customText: String,
        customType: String
    }
})
</script>
