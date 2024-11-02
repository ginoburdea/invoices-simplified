<script setup lang="ts"></script>

<template>
    <div class="flex gap-2 items-start mb-2">
        <div class="w-full grid grid-cols-10 gap-2">
            <div class="col-span-10 md:col-span-6">
                <slot name="name-column"></slot>
            </div>
            <div class="col-span-5 md:col-span-2">
                <slot name="price-column"></slot>
            </div>
            <div class="col-span-5 md:col-span-2">
                <slot name="quantity-column"></slot>
            </div>
        </div>

        <div>
            <slot name="button-column"></slot>
        </div>
    </div>
</template>

<style></style>
