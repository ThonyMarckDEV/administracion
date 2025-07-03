<!-- components/FilterInvoices.vue -->
<template>
  <div class="flex flex-col gap-4 md:flex-row md:items-end md:gap-6 mb-6">
    <div class="flex-1">
    <!-- Document Type Filter -->
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
    <!-- Payment ID Filter -->
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
    <!-- Correlative Filter -->
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
    <!-- Service Combobox -->
    <div class="flex-1">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Servicio
      </label>
      <ComboBoxService @select="onSelectService" />
    </div>
    <!-- Customer Combobox -->
    <div class="flex-1">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Cliente
      </label>
      <ComboBoxCustomer @select="onSelectCustomer" />
    </div>
    <!-- Payment Method Filter -->
    <div class="flex-1">
      <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Método de Pago
      </label>
      <select
        id="payment_method"
        v-model="filters.payment_method"
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
      >
        <option value="">Todos</option>
        <option value="transferencia">Transferencia</option>
        <option value="efectivo">Efectivo</option>
      </select>
    </div>
    <!-- Clear Filters Button -->
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
import ComboBoxService from '@/components/Inputs/comboBoxService.vue';
import ComboBoxCustomer from '@/components/Inputs/comboBoxCustomer.vue';

const filters = ref({
  document_type: '',
  payment_id: '',
  correlative_assigned: '',
    service_id: 0,
  customer_id: 0,
  payment_method: '',
});
const emit = defineEmits<{
  (e: 'filter', filters: {

    document_type: string;
    payment_id: string;
    correlative_assigned: string;
    service_id: number;
    customer_id: number;
    payment_method: string;
  }): void;
}>();

const onSelectService = (serviceId: number) => {
  filters.value.service_id = serviceId;
  emit('filter', { ...filters.value });
};
const onSelectCustomer = (customerId: number) => {
  filters.value.customer_id = customerId;
  emit('filter', { ...filters.value });
};

watch(filters, () => {
  emit('filter', { ...filters.value });
}, { deep: true });

const clearFilters = () => {
  filters.value = {
    document_type: '',
    payment_id: '',
    correlative_assigned: '',
    service_id: 0,
    customer_id: 0,
    payment_method: '',
  };
};
</script>