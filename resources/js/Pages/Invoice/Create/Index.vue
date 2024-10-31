<script setup lang="ts">
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { onBeforeMount } from "vue";
import Products from "./Products.vue";
import BillingInfo from "./BillingInfo.vue";

const props = defineProps({
    last_vendor_info: {
        type: String,
        required: false,
    },
});

interface Product {
    name: string;
    price: number;
    quantity: number;
}

const form = useForm({
    vendor: "",
    customer: "",
    products: [] as Product[],
});

const addProduct = () => {
    form.products.push({
        name: "",
        price: 0,
        quantity: 1,
    });
};

onBeforeMount(addProduct);
</script>

<template>
    <Head title="Create invoice" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">
                Create invoice
            </h1>
        </template>

        <form
            @submit.prevent="form.post(route('invoices.store'))"
            class="space-y-8 v-card"
        >
            <div>
                <h2 class="font-bold text-lg mb-2">Billing info</h2>
                <BillingInfo
                    v-model:vendor="form.vendor"
                    v-model:customer="form.customer"
                    :vendor-error="form.errors.vendor"
                    :customer-error="form.errors.customer"
                    :last-vendor-info="props.last_vendor_info"
                ></BillingInfo>
            </div>

            <div>
                <h2 class="font-bold text-lg mb-2">Products</h2>

                <div class="mb-4">
                    <Products
                        v-model="form.products"
                        :errors="form.errors"
                        @add-product="addProduct"
                    ></Products>
                </div>

                <SecondaryButton
                    :disabled="form.processing"
                    @click="addProduct"
                >
                    Add product
                </SecondaryButton>
            </div>

            <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
        </form>
    </AuthenticatedLayout>
</template>

<style></style>
