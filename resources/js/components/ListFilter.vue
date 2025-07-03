<template>
  <div class="ml-auto max-w-xs p-2">
    <select
      v-model="statusFilter"
      @change="emitStatus"
      class="w-full border rounded-md px-2 py-1 text-sm dark:bg-background dark:border-muted"
    >
      <option value="">Estado</option>
      <option value="pendiente">Pendiente</option>
      <option value="pagado">Pagado</option>
      <option value="vencido">Vencido</option>
    </select>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const emit = defineEmits<{
  (e: 'search', status: string): void;
}>();

const statusFilter = ref('');

const emitStatus = () => {
  emit('search', statusFilter.value);
};
</script>

<style scoped></style>
