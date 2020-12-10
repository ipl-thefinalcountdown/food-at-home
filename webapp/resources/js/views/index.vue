<template>
    <page-component />
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";

import PageComponent from "../components/Page.vue";

@Component({
    components: {
        PageComponent
    }
})
export default class IndexView extends Vue {}
</script>
