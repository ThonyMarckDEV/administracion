<!-- AnnulInvoiceModal.vue -->
<template>
  <div
    v-if="statusModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    @click.self="closeModal"
  >
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-gray-800">
      <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
        Anular Comprobante
      </h2>
      <form @submit.prevent="submitAnnul">
        <div class="mb-4">
          <label
            for="motivo"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Motivo de Anulación
          </label>
          <select
            id="motivo"
            v-model="motivo"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
            required
          >
            <option value="" disabled>Seleccione un motivo</option>
            <option value="ERROR EN LA DESCRIPCIÓN DEL BIEN O SERVICIO">ERROR EN LA DESCRIPCIÓN DEL BIEN O SERVICIO</option>
            <option value="ERROR EN DATOS DEL CLIENTE">
              ERROR EN DATOS DEL CLIENTE
            </option>
            <option value="ERROR EN LA FECHA DE EMISIÓN">ERROR EN LA FECHA DE EMISIÓN</option>
            <option value="ERROR EN CALCULOS">ERROR EN CALCULOS</option>
            <option value="FACTURA EMITIDA POR DUPLICADO">FACTURA EMITIDA POR DUPLICADO</option>
            <option value="CANCELACIÓN DE LA OPERACIÓN">CANCELACIÓN DE LA OPERACIÓN</option>
            <option value="DEVOLUCIÓN TOTAL DEL PRODUCTO O SERVICIO">DEVOLUCIÓN TOTAL DEL PRODUCTO O SERVICIO</option>
            <option value="ERROR EN EL TIPO DE DOCUMENTO EMITIDO">ERROR EN EL TIPO DE DOCUMENTO EMITIDO</option>
            <option value="ANULACIÓN POR PRUEBA O ERROR HUMANO">ANULACIÓN POR PRUEBA O ERROR HUMANO</option>
            <option value="REFACTURACIÓN CON DATOS CORRECTOS">REFACTURACIÓN CON DATOS CORRECTOS</option>
            <option value="Otro">Otro</option>
          </select>
        </div>
        <div class="flex justify-end space-x-2">
          <Button
            variant="ghost"
            size="sm"
            class="text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            @click="closeModal"
          >
            Cancelar
          </Button>
          <Button
            type="submit"
            variant="destructive"
            size="sm"
            class="bg-red-600 text-white hover:bg-red-700"
            :disabled="!motivo || annulling"
          >
            {{ annulling ? 'Anulando...' : 'Anular' }}
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import { InvoiceServices } from '@/services/invoiceServices';
const props = defineProps<{
  statusModal: boolean;
  invoiceId: number;
}>();
const emit = defineEmits<{
  (e: 'close-modal'): void;
  (e: 'annul-success'): void;
}>();
const motivo = ref('');
const annulling = ref(false);
const closeModal = () => {
    console.log('Emitting close-modal');
  motivo.value = '';
  emit('close-modal');
};
const submitAnnul = async () => {
    if (annulling.value) return;
  try {
    annulling.value = true;
    await InvoiceServices.annulInvoice(props.invoiceId, { invoice_id: props.invoiceId, motivo: motivo.value });
    emit('annul-success');
    alert('Comprobante anulado exitosamente.');
    closeModal();
  } catch (error: any) {
    console.error('Error al anular el comprobante:', error);
    alert(error.response?.data?.message || 'No se pudo anular el comprobante.');
  } finally {
    annulling.value = false;
  }
};
</script>