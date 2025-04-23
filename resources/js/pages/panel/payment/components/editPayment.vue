<template>
    <Dialog :open="statusModal" @update:open="closeModal">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Editar pago</DialogTitle>
                <DialogDescription>
                    <p class="text-sm text-muted-foreground">Edita los datos del Pago</p>
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="opSubmit" class="flex flex-col gap-4 py-3">
                <FormField v-slot="{ componentField }" name="amount">
                    <FormItem>
                        <FormLabel>Monto</FormLabel>
                        <FormControl>
                            <Input id="amount" type="number" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="payment_date">
                    <FormItem>
                        <FormLabel>Fecha de pago</FormLabel>
                        <FormControl>
                            <Input id="payment_date" type="date" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="payment_method">
                    <FormItem>
                        <Select id="payment_method" v-bind="componentField">
                            <FormLabel>Metodo de pago</FormLabel>
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona un metodo" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="efectivo">Efectivo</SelectItem>
                                <SelectItem value="transferencia">Transferencia</SelectItem>
                            </SelectContent>
                        </Select>
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="reference">
                    <FormItem>
                        <FormLabel>Referencia</FormLabel>
                        <FormControl>
                            <Input id="reference" type="text" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="status">
                    <FormItem>
                        <Select id="status" v-bind="componentField">
                            <FormLabel>Estado</FormLabel>
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona un estado" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="pagado">Pagado</SelectItem>
                                <SelectItem value="pendiente">Pendiente</SelectItem>
                                <SelectItem value="vencido">Vencido</SelectItem>
                            </SelectContent>
                        </Select>
                    </FormItem>
                </FormField>
            </form>
        </DialogContent>
    </Dialog>
</template>
<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { watch } from 'vue';
import { z } from 'zod';
import { PaymentResource, updatePayment } from '../interface/Payment';

const props = defineProps<{
    statusModal: boolean;
    paymentData: PaymentResource;
}>();

const emit = defineEmits<{
    (e: 'close-modal', open: boolean): void;
    (e: 'update-payment', payment: updatePayment): void;
}>();
// closeModal
const closeModal = () => {
    emit('close-modal', false);
};

// validate form
const formShema = toTypedSchema(
    z.object({
        amount: z.number().min(1, 'Campo requerido'),
        payment_date: z.string({ message: 'Campo obligatorio' }),
        payment_method: z.string({ message: 'Campo obligatorio' }),
        reference: z.string({ message: 'Campo obligatorio' }),
        status: z.string({ message: 'Campo obligatorio' }),
    }),
);

const { handleSubmit, setValues } = useForm({
    validationSchema: formShema,
    initialValues: {
        amount: props.paymentData.amount,
        payment_date: props.paymentData.payment_date,
        payment_method: props.paymentData.payment_method,
        reference: props.paymentData.reference,
        status: props.paymentData.status,
    },
});

// watch for paymentData
watch(
    () => props.paymentData,
    (newData) => {
        console.log('newData', newData);
        setValues({
            amount: Number(newData.amount),
            payment_date: newData.payment_date,
            payment_method: newData.payment_method,
            reference: newData.reference,
            status: newData.status,
        });
    },
    { immediate: true },
);

const opSubmit = handleSubmit((values) => {
    console.log('values', values);
    closeModal();
});
</script>
<style scoped></style>
