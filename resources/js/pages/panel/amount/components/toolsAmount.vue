<template>
    <div class="flex flex-wrap items-center gap-3 p-2">
        <!-- Exportar Excel -->
        <a href="/panel/reports/export-excel-amounts" download>
            <Button variant="ghost" size="sm" class="h-8 w-8 bg-green-600 p-2 text-white hover:bg-green-700" title="Exportar a Excel">
                <FileBarChart class="h-4 w-4 text-white" />
                <span class="sr-only">Exportar Excel</span>
            </Button>
        </a>

        <!-- Importar Excel -->
        <div>
            <input type="file" ref="fileRef" accept=".xlsx" class="hidden" @change="handleFileChange" />
            <Button @click="handleImportClick" variant="default" class="h-8 w-8 bg-blue-600 p-2 text-white hover:bg-blue-700" title="Importar Excel">
                <FileUp class="h-4 w-4 text-white" />
                <span class="sr-only">Importar Excel</span>
            </Button>
        </div>

        <!-- Exportar PDF -->
        <a href="/panel/reports/export-pdf-amounts" download>
            <Button variant="destructive" class="h-8 w-8 bg-red-600 p-2 text-white hover:bg-red-700" title="Exportar PDF">
                <FileDown class="h-4 w-4 text-white" />
                <span class="sr-only">Exportar PDF</span>
            </Button>
        </a>
    </div>
</template>
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast';
import axios from 'axios';
import { FileBarChart, FileDown, FileUp } from 'lucide-vue-next';
import { ref } from 'vue';

const fileRef = ref<HTMLInputElement | null>(null);
const { toast } = useToast();

const emit = defineEmits<{
    (e: 'import-success'): void;
}>();

const handleImportClick = () => {
    fileRef.value?.click();
};

const handleFileChange = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;
    const formData = new FormData();
    formData.append('archivo', file);

    try {
        await axios.post('/panel/reports/import-excel-amounts', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        toast({
            title: '✅ Importación exitosa',
            description: 'Los egresos fueron importados correctamente.',
        });

        emit('import-success');
        target.value = '';
    } catch (error) {
        toast({
            variant: 'destructive',
            title: '❌ Error al importar',
            description: 'Revisa que el archivo sea válido (.xlsx) y vuelve a intentarlo.',
        });
        console.error(error);
    }
};
</script>

<style scoped></style>
