<template>
    <Head title="nuevo egreso"></Head>
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
                    <CardTitle>NUEVO EGRESO</CardTitle>
                    <CardDescription>Complete los campos para registrar un nuevo egreso</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="flex flex-col gap-6">
                        <FormField v-slot="{ componentField }" name="description">
                            <FormItem>
                                <FormLabel>Descripción</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Descripción del egreso" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="amount">
                            <FormItem>
                                <FormLabel>Monto</FormLabel>
                                <FormControl>
                                    <Input type="number" placeholder="Monto" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField name="category_id">
                            <FormItem>
                                <FormLabel>Categoría</FormLabel>
                                <FormControl>
                                    <ComboboxAmount @select="(id) => setFieldValue('category_id', id)" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField name="supplier_id">
                            <FormItem>
                                <FormLabel>Proveedor</FormLabel>
                                <FormControl>
                                    <ComboBoxSupplier @select="(id) => setFieldValue('supplier_id', id)" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="date_init">
                            <FormItem>
                                <FormLabel>Fecha</FormLabel>
                                <FormControl>
                                    <Input type="date" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <Button type="submit">Registrar Egreso</Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import ComboboxAmount from '@/components/Inputs/comboboxAmount.vue';
import ComboBoxSupplier from '@/components/Inputs/comboBoxSupplier.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useAmount } from '@/composables/useAmount';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { AlertCircle } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed } from 'vue';
import { z } from 'zod';
import { AmountRequestCreate } from '../interface/Amount';

const { createAmount } = useAmount();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Egresos',
        href: '/panel/amounts',
    },
    {
        title: 'Nuevo egreso',
        href: '/panel/amounts/create',
    },
];

const page = usePage();

const hasErrors = computed(() => {
    return page.props.errors && Object.keys(page.props.errors).length > 0;
});

const formSchema = toTypedSchema(
    z.object({
        description: z.string().min(1, { message: 'Campo obligatorio' }).max(255, { message: 'Máximo 255 caracteres' }),
        amount: z.number({ message: 'Campo obligatorio' }),
        category_id: z.number({ message: 'Campo obligatorio' }),
        supplier_id: z.number({ message: 'Campo obligatorio' }),
        date_init: z.string({ message: 'Campo obligatorio' }),
    }),
);

const { handleSubmit, setFieldValue } = useForm({
    validationSchema: formSchema,
});

const onSubmit = handleSubmit((values: AmountRequestCreate) => {
    console.log(values);
    // Send data to backend
    createAmount(values);
});
</script>
<style scoped></style>
