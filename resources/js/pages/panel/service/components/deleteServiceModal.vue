<template>
    <Dialog :open="isOpen" @update:open="closeModal">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>¿Estás seguro de eliminar este servicio?</DialogTitle>
          <DialogDescription>
            Esta acción no se puede deshacer. Se eliminará permanentemente el servicio "{{ service?.name }}".
          </DialogDescription>
        </DialogHeader>
        
        <DialogFooter>
          <Button variant="outline" @click="closeModal">
            Cancelar
          </Button>
          <Button 
            variant="destructive" 
            @click="confirmDelete"
            :disabled="isDeleting"
          >
            {{ isDeleting ? 'Eliminando...' : 'Eliminar' }}
          </Button>
        </DialogFooter>
  
        <!-- Error Message -->
        <div v-if="errorMessage" class="text-red-500 mt-2">
          {{ errorMessage }}
        </div>
      </DialogContent>
    </Dialog>
  </template>
  
  <script setup lang="ts">
  import { ref } from 'vue'
  import { 
    Dialog, 
    DialogContent, 
    DialogDescription, 
    DialogFooter, 
    DialogHeader, 
    DialogTitle 
  } from '@/components/ui/dialog'
  import { Button } from '@/components/ui/button'
  import { ServiceResource } from '../interface/Service'
  import { ServiceServices } from '@/services/serviceServices'
  import axios from 'axios'
  
  const props = defineProps<{
    service?: ServiceResource
    isOpen: boolean
  }>()
  
  const emits = defineEmits(['update:isOpen', 'service-deleted'])
  
  const isDeleting = ref(false)
  const errorMessage = ref('')
  
  const closeModal = () => {
    emits('update:isOpen', false)
    errorMessage.value = '' // Reset error message
  }
  
  const confirmDelete = async () => {
    if (!props.service?.id) return
  
    isDeleting.value = true
    errorMessage.value = '' // Clear previous errors
  
    try {
      // Delete the service
      await ServiceServices.destroy(props.service.id)
      
      // Emit event to parent to refresh services list
      emits('service-deleted')
      
      // Close the modal
      closeModal()
      window.location.reload();
    } catch (error) {
      // More detailed error handling
      if (axios.isAxiosError(error)) {
        if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx
          errorMessage.value = `Error: ${error.response.status} - ${error.response.data.message || 'No se pudo eliminar el servicio'}`
        } else if (error.request) {
          // The request was made but no response was received
          errorMessage.value = 'No se recibió respuesta del servidor'
        } else {
          // Something happened in setting up the request that triggered an Error
          errorMessage.value = 'Error al configurar la solicitud'
        }
      } else {
        // Fallback for non-axios errors
        errorMessage.value = 'Error desconocido al eliminar el servicio'
      }
      
      console.error('Error deleting service:', error)
    } finally {
      isDeleting.value = false
    }
  }
  </script>