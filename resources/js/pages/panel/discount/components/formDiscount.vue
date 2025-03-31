<template>
    <Head title="Nuevo Descuento"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="mt-4 flex flex-col gap-4">
                <CardHeader>
                    <CardTitle>NUEVO DESCUENTO</CardTitle>
                    <CardDescription>Complete los campos para crear un nuevo descuento</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="flex flex-col gap-6">

                        <!-- Campo para ingresar la descripcion del descuento -->
                        <FormField v-slot="{ componentField }" name="description">
                            <FormItem>
                                <FormLabel>Descripción</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Descripcion" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Campo para ingresar el valor del porcentaje del descuento -->
                        <FormField v-slot="{ componentField }" name="percentage">
                            <FormItem>
                                <FormLabel>Porcentaje</FormLabel>
                                <FormControl>
                                    <Input type="number" placeholder="porcentaje" step="0.01" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Campo para ingresar el estado del descuento -->
                        <FormField v-slot="{ componentField }" name="state">
                            <FormItem>
                                <FormLabel>Estado</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField" class="w-full rounded-md border border-slate-950 p-2">
                                        <SelectTrigger class="w-full">
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
                        
                        <!--BOTONES PARA ENVIAR Y BORRAR-->
                        <div class="container flex justify-end gap-4">
                            <Button type="submit" variant="default"> Enviar </Button>
                            <Button type="reset" variant="outline"> Borrar </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">


import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectGroup, SelectItem, SelectLabel } from '@/components/ui/select';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import * as z from 'zod';

// composable
import { useDiscount } from '@/composables/useDiscount';
const { createDiscount } = useDiscount();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Descuentos',
        href: '/panel/discounts',
    },
    {
        title: 'Exportar',
        href: '/panel/discounts/export',
    },
    {
        title: 'Crear descuentos',
        href: '/panel/discounts/create',
    },
];

// Form validation
const formSchema = toTypedSchema(
    z.object({
        description: z
            .string({ message: 'campo obligatorio' })
            .min(2, { message: 'La descripción debe tener al menos 2 caracteres' })
            .max(255, { message: 'La descripción debe ser menor a 255 caracteres' }),
        percentage: z
            .number({ message: 'Campo obligatorio' })
            .min(0, { message: 'El porcentaje debe ser al menos 0' })
            .max(100, { message: 'El porcentaje no puede superar 100' }),
        state: z.enum(['activo', 'inactivo'], { message: 'estado inválido' }),
    }),
);

// Form submit
const { handleSubmit } = useForm({
    validationSchema: formSchema,
});
const onSubmit = handleSubmit((values) => {
    const discountData = {
        description: values.description,
        percentage: Number(values.percentage),
        state: values.state === 'activo' // ✅ convierte string a boolean
    };

    createDiscount(discountData);
});
</script>
<style scoped></style>