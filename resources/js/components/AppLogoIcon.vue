<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        :class="className"
        v-bind="$attrs"
    >
        <g fill="currentColor">
            <rect x="3" y="4" width="2" height="16" rx="0.5" />
            <rect x="6.5" y="4" width="1" height="16" rx="0.5" />
            <rect x="9" y="4" width="3" height="16" rx="0.5" />
            <rect x="13.5" y="4" width="1" height="16" rx="0.5" />
            <rect x="16" y="4" width="2" height="16" rx="0.5" />
            <rect x="19.5" y="4" width="1.5" height="16" rx="0.5" />
        </g>
    </svg>
</template>
