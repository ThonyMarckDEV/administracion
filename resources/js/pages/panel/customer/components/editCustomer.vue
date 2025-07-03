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
                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input id="email" type="email" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
<FormField name="state" v-slot="{ value, handleChange }">
  <FormItem>
    <FormLabel>Estado</FormLabel>
    <FormControl>
      <Select :modelValue="value" @update:modelValue="(val) => { console.log('Nuevo estado seleccionado:', val); handleChange(val); }">
        <SelectTrigger>
          <SelectValue :placeholder="value ?? 'Selecciona el estado'" />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
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
                <FormField v-if="isPersona" v-slot="{ componentField }" name="dni">
                    <FormItem>
                        <FormLabel>DNI</FormLabel>
                        <FormControl>
                            <Input id="dni" type="text" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-if="isEmpresa" v-slot="{ componentField }" name="ruc">
                    <FormItem>
                        <FormLabel>RUC</FormLabel>
                        <FormControl>
                            <Input id="ruc" type="text" v-bind="componentField" />
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
import { computed, ref, watch } from 'vue';
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

const clientTypeId = ref<number>(props.customerData.client_type_id);
const isPersona = computed(() => clientTypeId.value === 1); // 1 persona
const isEmpresa = computed(() => clientTypeId.value === 2); // 2 empresa

const formSchema = toTypedSchema(
    z.object({
        name: z
            .string({ message: 'Campo obligatorio' })
            .min(2, { message: 'Nombre mayor a 2 letras' })
            .max(150, { message: 'Nombre menor a 150 letras' }),
        codigo: z
            .string({ message: 'Campo obligatorio' })
            .min(2, { message: 'Código mayor a 2 caracteres' })
            .max(11, { message: 'Código menor a 11 caracteres' }),
        email: z
            .string({ message: 'Campo obligatorio' })
            .email({ message: 'Correo electrónico inválido' }),
        client_type_id: z.number({ message: 'Campo obligatorio' }),
        state: z.enum(['activo', 'inactivo'], {
            errorMap: () => ({ message: 'Campo obligatorio' }),
        }),
        dni: z
            .string({ message: 'DNI obligatorio para personas' })
            .regex(/^\d{8}$/, { message: 'DNI debe tener 8 dígitos numéricos' })
            .nullable()
            .optional()
            .refine((val) => (isPersona.value ? !!val : true), {
                message: 'DNI es obligatorio para personas',
            }),
        ruc: z
            .string({ message: 'RUC obligatorio para empresas' })
            .regex(/^\d{11}$/, { message: 'RUC debe tener 11 dígitos numéricos' })
            .nullable()
            .optional()
            .refine((val) => (isEmpresa.value ? !!val : true), {
                message: 'RUC es obligatorio para empresas',
            }),
    }).refine(
        (data) => {
            if (isPersona.value && data.dni === null) return false;
            if (isEmpresa.value && data.ruc === null) return false;
            if (isPersona.value && data.ruc !== null) return false;
            if (isEmpresa.value && data.dni !== null) return false;
            return true;
        },
        {
            message: 'Solo DNI para personas y RUC para empresas',
            path: ['client_type_id'],
        }
    )
);

const { handleSubmit, setValues, setFieldValue } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: props.customerData.name,
        codigo: props.customerData.codigo,
        email: props.customerData.email,
        client_type_id: props.customerData.client_type_id,
        state: props.customerData.state ? 'activo' : 'inactivo' ,
        dni: props.customerData.dni,
        ruc: props.customerData.ruc,
    },
});

watch(
    () => props.customerData,
    (newData) => {
        if (newData) {
            setValues({
                name: newData.name,
                codigo: newData.codigo,
                email: newData.email,
                client_type_id: newData.client_type_id,
                state: newData.state ? 'activo' : 'inactivo',
                dni: newData.dni,
                ruc: newData.ruc,
            });
        }
    },
    { deep: true, immediate: true }
);



const onSubmit = handleSubmit((values) => {
    const updatedCustomer: CustomerRequestUpdate = {
        name: values.name,
        codigo: values.codigo,
        email: values.email,
        client_type_id: values.client_type_id,
        state: values.state === 'activo',
        dni: values.dni ?? null,
        ruc: values.ruc ?? null,
    };

    emit('updateCustomer', updatedCustomer, props.customerData.id);
    clouseModal();
});
</script>

<style scoped></style>