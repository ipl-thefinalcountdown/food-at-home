<template>
  <b-form-group :label="label" :description="description">
    <small-scrollable-table
		:fields="fields"
		:items="items"
		:delete-clicked="deleteClicked"
		:disable-busy-state="disableBusyState"
	/>
		<div class="d-flex flex-row-reverse bd-highlight">
      <!-- Add button -->
      <div class="pl-3 pr-3">
        <b-button
			@click.prevent="addClicked"
          variant="secondary"
        >
          <i class="fas fa-plus"></i>
        </b-button>
      </div>

		<!-- value input -->
	  <div class="pr-0 col-md-4">
        <b-form-input
          v-model="valueInput"
		  :type="valueType"
      	  :step="valueStep"
          :placeholder="`${computedFields[1]}`"
        ></b-form-input>
      </div>

      <!-- key input -->
      <div class="pr-0 col-md-4">
        <b-form-input
          v-model="keyInput"
		  :type="keyType"
      	  :step="keyStep"
          :placeholder="`${computedFields[0]}`"
        ></b-form-input>
      </div>
    </div>
  </b-form-group>
</template>

<script lang="ts">
import Vue from "vue";

import { objToArray } from "../../utils/hashmap";
import { fieldKeys, BootstrapFields, prettyFields } from "../../utils/fields";

import SmallScrollableTable from "../../components/SmallScrollableTable.vue";
import FormField from "../../components/form/FormField.vue";

export default Vue.extend({
  components: {
	  SmallScrollableTable,
	  FormField
  },
  props: {
	  value: [Object, Map],
	  fields: Array,
      label: String,
      description: String,
	  placeholder: String,
	  keyType: String,
	  keyStep: String,
	  valueType: String,
	  valueStep: String,
	  disableBusyState: Boolean
  },
  computed: {
	  items() {
		if(this.value != undefined)
			return objToArray(this.value, <string[]>fieldKeys(<BootstrapFields>this.fields));
		return [];
	  },
	  computedFields() {
		  return <string[]>prettyFields(<BootstrapFields>this.fields);
	  }
  },
  model: {
      prop: 'value',
      event: 'onFieldChanged'
  },
  methods: {
    handleInput(value : any)
    {
      this.$emit('onFieldChanged', value);
    }
  },
  data() {
	  let obj = this;
	  return {
		  keyInput: '',
		  valueInput: '',
		  deleteClicked(record: Map<number, number>, index: number, event: Event) {
			let newValue = { ...obj.value };
			delete newValue[Object.values(record)[0]];

			obj.$emit('onFieldChanged', newValue);
		  },
		  addClicked() {
			  let newValue = { ...obj.value };
				newValue[(<any>obj).keyInput] = (<any>obj).valueInput;

			obj.$emit('onFieldChanged', newValue);
		  }
	  };
  }
});
</script>
