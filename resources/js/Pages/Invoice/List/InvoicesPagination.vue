<script setup lang="ts">
import SafeLink from "@/Components/SafeLink.vue";

defineProps({
    prevPageUrl: {
        type: String,
        required: false,
    },
    nextPageUrl: {
        type: String,
        required: false,
    },
});

const page = new URLSearchParams(window.location.search).get("page");
</script>

<template>
    <p class="text-center text-sm">
        <SafeLink :href="prevPageUrl" class="v-link"> Prev </SafeLink>
        <span class="mx-5">Page {{ page }}</span>
        <SafeLink :href="nextPageUrl" class="v-link"> Next </SafeLink>
    </p>
</template>

<style></style>
