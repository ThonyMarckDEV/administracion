<template>
    <Dialog :open="modal" @update:open="clouseModal">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Editando el periodo</DialogTitle>
                <DialogDescription>Los datos estan validados, llenar con precaución</DialogDescription>
            </DialogHeader>
            <form @submit="onSubmit" class="flex flex-col gap-4 py-4">
                <!-- Campo para ingresar el nombre del periodo -->
                 <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Periodo</FormLabel>
                        <FormControl>
                            <Input id="name" typ="text" v-bind="componentField"/>
                        </FormControl>
                        <FormMessage/>
                    </FormItem>
                </FormField>

                <!-- Campo para ingresar la descripción del periodo -->
                 <FormField v-slot="{ componentField }" name="description">
                    <FormItem>
                        <FormLabel>Periodo</FormLabel>
                        <FormControl>
                            <Input id="description" type="text" v-bind="componentField"/>
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

                <DialogFooter>
                    <Button type="submit">Guardar cambios</Button>
                    <Button type="button" variant="outline" @click="clouseModal">Cancelar</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { watch } from 'vue';
import * as z from 'zod';

import { PeriodResource, PeriodUpdateRequest } from '../interface/Period';

const props = defineProps<{ modal: boolean; periodData: PeriodResource }>();
const emit = defineEmits<{
    (e: 'emit-close', open: boolean): void;
    (e: 'update-period', period: PeriodUpdateRequest, id_period: number): void;
}>();

const clouseModal = () => emit('emit-close', false);

//Schema de validación
const formSchema = toTypedSchema(
    z.object({
        name: z.string().min(3, 'El periodo es requerido').max(50, 'El periodo no puede tener más de 10 caracteres'),
        description: z.string().min(2, 'La descripción es requerida').max(255, 'La descripción no puede tener más de 255 caracteres'),
        state: z.enum(['activo', 'inactivo']),
    })
);

// Inicialización del formulario
const { handleSubmit, setValues } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: props.periodData.name,
        description: props.periodData.description,
        state: props.periodData.state ? 'activo' : 'inactivo',
    },
});
watch(
    () => props.periodData,
    (newData) => {
        if (newData) {
            setValues({
                name: newData.name,
                description: newData.description,
                state: newData.state ? 'activo' : 'inactivo',
            });
        }
    },
    { deep: true, immediate: true },
);

const onSubmit = handleSubmit((values) => {
    console.log('Formulario enviado con: ', values);
    emit('update-period', values, props.periodData.id);
    clouseModal();
});

</script>
<style scoped lang="css">
</style>