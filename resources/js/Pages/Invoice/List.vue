<script setup lang="ts">
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed, ref } from "vue";

interface Invoice {
    id: number;
    number: number;
    client: string;
    total: number;
}

const invoices: Invoice[] = [
    { id: 1, number: 1, client: "Client 1", total: 100 },
    { id: 2, number: 2, client: "Client 2", total: 200 },
    { id: 3, number: 3, client: "Client 3", total: 300 },
    { id: 4, number: 4, client: "Client 4", total: 400 },
    { id: 5, number: 5, client: "Client 5", total: 500 },
];

const hasPrevPage = false;
const page = 1;
const hasNextPage = true;

const sortOptions = [
    {
        label: "Latest",
        value: "number:desc",
    },
    {
        label: "Oldest",
        value: "number:asc",
    },
    {
        label: "Highest total",
        value: "total:desc",
    },
    {
        label: "Lowest total",
        value: "total:asc",
    },
];

const sort = ref<string>(sortOptions[0].value);
const selectedSortLabel = computed(
    () => sortOptions.filter((option) => option.value === sort.value)[0].label
);
</script>

<template>
    <Head title="Invoices" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">
                Invoices
            </h1>
        </template>

        <div class="v-card overflow-x-auto">
            <div>
                <div class="mb-6">
                    <Dropdown width="full">
                        <template #trigger>
                            <div
                                class="flex justify-between items-center v-input cursor-pointer"
                            >
                                <span>Sort by: {{ selectedSortLabel }}</span>
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M6.34317 7.75732L4.92896 9.17154L12 16.2426L19.0711 9.17157L17.6569 7.75735L12 13.4142L6.34317 7.75732Z"
                                        fill="currentColor"
                                    />
                                </svg>
                            </div>
                        </template>
                        <template #content>
                            <DropdownLink
                                v-for="option of sortOptions"
                                @click="sort = option.value"
                                :key="option.value"
                            >
                                {{ option.label }}
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>

            <table class="mb-6 v-table">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="invoice of invoices">
                        <td>{{ invoice.number }}</td>
                        <td class="w-full">{{ invoice.client }}</td>
                        <td>{{ invoice.total }}</td>
                        <td class="space-x-4">
                            <a class="v-link">Download</a>
                            <a class="v-link">View</a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="text-center text-sm">
                <a class="v-link" :class="{ 'v-link--disabled': !hasPrevPage }">
                    Prev</a
                >
                <span class="mx-5">Page {{ page }}</span>
                <a class="v-link" :class="{ 'v-link--disabled': !hasNextPage }">
                    Next</a
                >
            </p>
        </div>
    </AuthenticatedLayout>
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
