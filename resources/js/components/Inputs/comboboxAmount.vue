<template>
    <!-- Estado de carga inicial -->
    <div v-if="!categories.length && !error" class="flex items-center space-x-2 py-2">
        <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-t-2 border-primary"></div>
        <span class="text-sm text-muted-foreground">Cargando categorías...</span>
    </div>

    <!-- Error message -->
    <div v-else-if="error" class="py-2 text-sm text-red-500">Error al cargar categorías. Intente nuevamente.</div>

    <!-- Combobox -->
    <Combobox v-else by="id" v-model="selectedCategory">
        <ComboboxAnchor>
            <div class="relative w-full max-w-sm items-center">
                <ComboboxInput
                    class="pl-9"
                    :model-value="texto"
                    :display-value="(val) => val?.name ?? ''"
                    placeholder="Seleccionar categoría..."
                    @update:model-value="handleSearchInput"
                />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                    <Search class="size-4 text-muted-foreground" />
                </span>
                <!-- Indicador de búsqueda -->
                <span v-if="isSearching" class="absolute inset-y-0 end-0 flex items-center justify-center px-3">
                    <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-t-2 border-primary"></div>
                </span>
            </div>
        </ComboboxAnchor>
        <ComboboxList>
            <ComboboxEmpty>No se encontró ninguna categoría.</ComboboxEmpty>
            <ComboboxGroup>
                <ComboboxItem v-for="category in categories" :key="category.id" :value="category" @select="onSelect(category)">
                    {{ category.name }}
                    <ComboboxItemIndicator>
                        <Check class="ml-auto h-4 w-4" />
                    </ComboboxItemIndicator>
                </ComboboxItem>
            </ComboboxGroup>
        </ComboboxList>
    </Combobox>
    <!-- {{ categories }} -->
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
import { InputCategory } from '@/interface/Inputs';
import debounce from 'debounce';
import { Check, Search } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

// Emits
const emit = defineEmits<{
    (e: 'select', category_id: number): void;
}>();

// Composable y estado
const { categories, loadingCategories } = useAmount();
const texto = ref('');
const error = ref(false);
const isSearching = ref(false);
const selectedCategory = ref<InputCategory | null>(null);

// Manejadores
const handleSearchInput = (value: string) => {
    texto.value = value.toLowerCase();
};

const onSelect = (category: InputCategory) => {
    selectedCategory.value = category;
    emit('select', category.id);
};

// Debounce para la búsqueda
const debounceSearch = debounce(async () => {
    try {
        isSearching.value = true;
        await loadingCategories(texto.value);
        error.value = false;
    } catch (e) {
        console.error('Error al buscar categorías:', e);
        error.value = true;
    } finally {
        isSearching.value = false;
    }
}, 400);

// Observar cambios en el texto de búsqueda
watch(texto, () => {
    debounceSearch();
});

// Carga inicial
onMounted(async () => {
    try {
        await loadingCategories('');
    } catch (e) {
        console.error('Error al cargar categorías:', e);
        error.value = true;
    }
});
</script>
