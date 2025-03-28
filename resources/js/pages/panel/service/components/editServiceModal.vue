<template>
  <Dialog :open="isOpen" @update:open="closeModal">
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Editar Servicio</DialogTitle>
        <DialogDescription>
          Modifica la información del servicio seleccionado
        </DialogDescription>
      </DialogHeader>
      
      <form @submit.prevent="handleSubmit" class="grid gap-4 py-4">
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="name" class="text-right">Nombre</Label>
          <Input 
            id="name" 
            v-model="editedService.name" 
            class="col-span-3" 
            required
          />
        </div>
        
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="cost" class="text-right">Costo</Label>
          <Input 
            id="cost" 
            type="number" 
            step="0.01" 
            v-model.number="editedService.cost" 
            class="col-span-3" 
            required
          />
        </div>
        
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="ini_date" class="text-right">Fecha de Inicio</Label>
          <Input 
            id="ini_date" 
            type="date" 
            :value="formattedInitialDate"
            @change="handleDateChange"
            class="col-span-3" 
            required
          />
        </div>
        
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="state" class="text-right">Estado</Label>
          <Select v-model="editedService.state">
            <SelectTrigger class="col-span-3">
              <SelectValue placeholder="Seleccionar estado" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="activo">Activo</SelectItem>
              <SelectItem value="pendiente">Pendiente</SelectItem>
              <SelectItem value="inactivo">Inactivo</SelectItem>
            </SelectContent>
          </Select>
        </div>
        
        <DialogFooter>
          <Button type="button" variant="outline" @click="closeModal">
            Cancelar
          </Button>
          <Button type="submit" :disabled="isSubmitting">
            {{ isSubmitting ? 'Guardando...' : 'Guardar Cambios' }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { ServiceResource } from '../interface/Service'
import { ServiceServices } from '@/services/serviceServices'

const props = defineProps<{
  service?: ServiceResource
  isOpen: boolean
}>()

const emits = defineEmits(['update:isOpen', 'service-updated'])

const editedService = ref<Partial<ServiceResource>>({})
const isSubmitting = ref(false)

// Computed property to handle initial date formatting
const formattedInitialDate = computed(() => {
  if (!editedService.value.ini_date) return ''
  
  try {
    const date = new Date(editedService.value.ini_date)
    
    // Ensure the date is valid before formatting
    if (isNaN(date.getTime())) return ''
    
    // Format to YYYY-MM-DD
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    
    return `${year}-${month}-${day}`
  } catch {
    return ''
  }
})

// Watch for changes in the service prop
watch(() => props.service, (newService) => {
  if (newService) {
    editedService.value = { ...newService }
  }
}, { immediate: true })

const closeModal = () => {
  emits('update:isOpen', false)
}

// Handle date change
const handleDateChange = (event: Event) => {
  const input = event.target as HTMLInputElement
  
  // Validate and parse the input date
  try {
    const selectedDate = new Date(input.value)
    
    // Ensure the date is valid
    if (!isNaN(selectedDate.getTime())) {
      // Use toISOString to ensure consistent formatting
      editedService.value.ini_date = selectedDate.toISOString()
    }
  } catch {
    // Handle invalid date input
    console.error('Invalid date input')
  }
}

const handleSubmit = async () => {
  if (!editedService.value.id) return

  isSubmitting.value = true
  try {
    const response = await ServiceServices.update(
      editedService.value.id, 
      {
        ...editedService.value,
        // Ensure the date is converted to ISO string
        ini_date: editedService.value.ini_date 
          ? new Date(editedService.value.ini_date).toISOString() 
          : undefined
      }
    )
    emits('service-updated', response)
    closeModal();
    window.location.reload();
  } catch (error) {
    console.error('Error updating service:', error)
    // TODO: Add error handling, perhaps show a toast notification
  } finally {
    isSubmitting.value = false
  }
}
</script>