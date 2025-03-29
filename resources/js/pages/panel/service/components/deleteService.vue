<template>
  <Dialog :open="props.modal" @update:open="emit('closeModal', false)">
      <DialogContent class="sm:max-w-[425px]">
          <DialogHeader>
              <DialogTitle>Eliminar Servicio</DialogTitle>
              <DialogDescription>¿Está seguro de que desea eliminar este servicio?</DialogDescription>
          </DialogHeader>
          <div class="flex justify-end gap-2">
              <Button variant="outline" @click="emit('closeModal', false)">Cancelar</Button>
              <Button variant="destructive" @click="$emit('deleteService', props.serviceId)">Eliminar</Button>
          </div>
      </DialogContent>
  </Dialog>
</template>
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const props = defineProps<{
  modal: boolean;
  serviceId: number;
}>();

const emit = defineEmits<{
  (e: 'closeModal', open: boolean): void;
  (e: 'deleteService', serviceId: number): void;
}>();
</script>
<style scoped></style>
