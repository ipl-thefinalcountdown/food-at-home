<template>
    <page-component>
        <b-container>
            <b-img center fluid src="/img/foodhome.png" alt="Center image"></b-img>
        </b-container>
    </page-component>
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
