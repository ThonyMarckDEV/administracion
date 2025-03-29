<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthSplitLayout.vue';

defineProps<{
    title?: string;
    description?: string;
    image?: string;
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description" :image="image">
        <slot />
    </AuthLayout>
</template>
