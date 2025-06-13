<template>
    <Dialog :open="statusModal" @close="closeModal">
        <DialogContent class="max-w-lg">
            <DialogTitle>Detalles del Comprobante</DialogTitle>
            <DialogDescription>
                Información detallada del comprobante seleccionado.
            </DialogDescription>
            <div class="mt-4 space-y-4">
                <div>
                    <strong>ID:</strong> {{ invoiceData.id }}
                </div>
                <div>
                    <strong>Tipo:</strong> {{ invoiceData.document_type }}
                </div>
                <div>
                    <strong>Serie:</strong> {{ invoiceData.serie_assigned }}
                </div>
                <div>
                    <strong>Correlativo:</strong> {{ invoiceData.correlative_assigned }}
                </div>
                <div>
                    <strong>Estado SUNAT:</strong> {{ invoiceData.sunat }}
                </div>
                <div>
                    <strong>Cliente:</strong> {{ invoiceData.payment.customer?.name ?? 'N/A' }}
                </div>
                <div>
                    <strong>Servicio:</strong> {{ invoiceData.payment.service?.name ?? 'N/A' }}
                </div>
                <div>
                    <strong>Monto:</strong> {{ invoiceData.payment.amount }}
                </div>
                <div>
                    <strong>Fecha de Pago:</strong> {{ invoiceData.payment.payment_date ?? 'N/A' }}
                </div>
                <div>
                    <strong>Método de Pago:</strong> {{ invoiceData.payment.payment_method }}
                </div>
                <div>
                    <strong>Referencia:</strong> {{ invoiceData.payment.reference ?? 'N/A' }}
                </div>
                <div>
                    <strong>Estado del Pago:</strong> {{ invoiceData.payment.status }}
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <Button variant="outline" @click="closeModal">Cerrar</Button>
            </div>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import Button from '@/components/ui/button/Button.vue';
import { InvoiceResource } from '../interface/Invoice';

defineProps<{
    invoiceData: InvoiceResource;
    statusModal: boolean;
}>();

defineEmits<{
    (e: 'close-modal'): void;
}>();
</script>