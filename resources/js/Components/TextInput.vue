<script setup lang="ts">
import { onMounted, ref } from "vue";

defineProps({
    multiline: {
        type: Boolean,
        required: false,
        default: false,
    },
});

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute("autofocus")) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <textarea class="v-input" v-model="model" ref="input" v-if="multiline" />
    <input class="v-input" v-model="model" ref="input" v-else />
</template>

<style></style>
