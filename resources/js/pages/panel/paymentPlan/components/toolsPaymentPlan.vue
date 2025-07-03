<template>
    <div class="flex flex-wrap items-center gap-3 p-2">
        <!-- Exportar Excel -->
        <a href="/panel/reports/export-excel-payment_plans" download>
            <Button variant="ghost" size="sm" class="bg-green-600 hover:bg-green-700 text-white p-2 h-8 w-8" title="Exportar a Excel">
                <FileBarChart class="w-4 h-4 text-white" />
                <span class="sr-only">Exportar Excel</span>
            </Button>
        </a>

        <!-- Exportar PDF -->
        <a href="/panel/reports/export-pdf-payment_plans" download>
            <Button variant="destructive" class="bg-red-600 hover:bg-red-700 text-white p-2 h-8 w-8" title="Exportar PDF">
                <FileDown class="w-4 h-4 text-white" />
                <span class="sr-only">Exportar PDF</span>
            </Button>
        </a>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { FileBarChart, FileDown, FileUp } from 'lucide-vue-next'
import axios from 'axios'
import { useToast } from '@/components/ui/toast'

const fileRef = ref<HTMLInputElement | null>(null)
const { toast } = useToast()

const emit = defineEmits<{
    (e: 'import-success'): void
}>()

const handleImportClick = () => {
    fileRef.value?.click()
}

const handleFileChange = async (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]
    if (!file) return

    const formData = new FormData()
    formData.append('archivo', file)

    try {
        await axios.post('/panel/reports/import-excel-payment-plans', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })

        toast({
            title: '✅ Importación exitosa',
            description: 'Los planes de pago fueron importados correctamente.',
        })

        emit('import-success')
        target.value = ''
    } catch (error) {
        toast({
            variant: 'destructive',
            title: '❌ Error al importar',
            description: 'Revisa que el archivo sea válido (.xlsx) y vuelve a intentarlo.',
        })
        console.error(error)
    }
}
</script>

<style scoped></style>
