<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" :class="cn('w-[280px] justify-start text-left font-normal', !selectedDate && 'text-muted-foreground')">
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ selectedDate ? df.format(selectedDate.toDate(getLocalTimeZone())) : 'Seleccionar fecha' }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar v-model="selectedDate" initial-focus :disabled="disabled" />
        </PopoverContent>
    </Popover>
</template>
<script setup lang="ts">
import { cn } from '@/lib/utils';
import { DateFormatter, type DateValue, getLocalTimeZone, parseDate } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';
import { Button } from './ui/button';
import { Calendar } from './ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from './ui/popover';
const props = defineProps<{
    date_init?: string;
    disabled: boolean;
}>();

const selectedDate = ref<DateValue>();
const emit = defineEmits<{
    (e: 'update-date', date: string): void;
}>();

const df = new DateFormatter('es-PE', {
    dateStyle: 'long',
});

watch(
    selectedDate,
    (newDate) => {
        if (newDate) {
            emit('update-date', newDate.toString());
        }
    },
    { immediate: true },
);

onMounted(() => {
    if (props.date_init) {
        selectedDate.value = parseDate(props.date_init);
    }
});
</script>
<style scoped></style>
