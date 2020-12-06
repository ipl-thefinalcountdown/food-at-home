<template>
  <div>
    <b-form
      @submit.prevent="onSubmit"
      @reset="(onReset) ? onReset($event) : ()=>{}">
		<slot></slot>

      <b-button type="submit" variant="primary">Submit</b-button>
      <b-button type="reset" variant="danger">Reset</b-button>
    </b-form>
  </div>
</template>


<script lang="ts">
import Vue from "vue";

export default Vue.extend({
  props: {
    onReset: Function,
    onSubmit: Function,
  },
});
</script>
