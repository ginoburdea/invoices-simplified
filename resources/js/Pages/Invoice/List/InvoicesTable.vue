<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

interface Props {
    invoices: Invoice[];
}

defineProps<Props>();
</script>

<template>
    <div class="overflow-auto mb-6">
        <table class="v-table w-full">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="invoice of invoices" :key="invoice.id">
                    <td>{{ invoice.number }}</td>
                    <td class="w-full">{{ invoice.customer }}</td>
                    <td>{{ invoice.total }}</td>
                    <td class="space-x-4 text-nowrap">
                        <a
                            :href="route('invoices.download', invoice.id)"
                            class="v-link"
                        >
                            Download
                        </a>
                        <Link
                            :href="route('invoices.edit', invoice.id)"
                            class="v-link"
                        >
                            View
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
.v-table {
    @apply w-fit min-w-full border-collapse text-left text-nowrap md:text-wrap;
}

.v-table thead {
    @apply bg-gray-200;
}

.v-table,
.v-table td,
.v-table th {
    @apply border border-gray-300;
}

.v-table tbody tr {
    @apply hover:bg-gray-50;
}

.v-table td,
.v-table th {
    @apply px-4 py-2;
}
</style>
