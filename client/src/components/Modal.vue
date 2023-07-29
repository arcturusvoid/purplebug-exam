<template>
    <div class="fixed w-full h-full top-0 left-0 flex items-center justify-center z-10">
        <div class="absolute w-full h-full bg-gray-900 opacity-50"></div>
        <div class="absolute max-h-full">
            <div class="container bg-white overflow-hidden md:rounded max-w-lg">
                <div
                    class="px-4 py-4 leading-none flex justify-between items-center font-medium text-sm bg-gray-100 border-b select-none">
                    <h3>{{ props.title }}</h3>
                </div>

                <div class="max-h-full px-4 py-4">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';
const props = defineProps(['title']);
</script>
