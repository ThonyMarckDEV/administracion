<template>
    <Dialog :open="statusModal" @update:open="closeModal">
        <DialogContent class="max-w-4xl h-[80vh]">
            <DialogTitle>Vista del Comprobante</DialogTitle>
            <DialogDescription>
                Vista previa del PDF del comprobante.
            </DialogDescription>
            <div class="mt-4 h-[calc(80vh-120px)]">
                <iframe
                    v-if="props.pdfUrl"
                    :src="props.pdfUrl"
                    class="w-full h-full border-0"
                    title="Vista previa del PDF"
                ></iframe>
                <div v-else class="flex items-center justify-center h-full">
                    <p class="text-red-500">Error al cargar el PDF</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <Button variant="outline" @click="closeModal">Cerrar</Button>
            </div>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import Button from '@/components/ui/button/Button.vue';
import { ref, watch, onUnmounted } from 'vue';

const props = defineProps<{
    statusModal: boolean;
    pdfUrl: string | null;
}>();

const emit = defineEmits<{
    (e: 'close-modal'): void;
}>();

const closeModal = () => {
    emit('close-modal');
};

// Track the current object URL for revocation
const currentUrl = ref<string | null>(null);

watch(
    () => props.pdfUrl,
    (newUrl) => {
        if (currentUrl.value && currentUrl.value !== newUrl) {
            URL.revokeObjectURL(currentUrl.value);
        }
        currentUrl.value = newUrl;
    }
);

onUnmounted(() => {
    if (currentUrl.value) {
        URL.revokeObjectURL(currentUrl.value);
        currentUrl.value = null;
    }
});
</script>
