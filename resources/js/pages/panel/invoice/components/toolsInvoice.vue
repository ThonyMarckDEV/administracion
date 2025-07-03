<template>
    <div class="flex flex-wrap items-center gap-3 p-2">
        <!-- Exportar Excel -->
        <a href="/panel/reports/export-excel-invoices" download>
            <Button variant="ghost" size="sm" class="bg-green-600 hover:bg-green-700 text-white p-2 h-8 w-8" title="Exportar a Excel">
                <FileBarChart class="w-4 h-4 text-white" />
                <span class="sr-only">Exportar Excel</span>
            </Button>
        </a>

        <!-- Exportar PDF -->
        <a href="/panel/reports/export-pdf-invoices" download>
            <Button variant="destructive" class="bg-red-600 hover:bg-red-700 text-white p-2 h-8 w-8" title="Exportar PDF">
                <FileDown class="w-4 h-4 text-white" />
                <span class="sr-only">Exportar PDF</span>
            </Button>
        </a>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { FileBarChart, FileDown } from 'lucide-vue-next'
</script>

<style scoped></style>