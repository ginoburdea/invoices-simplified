<script setup lang="ts">
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

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
</template>

<style></style>
