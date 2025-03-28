<template>
    <Dialog :open="modal" @update:open="clouseModal">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Editar el usuario</DialogTitle>
                <DialogDescription>Los datos estan validados, llenar con cuidado.</DialogDescription>
            </DialogHeader>
            <div v-if="userData" class="flex flex-col gap-4 py-4">
                <div class="flex items-center gap-4">
                    <Label for="name" class="w-20 text-right">Nombre y Apellidos</Label>
                    <Input id="name" :default-value="userData.name" class="flex-1" />
                </div>
                <div class="flex items-center gap-4">
                    <Label for="email" class="w-20 text-right">Correo</Label>
                    <Input id="email" type="email" :default-value="userData.email" class="flex-1" />
                </div>
                <div class="flex items-center gap-4">
                    <Label for="username" class="w-20 text-right">Usuario</Label>
                    <Input id="username" :default-value="userData.username" class="flex-1" />
                </div>
                <div class="flex items-center gap-4">
                    <Label for="status" class="w-20 text-right">estado</Label>
                    <Select :model-value="userData.status === true ? 'activo' : 'inactivo'">
                        <SelectTrigger class="flex-1">
                            <SelectValue placeholder="estado" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>Estado</SelectLabel>
                                <SelectItem value="activo">Activo</SelectItem>
                                <SelectItem value="inactivo">Inactivo</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <DialogFooter>
                <Button type="submit">Guardar cambios</Button>
                <Button variant="outline" @click="clouseModal">Cancel</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ref } from 'vue';
import { UserResource } from '../interface/User';

const props = defineProps<{
    modal: boolean;
    userData: UserResource;
}>();

const emit = defineEmits<{
    (e: 'emit-close', open: boolean): void;
}>();

// Estado reactivo para el valor de status
const status = ref(props.userData.status ? 'activo' : 'inactivo');

const clouseModal = () => {
    emit('emit-close', false);
};
</script>
<style scoped></style>
