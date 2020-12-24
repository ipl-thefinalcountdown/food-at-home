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
                <b-form-group id="fieldset-1" label="Choose an image">
                    <b-form-file v-if="isEdit"
                        v-model="image"
                        placeholder="Choose an image or drop it here..."
                        drop-placeholder="Drop image here..."
                    ></b-form-file>
                    <b-form-file v-else
                        required
                        v-model="image"
                        placeholder="Choose an image or drop it here..."
                        drop-placeholder="Drop image here..."
                    ></b-form-file>
                </b-form-group>
                <b-form-row v-if="isEdit">
                    <b-col align="center">
                        <b-img thumbnail rounded :src="`/storage/${photo_url}`" fluid alt="Product photo"></b-img>
                    </b-col>
                </b-form-row>
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
import Axios, { AxiosPromise } from "axios";
import { Params } from "../../stores/api";
import router from "../../router";

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
        ...mapActions(["getProduct", "putProduct", "postProduct"]),
    }
})

export default class ProductAddEditView extends Vue {
    getProduct!: (obj?: Params) => AxiosPromise;
    putProduct!: (obj?: Params) => AxiosPromise;
    postProduct!: (obj?: Params) => AxiosPromise;

    product?: ProductModel;
    form?: ProductModel;
    itemLoaded?: boolean;
    isEdit?: boolean;
    image?: any;
    photo_url?: string;

    data() {
        let obj = this;

        return {
            form: {},
            isEdit: false,
            itemLoaded: false,
            image: null,

            onSubmit() {
                let formData = new FormData();
                for (let e of Object.entries(obj.form || {})) {
                    formData.append(e[0], e[1]);
                }

                if (obj.image != undefined && obj.image != null)
                    formData.append("photo_url", obj.image);

                if (obj.isEdit) {
                    console.log(formData);
                    Axios({
                        url: `products/${obj.product?.id}`,
                        data: formData,
                        headers: {
                            'content-type': 'multipart/form-data'
                        },
                        method: 'POST'
                    }).then(() => {
                        router.go(-1);
                    }).catch((err) => {
                        createAlert(AlertType.Danger, `Error updating product ${obj.product?.name}: ${err}`);
                    })
                } else {
                    Axios({
                        url: `products`,
                        data: formData,
                        headers: {
                            'content-type': 'multipart/form-data'
                        },
                        method: 'POST'
                    }).then(() => {
                        router.go(-1);
                    }).catch((err) => {
                        createAlert(AlertType.Danger, `Error creating product: ${err}`);
                    })
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
                };
                this.photo_url = this.product?.photo_url;

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
