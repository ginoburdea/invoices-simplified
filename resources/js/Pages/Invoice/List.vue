<script setup lang="ts">
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import SafeLink from "@/Components/SafeLink.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed, ref } from "vue";

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

const page = new URLSearchParams(window.location.search).get("page");

const getUrlWithSortOptions = (sortField: string, sortType: string) => {
    const params = new URLSearchParams();
    params.append("page", "1");
    params.append("sortField", sortField);
    params.append("sortType", sortType);
    return window.location.pathname + "?" + params.toString();
};

const sortOptions = [
    {
        label: "Latest",
        url: getUrlWithSortOptions("number", "desc"),
        sortField: "number",
        sortType: "desc",
    },
    {
        label: "Oldest",
        url: getUrlWithSortOptions("number", "asc"),
        sortField: "number",
        sortType: "asc",
    },
    {
        label: "Highest total",
        url: getUrlWithSortOptions("total", "desc"),
        sortField: "total",
        sortType: "desc",
    },
    {
        label: "Lowest total",
        url: getUrlWithSortOptions("total", "asc"),
        sortField: "total",
        sortType: "asc",
    },
];

const sortField = new URLSearchParams(window.location.search).get("sortField");
const sortType = new URLSearchParams(window.location.search).get("sortType");

const selectedSortLabel = sortOptions.filter(
    (option) => option.sortField === sortField && option.sortType === sortType
)[0].label;
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
                                :href="option.url"
                                :key="option.label"
                            >
                                {{ option.label }}
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>

            <template v-if="invoices.length > 0">
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
                        <tr v-for="invoice of invoices">
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
                <p class="text-center text-sm">
                    <SafeLink :href="prev_page_url" class="v-link">
                        Prev
                    </SafeLink>
                    <span class="mx-5">Page {{ page }}</span>
                    <SafeLink :href="next_page_url" class="v-link">
                        Next
                    </SafeLink>
                </p>
            </template>
            <p class="text-center">No invoices found</p>
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
