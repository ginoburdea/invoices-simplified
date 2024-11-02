<script setup lang="ts">
import XButton from "@/Components/XButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputField from "@/Components/InputField.vue";
import { InvoiceProduct } from "@/types/index.js";
import ProductsRow from "./ProductsRow.vue";

const products = defineModel<InvoiceProduct[]>({ required: true });
const emit = defineEmits(["addProduct"]);

interface Props {
    errors: Record<string, string>;
}
const props = defineProps<Props>();

const genProductName = (index: number, field: string) => {
    return `products.${index}.${field}`;
};

const removeProduct = (index: number) => {
    products.value.splice(index, 1);
    if (products.value.length === 0) emit("addProduct");
};

const rowHasError = (errors: Record<string, string>, rowIndex: number) => {
    for (const field in errors) {
        const regex = new RegExp("^products\." + rowIndex + "\..+$");

        if (field.match(regex)) return true;
    }

    return false;
};
</script>

<template>
    <div v-for="(product, index) of products" class="mb-8 md:mb-4">
        <ProductsRow>
            <template #name-column>
                <InputLabel :for="genProductName(index, 'name')" value="Name" />
                <InputField
                    :id="genProductName(index, 'name')"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="product.name"
                    required
                />
            </template>
            <template #price-column>
                <InputLabel
                    :for="genProductName(index, 'price')"
                    value="Price"
                />
                <InputField
                    :id="genProductName(index, 'price')"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="product.price"
                    required
                />
            </template>
            <template #quantity-column>
                <InputLabel
                    :for="genProductName(index, 'quantity')"
                    value="Quantity"
                />
                <InputField
                    :id="genProductName(index, 'quantity')"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="product.quantity"
                    required
                />
            </template>
            <template #button-column>
                <InputLabel value="." class="invisible mb-1" />
                <XButton @click="removeProduct(index)"></XButton>
            </template>
        </ProductsRow>

        <ProductsRow v-if="rowHasError(props.errors, index)">
            <template #name-column>
                <InputError
                    class="mt-2"
                    :message="props.errors[genProductName(index, 'name')]"
                />
            </template>
            <template #price-column>
                <InputError
                    class="mt-2"
                    :message="props.errors[genProductName(index, 'price')]"
                />
            </template>
            <template #quantity-column>
                <InputError
                    class="mt-2"
                    :message="props.errors[genProductName(index, 'quantity')]"
                />
            </template>
            <template #button-column>
                <XButton
                    class="invisible"
                    @click="removeProduct(index)"
                ></XButton>
            </template>
        </ProductsRow>
    </div>
</template>

<style></style>
