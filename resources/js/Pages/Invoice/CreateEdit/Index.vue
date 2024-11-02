<script setup lang="ts">
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed, onBeforeMount } from "vue";
import Products from "./Products.vue";
import BillingInfo from "./BillingInfo.vue";
import { Invoice, InvoiceProduct } from "@/types/index.js";

interface CreateProps {
    action: "create";
    last_vendor_info?: string;
}

interface UpdateProps {
    action: "update";
    invoice: Invoice;
    invoice_products: InvoiceProduct[];
    last_vendor_info: undefined;
}

type Props = CreateProps | UpdateProps;

const props = defineProps<Props>();

const form = useForm({
    vendor: "",
    customer: "",
    products: [] as InvoiceProduct[],
});

onBeforeMount(() => {
    if (props.action === "update") {
        form.vendor = props.invoice.vendor;
        form.customer = props.invoice.customer;

        form.products = props.invoice_products.map((product) => ({
            name: product.name,
            price: product.price,
            quantity: product.quantity,
        }));

        return;
    }

    addProduct();
});

const addProduct = () => {
    form.products.push({
        name: "",
        price: 0,
        quantity: 1,
    });
};

const title = computed(() =>
    props.action === "create"
        ? "Create invoice"
        : `Invoice ${props.invoice.number}`
);
const submitFormFn = computed(() =>
    props.action === "create"
        ? () => form.post(route("invoices.store"))
        : () => form.patch(route("invoices.update", props.invoice.id))
);
</script>

<template>
    <Head :title="title" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">
                {{ title }}
            </h1>
        </template>

        <form @submit.prevent="submitFormFn" class="space-y-8 v-card">
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
