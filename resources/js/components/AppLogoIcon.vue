<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
  <div class="flex aspect-square size-12 items-center justify-center rounded-md bg-blue-300 text-sidebar-primary-foreground">
    <img
      src="/images/logo.png"
      alt="Logo Administración"
      class="h-8 w-8 object-contain"
    />
  </div>
  <div class="ml-2 grid flex-1 text-left text-lg">
    <span class="mb-0.5 truncate font-bold leading-none">Administración</span>
  </div>
</template>
