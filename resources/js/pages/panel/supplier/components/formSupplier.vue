<template>
    <Head title="Nuevo Proveedor"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="mt-4 flex flex-col gap-4">
                <CardHeader>
                    <CardTitle>NUEVO PROVEEDOR</CardTitle>
                    <CardDescription>Complete los campos para crear un nuevo proveedor</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="flex flex-col gap-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Razón Social</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Nombre" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="ruc">
                            <FormItem>
                                <FormLabel>RUC</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="10000000000" v-bind="componentField" />
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
                        <FormField v-slot="{ componentField }" name="address">
                            <FormItem>
                                <FormLabel>Dirección</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Calle Los Cocos 145, Urb. Santa María del Pinar" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
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
                        <div class="container flex justify-end gap-4">
                            <Button type="submit" variant="default">Enviar</Button>
                            <Button type="reset" variant="outline">Borrar</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { z } from 'zod';
import { useSupplier } from '@/composables/useSupplier';

const { createSupplier } = useSupplier();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Proveedores', href: '/panel/suppliers' },
    { title: 'Exportar', href: '/panel/suppliers/export' },
    { title: 'Crear proveedor', href: '/panel/suppliers/create' },
];

// Form validation
const formSchema = toTypedSchema(
    z.object({
        name: z
            .string({ message: 'Campo obligatorio' })
            .min(2, { message: 'Nombre mayor a 2 letras' })
            .max(50, { message: 'Nombre menor a 50 letras' }),
        ruc: z
            .string({ message: 'Campo obligatorio' })
            .length(11, { message: 'El RUC debe tener 11 dígitos' })
            .regex(/^\d+$/, { message: 'El RUC solo debe contener números' }),
        email: z
            .string({ message: 'Campo obligatorio' })
            .email({ message: 'Correo electrónico inválido' }),
        address: z
            .string()
            .max(255, { message: 'Dirección menor a 255 caracteres' })
            .nullable()
            .optional(),
        state: z.enum(['activo', 'inactivo'], { message: 'Estado inválido' }),
    })
);

const { handleSubmit } = useForm({
    validationSchema: formSchema,
});

const onSubmit = handleSubmit((values) => {
    createSupplier(values);
});
</script>

<style scoped></style>