<template>
    <Dialog :open="modal" @update:open="closeModal">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Editar Plan de Pago</DialogTitle>
                <DialogDescription>
                    <p class="text-sm text-muted-foreground">Editar los plan de pagos</p>
                </DialogDescription>
            </DialogHeader>

            <!-- Formulario -->
            <form @submit="onSubmit" class="flex flex-col gap-4 py-4">
                <!-- Seleccionar el Servicio -->
                <FormField v-slot="{ componentField }" name="service_id">
                    <FormItem>
                        <FormLabel>Servicio</FormLabel>
                        <FormControl>
                            <Select v-bind="componentField">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona el servicio"/>
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Servicio</SelectLabel>
                                        <SelectItem v-for="service in props.paymentPlanService" :key="service.id" :value="service.id">
                                            {{ service.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </FormControl>
                    </FormItem>
                </FormField>

                <!-- Seleccionar cliente -->
                <FormField v-slot="{ componentField }" name="customer_id">
                    <FormItem>
                        <FormLabel>Cliente</FormLabel>
                        <FormControl>
                            <Select v-bind="componentField">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona el Cliente"/>
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Cliente</SelectLabel>
                                        <SelectItem v-for="customer in props.paymentPlanCustomer" :key="customer.id" :value="customer.id">
                                            {{ customer.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </FormControl>
                    </FormItem>
                </FormField>

                <!-- Seleccionar el periodo -->
                <FormField v-slot="{ componentField }" name="period_id">
                    <FormItem>
                        <FormLabel>Periodo</FormLabel>
                        <FormControl>
                            <Select v-bind="componentField">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona el periodo"/>
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Periodo</SelectLabel>
                                        <SelectItem v-for="period in props.paymentPlanPeriod" :key="period.id" :value="period.id">
                                            {{ period.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </FormControl>
                    </FormItem>
                </FormField>

                <!-- Tipo de pago -->
                <FormField v-slot="{ componentField }" name="payment_type">
                    <FormItem>
                        <FormLabel>Tipo de pago</FormLabel>
                        <FormControl>
                            <Select v-bind="componentField">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona el tipo de pago" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="anual">Anual</SelectItem>
                                    <SelectItem value="mensual">Mensual</SelectItem>
                                </SelectContent>
                            </Select>
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Monto -->
                <FormField v-slot="{ componentField }" name="amount">
                    <FormItem>
                        <FormLabel>Monto</FormLabel>
                        <FormControl>
                            <Input v-bind="componentField" type="number" step="0.01" placeholder="Ingrese el monto" />
                        </FormControl>
                    </FormItem>
                </FormField>

                <!-- Duracción -->
                <FormField v-slot="{ componentField }" name="duration">
                <FormItem>
                    <FormLabel>Duración</FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" type="number" step="0.01" placeholder="Ingrese la duración" />
                    </FormControl>
                </FormItem>
                </FormField>

                <!-- Activo o inactivo -->
                <FormField v-slot="{ componentField }" name="state">
                    <FormItem>
                        <FormLabel>Estado</FormLabel>
                        <FormControl>
                            <Select v-bind="componentField">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona el estado" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Estado</SelectLabel>
                                        <SelectItem value="activo">Activo</SelectItem>
                                        <SelectItem value="inactivo">Inactivo</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!--guardar y cerrar    -->
                <DialogFooter>
                    <Button type="submit">Guardar cambios</Button>
                    <Button type="button" variant="outline" @click="closeModal">Cancelar</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select'
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { watch } from 'vue';
import { z } from 'zod';
import { InputService, InputPeriod, InputCustomer} from '@/interface/Inputs';
import { PaymentPlanRequestUpdate, PaymentPlanResource } from '../interface/PaymentPlan';

const props = defineProps<{
    modal: boolean;
    paymentPlanData: PaymentPlanResource;
    paymentPlanService: InputService[];
    paymentPlanPeriod: InputPeriod[];
    paymentPlanCustomer: InputCustomer[];
}>();

const emit = defineEmits<{
    (e: 'closeModal', open: boolean): void;
    (e: 'updatePaymentPlan', paymentPlanData: PaymentPlanRequestUpdate, paymentPlan_id: number): void;
}>();

const closeModal = () => {
    emit('closeModal', false);
};

const formShema = toTypedSchema(
    z.object({
        service_id: z
            .number({message: 'Campo obligatorio'}),
        customer_id: z
        .number({message: 'Campo obligatorio'}),
        period_id: z
            .number({message: 'Campo obligatorio'}),
        payment_type: z
            .enum(['anual', 'mensual'], { message: 'Campo obligatorio' }),
        amount: z
            .number({ message: 'Campo obligatorio' })
            .min(0, { message: 'El monto debe ser mayor o igual a 0' }),
        duration: z
            .number({ message: 'Campo obligatorio' })
            .int({ message: 'Debe ser un número entero' })
            .min(0, { message: 'Debe ser mayor o igual a 0' }),
        state: z
            .enum(['activo', 'inactivo'], {
            errorMap: () => ({ message: 'campo obligatorio' }),
        }),
    }),
);

const { handleSubmit, setValues } = useForm({
    validationSchema: formShema,
    initialValues: {
        service_id: props.paymentPlanData.service_id,
        customer_id: props.paymentPlanData.customer_id,
        period_id: props.paymentPlanData.period_id,
        payment_type: props.paymentPlanData ? 'anual' : 'mensual',
        amount: props.paymentPlanData.amount,
        duration: props.paymentPlanData.duration,
        state: props.paymentPlanData.state ? 'activo' : 'inactivo',
    },
});

watch(
    () => props.paymentPlanData,
    (newData) => {
        if (newData) {
            setValues({
                service_id: newData.service_id,
                customer_id: newData.customer_id,
                period_id: newData.period_id,
                payment_type: newData.payment_type ? 'anual' : 'mensual',
                amount: newData.amount,
                duration: newData.duration,
                state: newData.state ? 'activo' : 'inactivo',
            })
        }
    },
    { deep: true, immediate: true },
);

const onSubmit = handleSubmit((values) => {
        const updatedPaymentPlan: PaymentPlanRequestUpdate = {
        service_id: values.service_id,
        customer_id: values.customer_id,
        period_id: values.period_id,
        payment_type: values.payment_type,
        amount: values.amount,
        duration: values.duration,
        state: values.state === 'activo',
    };
    emit('updatePaymentPlan', updatedPaymentPlan, props.paymentPlanData.id);
        closeModal();
});

</script>

<style scoped lang="css">
</style>