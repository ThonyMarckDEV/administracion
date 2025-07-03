<template>
    <Head title="Nuevo cliente"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Alert v-if="hasErrors" variant="destructive">
                <AlertCircle class="h-4 w-4"></AlertCircle>
                <AlertTitle>Error</AlertTitle>
                <AlertDescription>
                    <ul class="mt-2 list-inside list-disc text-sm">
                        <li v-for="(message, field) in page.props.errors" :key="field">
                            {{ message }}
                        </li>
                    </ul>
                </AlertDescription>
            </Alert>
            <Card class="mt-4 flex flex-col gap-4">
                <CardHeader>
                    <CardTitle>NUEVO CLIENTE</CardTitle>
                    <CardDescription>Complete los campos para crear un nuevo cliente</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="flex flex-col gap-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nombre</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Nombre y apellidos" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="codigo">
                            <FormItem>
                                <FormLabel>Código</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Código (máx. 11 caracteres)" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="email">
                            <FormItem>
                                <FormLabel>Email</FormLabel>
                                <FormControl>
                                    <Input type="email" placeholder="correo@ejemplo.com" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-if="props.customerTypes" v-slot="{ componentField }" name="client_type_id">
                            <FormItem>
                                <FormLabel>Tipo cliente</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField" @update:modelValue="updateDniRucFields">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecciona el tipo" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectLabel>Tipo cliente</SelectLabel>
                                                <SelectItem v-for="type in props.customerTypes" :key="type.id" :value="type.id">
                                                    {{ type.name }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-if="isPersona" v-slot="{ componentField }" name="dni">
                            <FormItem>
                                <FormLabel>DNI</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="8 dígitos" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-if="isEmpresa" v-slot="{ componentField }" name="ruc">
                            <FormItem>
                                <FormLabel>RUC</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="11 dígitos" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                                                <FormField v-slot="{ componentField }" name="state">
                            <FormItem>
                                <FormLabel>Estado</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField" disabled>
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Selecciona el estado" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectLabel>Estado</SelectLabel>
                                                <SelectItem value="activo"> Activo </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <Button type="submit">Crear Cliente</Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useCustomer } from '@/composables/useCustomer';
import { InputClientType } from '@/interface/Inputs';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { AlertCircle } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, ref } from 'vue';
import { z } from 'zod';
import { CustomerRequestUpdate } from '../interface/Customer';

const { createCustomer } = useCustomer();
const page = usePage<SharedData>();

const props = defineProps<{
    customerTypes: InputClientType[];
}>();

const hasErrors = computed(() => {
    return page.props.errors && Object.keys(page.props.errors).length > 0;
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'clientes', href: '/panel/customers' },
    { title: 'Exportar', href: '/panel/users/export' },
    { title: 'crear usuario', href: '/panel/customers/create' },
];

// Form validation
const clientTypeId = ref<number | null>(null);
const isPersona = computed(() => clientTypeId.value === 1); // Persona
const isEmpresa = computed(() => clientTypeId.value === 2); // Empresa

const formSchema = toTypedSchema(
    z.object({
        name: z
            .string({ message: 'Campo obligatorio' })
            .min(2, { message: 'Nombre mayor a 2 letras' })
            .max(150, { message: 'Nombre menor a 150 letras' }),
        codigo: z
            .string({ message: 'Campo obligatorio' })
            .min(2, { message: 'Código mayor a 2 caracteres' })
            .max(11, { message: 'Código menor a 11 caracteres' }),
        email: z
            .string({ message: 'Campo obligatorio' })
            .email({ message: 'Correo electrónico inválido' }),
        client_type_id: z.number({ message: 'Campo obligatorio' }),
        dni: z
            .string({ message: 'DNI obligatorio para personas' })
            .regex(/^\d{8}$/, { message: 'DNI debe tener 8 dígitos numéricos' })
            .nullable()
            .optional()
            .refine((val) => (isPersona.value ? !!val : true), {
                message: 'DNI es obligatorio para personas',
            }),
        ruc: z
            .string({ message: 'RUC obligatorio para empresas' })
            .regex(/^\d{11}$/, { message: 'RUC debe tener 11 dígitos numéricos' })
            .nullable()
            .optional()
            .refine((val) => (isEmpresa.value ? !!val : true), {
                message: 'RUC es obligatorio para empresas',
            }),
            state: z.enum(['activo', 'inactivo'], { message: 'estado invalido' }),
     }).refine(
        (data) => {
            if (isPersona.value && data.dni === null) return false;
            if (isEmpresa.value && data.ruc === null) return false;
            if (isPersona.value && data.ruc !== null) return false;
            if (isEmpresa.value && data.dni !== null) return false;
            return true;
        },
        {
            message: 'Solo DNI para personas y RUC para empresas',
            path: ['client_type_id'],
        }
    )
);

const { handleSubmit, setFieldValue } = useForm({
    validationSchema: formSchema,
    initialValues: {
        state: 'activo',
    },
});

const updateDniRucFields = (value: number) => {
    clientTypeId.value = value;
    setFieldValue('dni', null);
    setFieldValue('ruc', null);
};

const onSubmit = handleSubmit((values) => {
    const customerData: CustomerRequestUpdate= {
        name: values.name,
        codigo: values.codigo,
        email: values.email,
        client_type_id: values.client_type_id,
        state: values.state === 'activo',
        dni: values.dni ?? null,
        ruc: values.ruc ?? null,
    };

    createCustomer(customerData);
});
</script>

<style scoped></style>