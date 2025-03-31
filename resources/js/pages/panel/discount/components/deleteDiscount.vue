<template>
    <Dialog :open="props.modal" @update:open="emit('closeModal', false)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Eliminar descuento</DialogTitle>
                <DialogDescription>¿Está seguro de que desea eliminar este descuento?</DialogDescription>
            </DialogHeader>
            <div class="flex justify-end gap-2">
                <Button variant="outline" @click="emit('closeModal', false)">Cancelar</Button>
                <Button variant="destructive" @click="$emit('deleteDiscount', props.discountId)">Eliminar</Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const props = defineProps<{
    modal: boolean;
    discountId: number;
}>();

const emit = defineEmits<{
    (e: 'closeModal', open: boolean): void;
    (e: 'deleteDiscount', discountId: number): void;
}>();
</script>
<style scoped></style>