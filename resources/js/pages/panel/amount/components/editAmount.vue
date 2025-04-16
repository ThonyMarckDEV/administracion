<template>
    <Dialog :open="modal" @update:open="clouseModal">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Editar Egreso</DialogTitle>
                <DialogDescription>
                    <p class="text-sm text-muted-foreground">Edita los datos del egreso</p>
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="onSubmit" class="flex flex-col gap-4 py-4">
                <FormField v-slot="{ componentField }" name="description">
                    <FormItem>
                        <FormLabel>Descripción</FormLabel>
                        <FormControl>
                            <Input id="description" type="text" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="amount">
                    <FormItem>
                        <FormLabel>Monto</FormLabel>
                        <FormControl>
                            <Input id="amount" type="number" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField name="category_id">
                    <FormItem>
                        <div class="flex items-center justify-between">
                            <div class="flex-grow space-y-2">
                                <FormLabel>Categoría</FormLabel>
                                <FormControl>
                                    <ComboboxAmount @select="(id) => setFieldValue('category_id', id)" />
                                </FormControl>
                            </div>
                            <span
                                class="ml-4 self-end rounded-md bg-primary/10 px-2 py-2 text-sm font-medium text-primary dark:bg-primary dark:text-primary-foreground"
                            >
                                {{ amountData.category_name }}
                            </span>
                        </div>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField name="supplier_id">
                    <FormItem>
                        <div class="flex items-center justify-between">
                            <div class="flex-grow space-y-2">
                                <FormLabel>Proveedor</FormLabel>
                                <FormControl>
                                    <ComboBoxSupplier @select="selectSupplier" />
                                </FormControl>
                                <FormMessage />
                            </div>
                            <span
                                class="ml-4 self-end rounded-md bg-primary/10 px-2 py-2 text-sm font-medium text-primary dark:bg-primary dark:text-primary-foreground"
                            >
                                {{ amountData.supplier_name }}
                            </span>
                        </div>
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="date_init">
                    <FormItem>
                        <FormLabel>Fecha</FormLabel>
                        <FormControl>
                            <Input id="date_init" type="date" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <Button type="submit">Guardar Cambios</Button>
            </form>
            <!-- {{ amountData }} -->
        </DialogContent>
    </Dialog>
</template>
<script setup lang="ts">
import ComboBoxSupplier from '@/components/Inputs/comboBoxSupplier.vue';
import ComboboxAmount from '@/components/Inputs/comboboxAmount.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { watch } from 'vue';
import { z } from 'zod';
import { AmountResponseShow } from '../interface/Amount';

const props = defineProps<{
    modal: boolean;
    amountData: AmountResponseShow;
    amount_id: number;
}>();

const emit = defineEmits<{
    (e: 'close-modal', open: boolean): void;
    (e: 'update-amount', amount: AmountResponseShow, amount_id: number): void;
}>();

const clouseModal = () => {
    emit('close-modal', false);
};

const formShema = toTypedSchema(
    z.object({
        description: z.string().min(1, 'Campo requerido'),
        amount: z.number().min(1, 'Campo requerido'),
        category_id: z.number().min(1, 'Campo requerido'),
        supplier_id: z.number().min(1, 'Campo requerido'),
        date_init: z.string({ message: 'Campo obligatorio' }),
    }),
);

const { handleSubmit, setValues, setFieldValue } = useForm({
    validationSchema: formShema,
    initialValues: {
        description: props.amountData.description,
        amount: Number(props.amountData.amount),
        category_id: props.amountData.category_id,
        supplier_id: props.amountData.supplier_id,
        date_init: props.amountData.date_init,
    },
});

watch(
    () => props.amountData,
    (newData) => {
        if (newData) {
            setValues({
                category_id: newData.category_id,
                supplier_id: newData.supplier_id,
                description: newData.description,
                amount: Number(newData.amount),
                date_init: newData.date_init,
            });
        }
    },
    { deep: true, immediate: true },
);

const selectSupplier = (supplier_id: number) => {
    setFieldValue('supplier_id', supplier_id);
    console.log('supplier_id', supplier_id);
};

const onSubmit = handleSubmit((values) => {
    const updatedAmount: AmountResponseShow = {
        ...props.amountData,
        description: values.description,
        amount: values.amount,
        category_id: values.category_id,
        supplier_id: values.supplier_id,
        date_init: values.date_init,
    };
    emit('update-amount', updatedAmount, props.amount_id);
});

const getDate = (date: string) => {
    setFieldValue('date_init', date);
    console.log('date_init', date);
    console.log('date', date);
};
</script>
<style scoped></style>
