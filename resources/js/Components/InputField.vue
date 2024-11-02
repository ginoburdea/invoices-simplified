<script setup lang="ts">
import { onMounted, ref, watch } from "vue";

interface Props {
    type: "text" | "number" | "textarea" | "email" | "hidden" | "password";
}

const props = defineProps<Props>();

const model = defineModel<string | number>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute("autofocus")) {
        input.value?.focus();
    }
});

// Resize the textbox when the "input" variable gets assigned
watch(input, (input) => {
    if (props.type === "textarea" && input) {
        textboxOnInput(input);
    }
});

defineExpose({ focus: () => input.value?.focus() });

const textboxOnInput = (el: HTMLInputElement | null) => {
    if (!el) return;

    const currentHeight = parseInt((el.style.height || "").replace("px", ""));

    // If the element doesn't have a height property inside the style property, add it
    // If it does and the textbox is overflowing, adjust the height
    if (isNaN(currentHeight) || currentHeight < el.scrollHeight) {
        el.style.height = el.scrollHeight + 5 + "px";
    }
};
</script>

<template>
    <textarea
        class="v-input"
        v-model="model"
        ref="input"
        v-if="type === 'textarea'"
        @input="textboxOnInput(input)"
    />
    <input class="v-input" v-model="model" ref="input" v-else />
</template>

<style></style>
