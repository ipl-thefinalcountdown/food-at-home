<template>
    <page-component>
        <div class="container">
            <item-add-edit v-if="itemLoaded" :on-submit="onSubmit" :on-reset="onReset">
                <form-field label="Name" placeholder="Insert name" v-model="form.name" />
                <form-field type="number" step=any label="Price" placeholder="Insert product price" v-model="form.price" />
                <form-searchable-select label="Product type" placeholder="Select the product type" v-model="form.type" :options="types" />
                <b-form-group label="Description">
                    <b-form-textarea id="Description" placeholder="Insert description" v-model="form.description" rows="3" max-rows="6" />
                </b-form-group>
            </item-add-edit>
            <div v-else class="text-center text-secondary my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
            </div>
        </div>
    </page-component>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";

import { mapState, mapActions } from "vuex";

import { ProductModel, ProductType } from "../../models/product";
import { deSnakeCase } from "../../utils/string";

import PageComponent from "../../components/Page.vue";
import ItemAddEdit from "../../components/item/ItemAddEdit.vue";
import FormField from "../../components/form/FormField.vue";
import FormSearchableSelect from "../../components/form/FormSearchableSelect.vue";
import { AlertType, createAlert } from "../../utils/alert";
import { AxiosPromise } from "axios";
import { Params } from "../../stores/api";

@Component({
    components: {
        PageComponent,
        ItemAddEdit,
        FormField,
        FormSearchableSelect,
    },

    computed: mapState({
        product: (state: any) => state.api.product.data,

        types: () => Object.entries(ProductType)
                .map(tuple => {
                    return {
                        value: tuple[1],
                        text: `${deSnakeCase(tuple[0])}`,
                    };
                }),

        pending: (state: any) => state.api.pending,
        error: (state: any) => state.api.error,
    }),

    methods: {
        ...mapActions(["getProduct"]),
    }
})

export default class ProductAddEditView extends Vue {
    getProduct!: (obj?: Params) => AxiosPromise;

    product?: ProductModel;
    form?: ProductModel;
    itemLoaded?: boolean;
    isEdit?: boolean;

    data() {
        let obj = this;

        return {
            form: {},
            isEdit: false,
            itemLoaded: false,

            onSubmit() {
                if (obj.isEdit) {

                } else {

                }
            },

            onReset(event: Event) {
                event.preventDefault();
                obj.setEditFields();
            }
        }
    }

    setEditFields() {
        let productId = this.$route.params.id;

        if (this.isEdit) {
            this.getProduct({ params: { id: productId } })
                .then(() => {
                    this.form = {
                        name: this.product?.name,
                        price: this.product?.price,
                        type: this.product?.type,
                        description: this.product?.description,
                        photo_url: this.product?.photo_url,
                };

                this.itemLoaded = true;
            }).catch((err) => {
                createAlert(AlertType.Danger, `Error fetching project ${productId}: ${err}`);
            })
        }
    }

    mounted() {
        let pathArr = this.$route.path.split("/");

        if (pathArr[pathArr.length - 1] == "edit")
            this.isEdit = true;
        else
            this.itemLoaded = true;

        this.setEditFields();
    }
}
</script>
