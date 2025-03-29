<template>
    <Dialog :open="modal" @update:open="clouseModal">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Editando el tipo de cliente</DialogTitle>
                <DialogDescription>Los datos están validados, llenar con precaución.</DialogDescription>
            </DialogHeader>
            <form @submit="onSubmit" class="flex flex-col gap-4 py-4">

                <!-- Campo para editar el nombre del tipo de cliente -->
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Tipo de cliente</FormLabel>
                        <FormControl>
                            <Input id="name" type="text" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Campo para elegir el estado del tipo de cliente -->
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

import { ClientTypeResource, ClientTypeUpdateRequest} from '../interface/ClientType';

const props = defineProps<{ modal: boolean; clientTypeData: ClientTypeResource }>();
const emit = defineEmits<{
    (e: 'emit-close', open: boolean): void;
    (e: 'update-client-type', clientType: ClientTypeUpdateRequest, id_clientType: number): void;
}>();

const clouseModal = () => emit('emit-close', false);

//Schema de validación
const formSchema = toTypedSchema(
    z.object({
        name: z.string().min(3, 'El nombre es requerido').max(50, 'El nombre no puede tener más de 50 caracteres'),
        state: z.enum(['activo', 'inactivo']),
    })
);

//Inicialización del formulario
const { handleSubmit, setValues } = useForm ({
    validationSchema: formSchema,
    initialValues: {
        name: props.clientTypeData.name,
        state: props.clientTypeData.state ? 'activo' : 'inactivo',
    }
});

watch(
    () => props.clientTypeData,
    (newData) => {
        if (newData) {
            setValues({
                name: newData.name,
                state: newData.state ? 'activo' : 'inactivo',
            })
        }
    },
    { deep: true, immediate: true },
);

const onSubmit = handleSubmit((values) => {
    console.log('Formulario enviado con: ', values);
    emit('update-client-type', values, props.clientTypeData.id);
    clouseModal();
});
</script>
<style scoped lang="css"></style>