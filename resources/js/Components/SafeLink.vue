<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

defineProps<{
    href?: string;
}>();
</script>

<template>
    <Link :href="href" class="v-link" v-if="href">
        <slot />
    </Link>
    <a class="v-link" :class="{ 'v-link--disabled': !href }" v-else>
        <slot />
    </a>
</template>

<style></style>
