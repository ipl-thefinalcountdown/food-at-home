<template>
  <b-form-group :label="label" :description="description">
	<model-select :options="options" :value="value" @input="handleInput" :placeholder="placeholder" />
  </b-form-group>
</template>

<script lang="ts">
import Vue from "vue";

import { ModelSelect } from 'vue-search-select';

export default Vue.extend({
	 props: {
	  value: Number,
      label: String,
      description: String,
	  placeholder: String,
	  options: Array
  },
  model: {
      prop: 'value',
      event: 'onFieldChanged'
  },
  methods: {
    handleInput(value : string)
    {
      this.$emit('onFieldChanged', value);
    }
  },
  components: {
	  ModelSelect
  }
});
</script>
