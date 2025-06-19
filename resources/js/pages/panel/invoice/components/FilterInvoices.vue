<!-- components/FilterInvoices.vue -->
<template>
  <div class="flex flex-col gap-4 md:flex-row md:items-end md:gap-6 mb-6">
    <div class="flex-1">
      <label for="document_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Tipo de Comprobante
      </label>
      <select
        id="document_type"
        v-model="filters.document_type"
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
      >
        <option value="">Todos</option>
        <option value="boleta">Boleta</option>
        <option value="factura">Factura</option>
      </select>
    </div>
    <div class="flex-1">
      <label for="payment_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        ID Pago
      </label>
      <input
        id="payment_id"
        v-model="filters.payment_id"
        type="text"
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
        placeholder="Ingrese ID de pago"
      />
    </div>
    <div class="flex-1">
      <label for="correlative" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Correlativo
      </label>
      <input
        id="correlative"
        v-model="filters.correlative_assigned"
        type="text"
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
        placeholder="Ingrese correlativo"
      />
    </div>
    <Button
      variant="outline"
      size="sm"
      class="h-10 px-4"
      @click="clearFilters"
    >
      Limpiar Filtros
    </Button>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import Button from '@/components/ui/button/Button.vue';

const filters = ref({
  document_type: '',
  payment_id: '',
  correlative_assigned: '',
});

const emit = defineEmits<{
  (e: 'filter', filters: { document_type: string; payment_id: string; correlative_assigned: string }): void;
}>();

watch(filters, () => {
  emit('filter', { ...filters.value });
}, { deep: true });

const clearFilters = () => {
  filters.value = {
    document_type: '',
    payment_id: '',
    correlative_assigned: '',
  };
};
</script>