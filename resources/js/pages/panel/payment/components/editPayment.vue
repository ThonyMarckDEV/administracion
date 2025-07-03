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
                <FormField name="customer_id">
                    <FormItem>
                        <div class="flex items-center justify-between">
                            <div class="flex-grow space-y-2">
                                <FormLabel>Cliente</FormLabel>
                                <FormControl>
                                    <ComboBoxCustomer @select="(id) => setFieldValue('customer_id', id)" />
                                </FormControl>
                            </div>
                            <span
                                class="ml-4 self-end rounded-md bg-primary/10 px-2 py-2 text-sm font-medium text-primary dark:bg-primary dark:text-primary-foreground"
                            >
                                {{ paymentData.customer }}
                            </span>
                        </div>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField name="service_id">
                    <FormItem>
                        <div class="flex items-center justify-between">
                            <div class="flex-grow space-y-2">
                                <FormLabel>Servicio</FormLabel>
                                <FormControl>
                                    <ComboBoxService @select="(id) => setFieldValue('service_id', id || null)" />
                                </FormControl>
                            </div>
                            <span
                                class="ml-4 self-end rounded-md bg-primary/10 px-2 py-2 text-sm font-medium text-primary dark:bg-primary dark:text-primary-foreground"
                            >
                                {{ paymentData.service }}
                            </span>
                        </div>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ value }" name="amount">
                    <FormItem>
                        <FormLabel>Monto</FormLabel>
                        <NumberField
                            class="gap-2"
                            :min="0"
                            :format-options="{
                                style: 'currency',
                                currency: 'SOL',
                                currencyDisplay: 'code',
                                currencySign: 'accounting',
                            }"
                            :model-value="value"
                            @update:model-value="
                                (v) => {
                                    if (v) {
                                        setFieldValue('amount', v);
                                    } else {
                                        setFieldValue('amount', undefined);
                                    }
                                }
                            "
                        >
                            <NumberFieldContent>
                                <NumberFieldDecrement />
                                <FormControl>
                                    <NumberFieldInput />
                                </FormControl>
                                <NumberFieldIncrement />
                            </NumberFieldContent>
                        </NumberField>
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
                <div class="flex items-center justify-end gap-2">
                    <button
                        type="button"
                        class="inline-flex h-10 items-center justify-center rounded-md border border-transparent bg-secondary px-4 py-2 text-sm font-medium text-secondary-foreground shadow-sm hover:bg-secondary/80 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 disabled:opacity-50"
                        @click="closeModal"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="inline-flex h-10 items-center justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm hover:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
                    >
                        Guardar
                    </button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
<script setup lang="ts">
import ComboBoxCustomer from '@/components/Inputs/comboBoxCustomer.vue';
import ComboBoxService from '@/components/Inputs/comboBoxService.vue';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from '@/components/ui/number-field';
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
    (e: 'update-payment', payment: updatePayment, payment_id: number): void;
}>();
// closeModal
const closeModal = () => {
    emit('close-modal', false);
};

// validate form
const formShema = toTypedSchema(
    z.object({
        amount: z.number().min(1, 'minimo 1').max(10000, 'maximo 10000'),
        payment_date: z.string().refine(val => !isNaN(Date.parse(val)), 'Fecha inválida'),
        payment_method: z.string({ message: 'Campo obligatorio' }),
        reference: z.string({ message: 'Campo obligatorio' }),
        status: z.string({ message: 'Campo obligatorio' }),
        service_id: z.number().nullable().default(null),
        customer_id: z.number().nullable().default(null),
        payment_id: z.number().nullable().default(null),
    }),
);

const { handleSubmit, setValues, setFieldValue } = useForm({
    validationSchema: formShema,
    initialValues: {
        amount: Number(props.paymentData.amount),
        payment_date: props.paymentData.payment_date,
        payment_method: props.paymentData.payment_method,
        reference: props.paymentData.reference,
        status: props.paymentData.status,
        service_id: null, // Si no hay ID, usa `null`
        customer_id: null,
        payment_id: null,
    },
});

// watch for paymentData
watch(
    () => props.paymentData,
    (newData) => {
        console.log('newData', newData);
        setValues({
            amount: Number(newData.amount),
            payment_date: newData.payment_date
        ? newData.payment_date.split('-').reverse().join('-') // convierte "26-10-2025" a "2025-10-26"
        : '',
            payment_method: newData.payment_method,
            reference: newData.reference,
            status: newData.status,
        });
    },
    { immediate: true },
);

const opSubmit = handleSubmit((values) => {
    console.log('values', values);
    const paymentData: updatePayment = {
        customer_id: Number(values.customer_id) || null,
        service_id: Number(values.service_id) || null,
        // add payment_id
        payment_plan_id: null,
        // add discount_id
        discount_id: null,
        amount: Number(values.amount),
        payment_date: values.payment_date,
        payment_method: values.payment_method,
        reference: values.reference,
        status: values.status,
    };
    emit('update-payment', paymentData, props.paymentData.id);
    closeModal();
});
</script>
<style scoped></style>
