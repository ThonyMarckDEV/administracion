<template>
    <Head title="Nuevo Periodo"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="mt-4 flex flex-col gap-4">
                <CardHeader>
                    <CardTitle>NUEVO PERIODO</CardTitle>
                    <CardDescription>Complete los campos para crear un nuevo periodo</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="flex flex-col gap-6">

                        <!-- Campo para ingresar el nombre del periodo -->
                         <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Periodo</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="2024-I" v-bind="componentField"/>
                                </FormControl>
                                <FormMessage/>
                            </FormItem>
                         </FormField>

                         <!-- Campo para ingresar la descripción del periodo -->
                         <FormField v-slot="{ componentField }" name="description">
                            <FormItem>
                                <FormLabel>Periodo</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Primer semestre académico del año 2024" v-bind="componentField"/>
                                </FormControl>
                                <FormMessage/>
                            </FormItem>
                         </FormField>

                         <!-- Campo para elegir el estado del tipo de cliente -->
                         <FormField v-slot="{ componentField }" name="state">
                            <FormItem>
                                <FormLabel>Estado</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField" class="w-full rounded-md border border-slate-950 p-2">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Selecciona el estado"/>
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
                            </FormItem>
                        </FormField>

                        <!-- BOTONES PARA ENVIAR O BORRAR -->
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

import { usePeriod } from '@/composables/usePeriod';
const { createPeriod } = usePeriod();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Periodos',
        href: '/panel/periods',
    },
    
    {
        title: 'Crear periodo',
        href: '/panel/periods/create',
    },
];

//Form validation
const formSchema = toTypedSchema(
    z.object({
        name: z
            .string({message: 'Campo obligatorio'})
            .min(1, {message: 'El periodo debe tener al menos 1 caracter'})
            .max(50, {message: 'El periodo debe debe tener menos de 10 caracteres '}),
        description: z
            .string({ message: 'campo obligatorio' })
            .min(2, { message: 'La descripción debe tener al menos 2 caracteres' })
            .max(255, { message: 'La descripción debe ser menor a 255 caracteres' }),
        state: z
            .enum(['activo', 'inactivo'], {message: 'Estado invalido'}),
    }),
);

//form submit
const { handleSubmit } = useForm ({
    validationSchema: formSchema,
});

const onSubmit = handleSubmit((values) => {
    createPeriod(values);
});
</script>

<style scoped lang="css"></style>