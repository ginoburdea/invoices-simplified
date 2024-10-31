<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Title from "./Title.vue";
import Filters from "./Filters.vue";
import InvoicesTable from "./InvoicesTable.vue";
import InvoicesPagination from "./InvoicesPagination.vue";

defineProps({
    invoices: {
        type: Array,
        required: true,
    },
    prev_page_url: {
        type: String,
        required: false,
    },
    next_page_url: {
        type: String,
        required: false,
    },
});
</script>

<template>
    <Head title="Invoices" />

    <AuthenticatedLayout>
        <template #header>
            <Title></Title>
        </template>

        <div class="v-card overflow-x-auto">
            <Filters></Filters>

            <template v-if="invoices.length > 0">
                <InvoicesTable :invoices="(invoices as any[])"></InvoicesTable>
                <InvoicesPagination
                    :next-page-url="next_page_url"
                    :prev-page-url="prev_page_url"
                ></InvoicesPagination>
            </template>

            <p class="text-center" v-else>No invoices found</p>
        </div>
    </AuthenticatedLayout>
</template>

<style></style>
