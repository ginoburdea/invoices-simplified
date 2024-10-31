<script setup lang="ts">
interface Invoice {
    id: number;
    number: number;
    customer: string;
    total: number;
}

interface Props {
    invoices: Invoice[];
}

defineProps<Props>();
</script>

<template>
    <table class="mb-6 v-table">
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
                <td class="space-x-4">
                    <a class="v-link">Download</a>
                    <a class="v-link">View</a>
                </td>
            </tr>
        </tbody>
    </table>
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
