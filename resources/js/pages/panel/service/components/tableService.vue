<template>
    <div class="container mx-auto py-6 px-0">

      <!-- Edit Service Modal -->
      <EditServiceModal 
        :service="selectedService" 
        :is-open="editModalOpen" 
        @update:is-open="editModalOpen = $event"
        @service-updated="handleServiceUpdate"
      />

       <!-- Delete Confirmation Modal -->
      <DeleteConfirmationModal 
        :service="selectedService" 
        :is-open="deleteConfirmationOpen"
        @update:is-open="deleteConfirmationOpen = $event"
        @service-deleted="handleServiceDelete"
      />
  
      <Table class="w-full border border-gray-100 rounded-lg overflow-clip">
        <TableCaption>Lista de servicios</TableCaption>
        <TableHeader>
          <TableRow>
            <TableHead class="text-center">ID</TableHead>
            <TableHead class="w-[100px]">Nombre</TableHead>
            <TableHead>Costo</TableHead>
            <TableHead>Fecha de Inicio</TableHead>
            <TableHead>Estado</TableHead>
            <TableHead class="text-center">Acciones</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="service in serviceList" :key="service.id">
            <td class="text-center font-bold">{{ service.id }}</td>
            <td>{{ service.name }}</td>
            <td>{{ service.cost.toFixed(2) }}</td>
            <td>{{ formatDate(service.ini_date) }}</td>
            <td class="w-[100px] text-center">
              <span
                :class="{
                  'bg-green-400': service.state === 'activo',
                  'bg-yellow-400': service.state === 'pendiente',
                  'bg-red-400': service.state === 'inactivo'
                }"
                class="text-white px-2 py-1 rounded-full"
              >
                {{ service.state === 'activo' ? 'Activo' : 
                   service.state === 'pendiente' ? 'Pendiente' : 'Inactivo' }}
              </span>
            </td>
            <td class="flex gap-2 justify-center">
              <Button 
                variant="outline" 
                @click="openEditModal(service)"
              >
                <UserPen class="w-4 h-4" />
              </Button>
              <Button 
                variant="link" 
                @click="openDeleteConfirmation(service)"
              >
                <Trash class="w-4 h-4" />
              </Button>
            </td>
          </TableRow>
        </TableBody>
      </Table>
      <PaginationUser 
        :meta="servicePaginate"
        @page-change="$emit('page-change', $event)"
      />
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref } from 'vue'
  import { Pagination } from '@/interface/paginacion'
  import { ServiceResource } from '../interface/Service'
  import Table from '@/components/ui/table/Table.vue'
  import TableCaption from '@/components/ui/table/TableCaption.vue'
  import TableHeader from '@/components/ui/table/TableHeader.vue'
  import TableRow from '@/components/ui/table/TableRow.vue'
  import TableHead from '@/components/ui/table/TableHead.vue'
  import TableBody from '@/components/ui/table/TableBody.vue'
  import Button from '@/components/ui/button/Button.vue'
  import PaginationUser from './paginationService.vue'
  import EditServiceModal from './editServiceModal.vue'
  import { Trash, UserPen } from 'lucide-vue-next'
  import axios from 'axios'
  import DeleteConfirmationModal from './deleteServiceModal.vue';

  const props = defineProps<{
    serviceList: ServiceResource[]
    servicePaginate: Pagination
  }>()
  
  const emits = defineEmits(['page-change', 'services-updated'])
  
  const selectedService = ref<ServiceResource | undefined>(undefined)
  const editModalOpen = ref(false)
  const deleteConfirmationOpen = ref(false)
  
  const openEditModal = (service: ServiceResource) => {
    selectedService.value = service
    editModalOpen.value = true
  }

  const formatDate = (dateString: string) => {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    }).format(date)
  }

  
  const openDeleteConfirmation = (service: ServiceResource) => {
    selectedService.value = service
    deleteConfirmationOpen.value = true
  }
  
  const handleServiceUpdate = (updatedService: ServiceResource) => {
    // Emit event to parent to refresh services list
    emits('services-updated')
    
  }

  const handleServiceDelete = () => {
    // Trigger services list refresh
    emits('services-updated')
  }
  </script>