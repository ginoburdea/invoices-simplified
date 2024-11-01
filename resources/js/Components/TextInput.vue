<script setup lang="ts">
import { onMounted, ref, watch } from "vue";

const props = defineProps({
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

// Resize the textbox when the "input" variable gets assigned
watch(input, (input) => {
    if (props.multiline && input) {
        textboxOnInput({ target: input });
    }
});

defineExpose({ focus: () => input.value?.focus() });

const textboxOnInput = (event: Event) => {
    const currentHeight = parseInt(
        (event.target.style.height || "").replace("px", "")
    );

    // If the element doesn't have a height property inside the style property, add it
    // If it does and the textbox is overflowing, adjust the height
    if (isNaN(currentHeight) || currentHeight < event.target.scrollHeight) {
        event.target.style.height = event.target.scrollHeight + 5 + "px";
    }
};
</script>

<template>
    <textarea
        class="v-input"
        v-model="model"
        ref="input"
        v-if="multiline"
        @input="textboxOnInput"
    />
    <input class="v-input" v-model="model" ref="input" v-else />
</template>

<style></style>
