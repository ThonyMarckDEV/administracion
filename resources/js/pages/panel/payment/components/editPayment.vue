<template>
    <Dialog :open="props.statusModal" @openChange="clouseModal">
        <DialogHeader>
            <DialogTitle>Editar Egreso</DialogTitle>
            <DialogDescription>
                <p class="text-sm text-muted-foreground">Edita los datos del egreso</p>
            </DialogDescription>
            <p>valor desde edit {{ statusModal }}</p>
        </DialogHeader>
    </Dialog>
</template>
<script setup lang="ts">
import { Dialog, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { PaymentUpdateRequest } from '../interface/Payment';

const props = defineProps<{
    statusModal: boolean;
    idPayment?: number;
}>();

const emit = defineEmits<{
    (e: 'close-modal', open: boolean): void;
    (e: 'update-payment', payment: PaymentUpdateRequest, payment_id: number): void;
}>();

const clouseModal = () => {
    console.log('cerrando modal');
    emit('close-modal', false);
};

// watch(
//     () => props.paymentData,
//     (newData) => {
//         if (newData) {
//             console.log('nuevos datos llegaron al componente: ', newData);
//         }
//     },
//     { immediate: true },
// );
</script>
<style scoped></style>
