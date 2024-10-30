<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import XButton from "@/Components/XButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { onBeforeMount } from "vue";

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
        quantity: 0,
    });
};

onBeforeMount(addProduct);

const genProductName = (index: number, field: string) => {
    return `product.${index}.${field}`;
};

const removeProduct = (index: number) => {
    form.products.splice(index, 1);
    if (form.products.length === 0) addProduct();
};
</script>

<template>
    <Head title="Create invoice" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">
                Create invoice
            </h1>

            <form
                @submit.prevent="form.patch(route('profile.update'))"
                class="mt-6 space-y-6"
            >
                <div>
                    <h2 class="font-bold text-lg mb-2">Billing info</h2>
                    <div class="flex gap-4">
                        <div class="w-full">
                            <div class="mb-2">
                                <InputLabel for="vendor" value="Vendor" />
                                <TextInput
                                    id="vendor"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.vendor"
                                    required
                                    multiline
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.vendor"
                                />
                            </div>
                            <a class="v-link text-sm">Load from last invoice</a>
                        </div>
                        <div class="w-full">
                            <InputLabel for="customer" value="Customer" />
                            <TextInput
                                id="customer"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.customer"
                                required
                                multiline
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.customer"
                            />
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="font-bold text-lg mb-2">Products</h2>
                    <div v-for="(product, index) of form.products">
                        <div class="flex gap-2 items-end mb-2">
                            <div class="w-4/5">
                                <InputLabel
                                    :for="genProductName(index, 'name')"
                                    value="Name"
                                />
                                <TextInput
                                    :id="genProductName(index, 'name')"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="product.name"
                                    required
                                />
                                <!-- <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors[
                                                genProductName(index, 'name')
                                            ].message
                                        "
                                    /> -->
                            </div>
                            <div class="w-1/5">
                                <InputLabel
                                    :for="genProductName(index, 'price')"
                                    value="Price"
                                />
                                <TextInput
                                    :id="genProductName(index, 'price')"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="product.price"
                                    required
                                />
                                <!-- <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors[
                                                genProductName(index, 'price')
                                            ].message
                                        "
                                    /> -->
                            </div>
                            <div class="w-1/5">
                                <InputLabel
                                    :for="genProductName(index, 'quantity')"
                                    value="Quantity"
                                />
                                <TextInput
                                    :id="genProductName(index, 'quantity')"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="product.quantity"
                                    required
                                />
                                <!-- <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors[
                                                genProductName(index, 'quantity')
                                            ].message
                                        "
                                    /> -->
                            </div>
                            <XButton @click="removeProduct(index)"></XButton>
                        </div>
                    </div>
                </div>

                <div>
                    <SecondaryButton
                        :disabled="form.processing"
                        @click="addProduct"
                    >
                        Add product
                    </SecondaryButton>
                </div>

                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
            </form>
        </template>
    </AuthenticatedLayout>
</template>

<style></style>
