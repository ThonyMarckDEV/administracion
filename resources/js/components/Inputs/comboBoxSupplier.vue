<template>
    <!-- status loading -->
    <div v-if="!suppliers.length && !error" class="flex items-center space-x-2 py-2">
        <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-t-2 border-primary"></div>
        <span class="text-sm text-muted-foreground">Cargando proveedores...</span>
    </div>

    <!-- Error message -->
    <div v-else-if="error" class="py-2 text-sm text-red-500">Error al cargar proveedores. Intente nuevamente.</div>

    <!-- Combobox -->
    <Combobox v-else by="id" v-model="selectedSupplier">
        <ComboboxAnchor>
            <div class="relative w-full max-w-sm items-center">
                <ComboboxInput
                    class="pl-9"
                    :display-value="(val) => val?.name ?? ''"
                    :model-value="texto"
                    placeholder="Seleccionar proveedor..."
                    @update:model-value="heandleSearchInput"
                />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                    <Search class="size-4 text-muted-foreground" />
                </span>
                <!-- search -->
                <span v-if="isSearching" class="absolute inset-y-0 end-0 flex items-center justify-center px-3">
                    <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-t-2 border-primary"></div>
                </span>
            </div>
        </ComboboxAnchor>
        <ComboboxList>
            <ComboboxEmpty>No se encontró ningún proveedor.</ComboboxEmpty>
            <ComboboxGroup>
                <ComboboxItem v-for="supplier in suppliers" :key="supplier.id" :value="supplier" @select="onSelect(supplier)">
                    {{ supplier.name }}
                    <ComboboxItemIndicator>
                        <Check class="ml-auto h-4 w-4" />
                    </ComboboxItemIndicator>
                </ComboboxItem>
            </ComboboxGroup>
        </ComboboxList>
    </Combobox>
</template>
<script setup lang="ts">
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
} from '@/components/ui/combobox';
import { useAmount } from '@/composables/useAmount';
import { InputSupplier } from '@/interface/Inputs';
import debounce from 'debounce';
import { Check, Search } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const emit = defineEmits<{
    (e: 'select', supplier_id: number): void;
}>();

// composable and state
const { suppliers, loadingSuppliers } = useAmount();
const texto = ref<string>('');
const error = ref<boolean>(false);
const isSearching = ref<boolean>(false);
const selectedSupplier = ref<InputSupplier | null>(null);

const heandleSearchInput = (value: string) => {
    texto.value = value.toLowerCase();
};

const onSelect = (supplier: InputSupplier) => {
    selectedSupplier.value = supplier;
    emit('select', supplier.id);
};

const debounceSearch = debounce(async () => {
    try {
        isSearching.value = true;
        await loadingSuppliers(texto.value);
        error.value = false;
    } catch (e) {
        error.value = true;
    } finally {
        isSearching.value = false;
    }
}, 400);

watch(texto, () => {
    debounceSearch();
});
onMounted(async () => {
    try {
        await loadingSuppliers('');
    } catch (e) {
        error.value = true;
    }
});
</script>
<style></style>
