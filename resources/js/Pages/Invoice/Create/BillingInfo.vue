<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";

const vendor = defineModel("vendor", { type: String, required: true });
const customer = defineModel("customer", { type: String, required: true });

interface Props {
    vendorError?: string;
    customerError?: string;
    lastVendorInfo?: string;
}
const props = defineProps<Props>();

const useLastBillingInfo = () => {
    vendor.value = props.lastVendorInfo || "";
};
</script>

<template>
    <div class="flex gap-4">
        <div class="w-full">
            <div class="mb-2">
                <InputLabel for="vendor" value="Vendor" />
                <TextInput
                    id="vendor"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="vendor"
                    required
                    multiline
                />
                <InputError class="mt-2" :message="props.vendorError" />
            </div>
            <a
                class="v-link text-sm"
                v-if="props.lastVendorInfo"
                @click="useLastBillingInfo"
            >
                Load from last invoice
            </a>
        </div>
        <div class="w-full">
            <InputLabel for="customer" value="Customer" />
            <TextInput
                id="customer"
                type="text"
                class="mt-1 block w-full"
                v-model="customer"
                required
                multiline
            />
            <InputError class="mt-2" :message="props.customerError" />
        </div>
    </div>
</template>

<style></style>
