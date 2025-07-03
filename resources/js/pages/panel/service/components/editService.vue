<template>
  <Dialog :open="modal" @update:open="closeModal">
      <DialogContent class="sm:max-w-[425px]">
          <DialogHeader>
              <DialogTitle>Editar el servicio</DialogTitle>
              <DialogDescription>Los datos están validados, llenar con cuidado.</DialogDescription>
          </DialogHeader>
          <form @submit="onSubmit" class="flex flex-col gap-4 py-4">
              <FormField v-slot="{ componentField }" name="name">
                  <FormItem>
                      <FormLabel>Nombre del Servicio</FormLabel>
                      <FormControl>
                          <Input id="name" type="text" v-bind="componentField" />
                      </FormControl>
                      <FormMessage />
                  </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="cost">
                  <FormItem>
                      <FormLabel>Costo</FormLabel>
                      <FormControl>
                          <Input 
                              id="cost" 
                              type="number" 
                              step="0.01" 
                              v-bind="componentField" 
                          />
                      </FormControl>
                      <FormMessage />
                  </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="ini_date">
                  <FormItem>
                      <FormLabel>Fecha de Inicio</FormLabel>
                      <FormControl>
                          <Input 
                              id="ini_date" 
                              type="date" 
                              v-bind="componentField" 
                          />
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
                  <Button type="button" variant="outline" @click="closeModal">Cancelar</Button>
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
import { ServiceResource, ServiceUpdateRequest } from '../interface/Service';

const props = defineProps<{ modal: boolean; serviceData: ServiceResource }>();
const emit = defineEmits<{
  (e: 'emit-close', open: boolean): void;
  (e: 'update-service', service: ServiceUpdateRequest, id_service: number): void;
}>();

const closeModal = () => emit('emit-close', false);

// Validation schema
const formSchema = toTypedSchema(
  z.object({
      name: z.string().min(2, 'Nombre debe tener al menos 2 caracteres').max(100, 'Máximo 100 caracteres'),
      cost: z.number().min(0, 'El costo debe ser mayor o igual a 0'),
      ini_date: z.string().refine(val => !isNaN(Date.parse(val)), 'Fecha inválida'),
      state: z.enum(['activo', 'inactivo']),
  }),
);

// Form initialization
const { handleSubmit, setValues } = useForm({
  validationSchema: formSchema,
  initialValues: {
      name: props.serviceData.name,
      cost: props.serviceData.cost,
      ini_date: props.serviceData.ini_date,
      state: props.serviceData.state ? 'activo' : 'inactivo',
  },
});

// Watch for changes in service data
watch(
  () => props.serviceData,
  (newData) => {
      if (newData) {
          setValues({
              name: newData.name,
              cost: newData.cost,
              // Extract just the date part from the timestamp
              ini_date: newData.ini_date ? newData.ini_date.split('T')[0] : '',
              state: newData.state ? 'activo' : 'inactivo',
          });
      }
  },
  { deep: true, immediate: true },
);

// Submit handler
const onSubmit = handleSubmit((values) => {
  console.log('Formulario enviado con:', values);
emit('update-service', {
  ...values,
  state: values.state === 'activo'
}, props.serviceData.id);  closeModal();
});
</script>