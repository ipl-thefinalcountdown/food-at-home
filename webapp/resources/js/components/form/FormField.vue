<template>
  <b-form-group :label="label" :description="description">
    <b-form-input
      :type="type"
      :step="step"
      :value="value"
      @input="handleInput"
      required
      :placeholder="placeholder"
    ></b-form-input>
  </b-form-group>
</template>

<script lang="ts">
import Vue from "vue";

export default Vue.extend({
	 props: {
      type: String,
      step: String,
      value: String,
      label: String,
      description: String,
      placeholder: String
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
  }
});
</script>
