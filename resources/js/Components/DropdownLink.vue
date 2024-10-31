<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

defineProps<{
    href?: string;
}>();
</script>

<template>
    <Link :href="href" class="dropdown-link" v-if="href">
        <slot />
    </Link>
    <a class="dropdown-link" v-else>
        <slot />
    </a>
</template>

<style scoped>
.dropdown-link {
    @apply block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none cursor-pointer;
}
</style>
