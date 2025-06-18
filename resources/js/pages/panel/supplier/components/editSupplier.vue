<template>
    <Dialog :open="modal" @update:open="clouseModal">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Editando el proveedor</DialogTitle>
                <DialogDescription>Los datos están validados, llenar con precaución.</DialogDescription>
            </DialogHeader>
            <form @submit="onSubmit" class="flex flex-col gap-4 py-4">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Razón social</FormLabel>
                        <FormControl>
                            <Input id="name" type="text" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="ruc">
                    <FormItem>
                        <FormLabel>RUC</FormLabel>
                        <FormControl>
                            <Input id="ruc" type="text" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input id="email" type="email" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="address">
                    <FormItem>
                        <FormLabel>Dirección</FormLabel>
                        <FormControl>
                            <Input id="address" type="text" v-bind="componentField" />
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
                <DialogFooter>
                    <Button type="submit">Guardar cambios</Button>
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
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { watch } from 'vue';
import { z } from 'zod';
import { SupplierResource, SupplierUpdateRequest } from '../interface/Supplier';

const props = defineProps<{ modal: boolean; supplierData: SupplierResource }>();
const emit = defineEmits<{
    (e: 'emit-close', open: boolean): void;
    (e: 'update-supplier', supplier: SupplierUpdateRequest, id_supplier: number): void;
}>();

const clouseModal = () => emit('emit-close', false);

// Schema de validación
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

// Inicialización del formulario
const { handleSubmit, setValues } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: props.supplierData.name,
        ruc: props.supplierData.ruc,
        email: props.supplierData.email,
        address: props.supplierData.address,
        state: props.supplierData.state ? 'activo' : 'inactivo',
    },
});

watch(
    () => props.supplierData,
    (newData) => {
        if (newData) {
            setValues({
                name: newData.name,
                ruc: newData.ruc,
                email: newData.email,
                address: newData.address,
                state: newData.state ? 'activo' : 'inactivo',
            });
        }
    },
    { deep: true, immediate: true }
);

const onSubmit = handleSubmit((values) => {
    emit('update-supplier', values, props.supplierData.id);
    clouseModal();
});
</script>

<style scoped></style>