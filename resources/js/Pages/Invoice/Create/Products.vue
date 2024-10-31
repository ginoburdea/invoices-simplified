<script setup lang="ts">
import XButton from "@/Components/XButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";

interface Product {
    name: string;
    price: number;
    quantity: number;
}

const products = defineModel<Product[]>({ required: true });
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
    <div v-for="(product, index) of products">
        <div class="flex gap-2 items-end mb-2">
            <div class="w-4/5">
                <InputLabel :for="genProductName(index, 'name')" value="Name" />
                <TextInput
                    :id="genProductName(index, 'name')"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="product.name"
                    required
                />
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
            </div>
            <XButton @click="removeProduct(index)"></XButton>
        </div>
        <div class="flex gap-2 mb-2" v-if="rowHasError(props.errors, index)">
            <div class="w-4/5">
                <InputError
                    class="mt-2"
                    :message="props.errors[genProductName(index, 'name')]"
                />
            </div>
            <div class="w-1/5">
                <InputError
                    class="mt-2"
                    :message="props.errors[genProductName(index, 'price')]"
                />
            </div>
            <div class="w-1/5">
                <InputError
                    class="mt-2"
                    :message="props.errors[genProductName(index, 'quantity')]"
                />
            </div>
            <XButton class="invisible" @click="removeProduct(index)"></XButton>
        </div>
    </div>
</template>

<style></style>
