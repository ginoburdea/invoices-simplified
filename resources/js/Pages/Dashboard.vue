<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { router } from "@inertiajs/vue3";
import { Line as LineChart } from "vue-chartjs";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    LineController,
    LineElement,
    PointElement,
} from "chart.js";
import { computed } from "vue";
import dayjs from "dayjs";

ChartJS.register(
    CategoryScale,
    LinearScale,
    LineController,
    LineElement,
    PointElement
);

interface Props {
    sales: {
        start_date: string;
        end_date: string;
        value: number;
    }[];
}
const props = defineProps<Props>();

const time_frame = new URLSearchParams(window.location.search).get(
    "time_frame"
);
const group_by = new URLSearchParams(window.location.search).get("group_by");

const getUrlWithTimeFrame = (timeFrame: string) => {
    const params = new URLSearchParams(window.location.search);
    params.set("time_frame", timeFrame);
    return window.location.pathname + "?" + params.toString();
};

const getUrlWithGroupBy = (groupBy: string) => {
    const params = new URLSearchParams(window.location.search);
    params.set("group_by", groupBy);
    return window.location.pathname + "?" + params.toString();
};

const timeFrameOptions = [
    {
        label: "Last 3 days",
        url: getUrlWithTimeFrame("last_3_days"),
        value: "last_3_days",
    },
    {
        label: "Last 7 days",
        url: getUrlWithTimeFrame("last_7_days"),
        value: "last_7_days",
    },
    {
        label: "Last 14 days",
        url: getUrlWithTimeFrame("last_14_days"),
        value: "last_14_days",
    },
];

const groupByOptions = [
    {
        label: "Hour",
        url: getUrlWithGroupBy("hour"),
        value: "hour",
    },
    {
        label: "Day",
        url: getUrlWithGroupBy("day"),
        value: "day",
    },
    {
        label: "Week",
        url: getUrlWithGroupBy("week"),
        value: "week",
    },
];

const selectedTimeFrameLabel = timeFrameOptions.filter(
    (option) => option.value === time_frame
)[0].label;

const selectedGroupByLabel = groupByOptions.filter(
    (option) => option.value === group_by
)[0].label;

const chartLabels = computed(() =>
    props.sales.map((sale) => {
        let format = group_by === "hour" ? "D MMM Ha" : "D MMM";

        return (
            dayjs(sale.start_date).format(format) +
            " - " +
            dayjs(sale.end_date).format(format)
        );
    })
);
const chartValues = computed(() => props.sales.map((sale) => sale.value));
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="v-card">
            <div class="flex flex-col md:flex-row gap-4 justify-between mb-6">
                <div>
                    <InputLabel value="Time frame" class="mb-1" />

                    <Dropdown width="full">
                        <template #trigger>
                            <div
                                class="flex justify-between items-center v-input cursor-pointer gap-4"
                            >
                                <span>{{ selectedTimeFrameLabel }}</span>
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
                                v-for="option of timeFrameOptions"
                                :href="option.url"
                                :key="option.label"
                            >
                                {{ option.label }}
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>

                <div>
                    <InputLabel value="Group by" class="md:text-right mb-1" />

                    <Dropdown width="full">
                        <template #trigger>
                            <div
                                class="flex justify-between items-center v-input cursor-pointer gap-4"
                            >
                                <span>{{ selectedGroupByLabel }}</span>
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
                                v-for="option of groupByOptions"
                                :href="option.url"
                                :key="option.label"
                            >
                                {{ option.label }}
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>

            <LineChart
                :data="{
                    labels: chartLabels,
                    datasets: [{ data: chartValues }],
                }"
                :options="{
                    responsive: true,
                }"
            ></LineChart>
        </div>
    </AuthenticatedLayout>
</template>
