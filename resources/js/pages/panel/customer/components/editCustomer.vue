<template>
    <Dialog :open="modal" @update:open="clouseModal">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Editar Cliente</DialogTitle>
                <DialogDescription>
                    <p class="text-sm text-muted-foreground">Edita los datos del cliente</p>
                </DialogDescription>
            </DialogHeader>
            <form @submit="onSubmit" class="flex flex-col gap-4 py-4">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nombre</FormLabel>
                        <FormControl>
                            <Input id="name" type="text" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="codigo">
                    <FormItem>
                        <FormLabel>Código</FormLabel>
                        <FormControl>
                            <Input id="codigo" type="text" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
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
                <FormField v-slot="{ componentField }" name="client_type_id">
                    <FormItem>
                        <FormLabel>Tipo cliente</FormLabel>
                        <FormControl>
                            <Select v-bind="componentField">
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
                <DialogFooter class="flex justify-end gap-2">
                    <Button type="submit">Guardar Cambios</Button>
                    <Button type="button" variant="outline" @click="clouseModal">Cancelar</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { InputClientType } from '@/interface/Inputs';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { watch } from 'vue';
import { z } from 'zod';
import { CustomerRequestUpdate, CustomerResource } from '../interface/Customer';

const props = defineProps<{
    modal: boolean;
    customerData: CustomerResource;
    customerTypes: InputClientType[];
}>();

const emit = defineEmits<{
    (e: 'closeModal', open: boolean): void;
    (e: 'updateCustomer', customerData: CustomerRequestUpdate, customer_id: number): void;
}>();

const clouseModal = () => {
    emit('closeModal', false);
};

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
        state: z.enum(['activo', 'inactivo'], {
            errorMap: () => ({ message: 'campo obligatorio' }),
        }),
    }),
);

const { handleSubmit, setValues } = useForm({
    validationSchema: formShema,
    initialValues: {
        name: props.customerData.name,
        codigo: props.customerData.codigo,
        client_type_id: props.customerData.client_type_id,
        state: props.customerData.state ? 'activo' : 'inactivo',
    },
});

watch(
    () => props.customerData,
    (newData) => {
        if (newData) {
            setValues({
                name: newData.name,
                codigo: newData.codigo,
                client_type_id: newData.client_type_id,
                state: newData.state ? 'activo' : 'inactivo',
            });
        }
    },
    { deep: true, immediate: true },
);

const onSubmit = handleSubmit((values) => {
    // console.log(values);
    emit('updateCustomer', values, props.customerData.id);
});
</script>
<style scoped></style>
