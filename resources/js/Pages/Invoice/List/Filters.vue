<script setup lang="ts">
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";

const getUrlWithSortOptions = (sortField: string, sortType: string) => {
    const params = new URLSearchParams();
    params.append("page", "1");
    params.append("sortField", sortField);
    params.append("sortType", sortType);
    return window.location.pathname + "?" + params.toString();
};

const getUrlWithSearchQuery = (query: string) => {
    const params = new URLSearchParams(window.location.search);
    params.set("page", "1");

    if (query) {
        params.set("query", query);
    } else {
        params.delete("query");
    }

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

const initialSearchText = new URLSearchParams(window.location.search).get(
    "query"
);

const searchText = ref(initialSearchText || "");
const searchTextError = ref("");
</script>

<template>
    <div class="mb-6 flex flex-col md:flex-row justify-between gap-6">
        <div>
            <InputLabel for="search" value="Search" />
            <div class="flex gap-2 flex-row">
                <TextInput
                    id="search"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="searchText"
                    placeholder="Number or client"
                />
                <Link
                    :href="getUrlWithSearchQuery(searchText)"
                    class="v-icon-button mt-1"
                >
                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M18.319 14.4326C20.7628 11.2941 20.542 6.75347 17.6569 3.86829C14.5327 0.744098 9.46734 0.744098 6.34315 3.86829C3.21895 6.99249 3.21895 12.0578 6.34315 15.182C9.22833 18.0672 13.769 18.2879 16.9075 15.8442C16.921 15.8595 16.9351 15.8745 16.9497 15.8891L21.1924 20.1317C21.5829 20.5223 22.2161 20.5223 22.6066 20.1317C22.9971 19.7412 22.9971 19.1081 22.6066 18.7175L18.364 14.4749C18.3493 14.4603 18.3343 14.4462 18.319 14.4326ZM16.2426 5.28251C18.5858 7.62565 18.5858 11.4246 16.2426 13.7678C13.8995 16.1109 10.1005 16.1109 7.75736 13.7678C5.41421 11.4246 5.41421 7.62565 7.75736 5.28251C10.1005 2.93936 13.8995 2.93936 16.2426 5.28251Z"
                            fill="currentColor"
                        />
                    </svg>
                </Link>
            </div>
            <InputError class="mt-2" :message="searchTextError" />
        </div>
        <div>
            <InputLabel value="Sort" class="md:text-right mb-1" />

            <Dropdown width="full">
                <template #trigger>
                    <div
                        class="flex justify-between items-center v-input cursor-pointer gap-4"
                    >
                        <span>{{ selectedSortLabel }}</span>
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
</template>

<style></style>
