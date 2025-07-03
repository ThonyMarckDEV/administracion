<template>
    <Head title="Nuevo plan de pago"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

            <!-- El alert no se para que sirve pero ahi esta, cualquier cosa funen a mi supervisor -->
            <Alert v-if="hasErrors" variant="destructive">
                <AlertCircle class="h-4 w-4">
                    <AlertTitle>Error</AlertTitle>
                    <AlertDescription>
                        <ul class="mt-2 list-inside list-disc text-sm">
                            <li v-for="(message, field) in page.props.errors" :key="field">
                                {{ message }}
                            </li>
                        </ul>
                    </AlertDescription>
                </AlertCircle>
            </Alert>

            <!-- Formulario -->
            <Card class="mt-4 flex flex-col gap-4">

                <!-- Emcabezado de la tarjeta -->
                <CardHeader>
                    <CardTitle>NUEVO PLAN DE PAGO</CardTitle>
                    <CardDescription>Complete los campos para crear un nuevo plan de pago</CardDescription>
                </CardHeader>

                <!-- Contenido de la tarjeta -->
                <CardContent>
                    
                    <form @submit="onSubmit" class="flex flex-col gap-6">
                        
                    <!-- Servicio -->
                        <FormField name="service_id">
                        <FormItem>
                            <FormLabel>Servicio</FormLabel>
                            <FormControl>
                            <Select v-model="selectedServiceId">
                                <SelectTrigger>
                                <SelectValue placeholder="Selecciona el servicio"/>
                                </SelectTrigger>
                                <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Servicio</SelectLabel>
                                    <SelectItem
                                    v-for="service in props.paymentPlanService"
                                    :key="service.id"
                                    :value="service.id"
                                    >
                                    {{ service.name }}
                                    </SelectItem>
                                </SelectGroup>
                                </SelectContent>
                            </Select>
                            </FormControl>
                        </FormItem>
                        </FormField>

                                                <!-- Seleccionar el Cliente -->
                        <FormField v-if="props.paymentPlanCustomer" v-slot="{ componentField }" name="customer_id">
                            <FormItem>
                                <FormLabel>Cliente</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecciona el cliente"/>
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
                        <FormField v-if="props.paymentPlanPeriod" v-slot="{ componentField }" name="period_id">
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

                            <FormField name="amount">
                                <FormItem>
                                    <FormLabel>Monto</FormLabel>
                                    <FormControl>
                                        <Input :value="amount" readonly class="bg-gray-100" />
                                    </FormControl>
                                </FormItem>
                            </FormField>

                        <!-- Duración -->

                            <FormField name="duration">
                                <FormItem>
                                    <FormLabel>Duración</FormLabel>
                                    <FormControl>
                                        <Input v-model="duration" type="number" step="1" placeholder="Ingrese la duración" />
                                    </FormControl>
                                </FormItem>
                            </FormField>

                         <!-- Boton de crear -->
                         <Button type="submit">Crear plan de pago</Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { AlertCircle } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, ref, watch } from 'vue'; 
import { custom, z } from 'zod';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { InputService, InputPeriod, InputCustomer } from '@/interface/Inputs';
import { usePaymentPlan } from '@/composables/usePaymentPlan';

const { createPaymentPlan } = usePaymentPlan();
const page = usePage<SharedData>();

const props = defineProps<{
    paymentPlanService: InputService[];
    paymentPlanPeriod: InputPeriod[];
    paymentPlanCustomer: InputCustomer[];
}>();

const hasErrors = computed(() => {
    return page.props.errors && Object.keys(page.props.errors).length > 0;
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Plan de pagos',
        href: '/panel/paymentPlans',
    },
    {
        title: 'Crear plan de pago',
        href: '/panel/paymentPlans/create',
    },
];

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
    }),
);

// 🔧 SE CAMBIÓ AQUÍ — nuevos refs para lógica reactiva
const selectedServiceId = ref<number>(0);
const duration = ref<number>(1);
const amount = ref<number>(0);

// 🔧 SE CAMBIÓ AQUÍ — obtener el objeto servicio seleccionado
const selectedService = computed(() =>
    props.paymentPlanService.find((service) => service.id === selectedServiceId.value) || null
);

// 🔧 SE CAMBIÓ AQUÍ — cálculo automático del monto con tipo consistente
watch([selectedService, duration], () => {
    const rawCost = selectedService.value?.cost ?? 0;
    const costo = typeof rawCost === 'string' ? parseFloat(rawCost) : rawCost;
    const durac = duration.value;

    if (costo > 0 && durac > 0) {
        amount.value = parseFloat((costo / durac).toFixed(2));
    } else {
        amount.value = 0;
    }
});


const { handleSubmit, setFieldValue } = useForm({
    validationSchema: formShema,
    initialValues: {
        service_id: 0,         
        customer_id: 0,        
        period_id: 0,          
        payment_type: 'anual', // Valor por defecto
        amount: 0,
        duration: 1,
    },
});

// Sincronizar con el formulario (sin null)
watch(amount, (val) => setFieldValue('amount', val));
watch(duration, (val) => setFieldValue('duration', val));
watch(selectedServiceId, (val) => {
    if (val > 0) setFieldValue('service_id', val);
});

const onSubmit = handleSubmit((values) => {
    createPaymentPlan(values);
});
</script>
<style scoped>
</style>