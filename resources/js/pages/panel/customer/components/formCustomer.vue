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
                                    <Input type="text" placeholder="nombre y apellidos" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="codigo">
                            <FormItem>
                                <FormLabel>Código</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="codigo" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="client_type_id">
                            <FormItem>
                                <FormLabel>Tipo de Cliente</FormLabel>
                                <FormControl>
                                    <Input type="number" placeholder="tipo de cliente" v-bind="componentField" />
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
//composable
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useCustomer } from '@/composables/useCustomer';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { AlertCircle } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed } from 'vue';
import { z } from 'zod';
const { createCustomer } = useCustomer();
const page = usePage<SharedData>();
const hasErrors = computed(() => {
    return page.props.errors && Object.keys(page.props.errors).length > 0;
});
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'clientes',
        href: '/panel/customers',
    },
    {
        title: 'Exportar',
        href: '/panel/users/export',
    },
    {
        title: 'crear usuario',
        href: '/panel/customers/create',
    },
];

// Form validation

const formShema = toTypedSchema(
    z.object({
        name: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'nombre mayor a 2 letras' })
            .max(150, { message: 'nombre menor a 150 letras' }),
        codigo: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'codigo mayor a 2 letras' })
            .max(50, { message: 'codigo menor a 50 letras' }),
        client_type_id: z.number({ message: 'campo obligatorio' }),
    }),
);

const { handleSubmit } = useForm({
    validationSchema: formShema,
});

const onSubmit = handleSubmit((values) => {
    console.log(values);
    createCustomer(values);
});
</script>
<style scoped></style>
