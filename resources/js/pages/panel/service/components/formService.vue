
<template>
    <Head title="Nuevo Servicio"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="mt-4 flex flex-col gap-4">
                <CardHeader>
                    <CardTitle>NUEVO SERVICIO</CardTitle>
                    <CardDescription>Complete los campos para crear un nuevo servicio</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="flex flex-col gap-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nombre del Servicio</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Nombre del servicio" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        
                        <FormField v-slot="{ componentField }" name="cost">
                            <FormItem>
                                <FormLabel>Costo</FormLabel>
                                <FormControl>
                                    <Input type="number" placeholder="0.00" step="0.01" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        
                        <FormField v-slot="{ componentField }" name="ini_date">
                            <FormItem>
                                <FormLabel>Fecha de Inicio</FormLabel>
                                <FormControl>
                                    <Input type="date" v-bind="componentField" />
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
                            <Button type="submit" variant="default">Crear Servicio</Button>
                            <Button type="reset" variant="outline">Borrar</Button>
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
import { router } from '@inertiajs/vue3';
import { showSuccessMessage, showErrorMessage } from '@/utils/message';
import { toast } from 'vue-sonner'; // or your preferred toast library
// Composable
import { useService } from '@/composables/useService';
const { createService } = useService();


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Servicios',
        href: '/panel/services',
    },
    {
        title: 'Crear Servicio',
        href: '/panel/services/create',
    },
];

// Form validation
const formSchema = toTypedSchema(
    z.object({
        name: z
            .string({ message: 'Campo obligatorio' })
            .min(2, { message: 'Nombre debe tener al menos 2 caracteres' })
            .max(100, { message: 'Nombre debe tener menos de 100 caracteres' }),
        cost: z
            .number({ message: 'Costo es obligatorio' })
            .min(0, { message: 'Costo no puede ser negativo' }),
        ini_date: z
            .string({ message: 'Fecha de inicio es obligatoria' })
            .refine((dateString) => {
                // Parse the date
                const date = new Date(dateString);
                
                // Check if the date is valid
                if (isNaN(date.getTime())) {
                    return false;
                }
                
                // Correct invalid dates (like 31/11/2025)
                const originalYear = date.getFullYear();
                const originalMonth = date.getMonth();
                const originalDay = date.getDate();
                
                // If the date is adjusted, it means the original date was invalid
                return originalYear === date.getFullYear() && 
                       originalMonth === date.getMonth() && 
                       originalDay === date.getDate();
            }, { message: 'Fecha de inicio no válida' }),
        state: z.enum(['activo', 'inactivo'], { message: 'Estado inválido' }),
    }),
);

// Form submit
const { handleSubmit } = useForm({
    validationSchema: formSchema,
});

const onSubmit = handleSubmit((values) => {
    // Ensure cost is a number
    const serviceData = {
        ...values,
        cost: Number(values.cost)
    };
    
    createService(serviceData);
});


</script>

<style scoped></style>
