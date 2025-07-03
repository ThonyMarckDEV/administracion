<template>
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <VueApexCharts :type="props.chartType" :options="options" :series="props.series" />
  </div>
</template>

<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { computed, onMounted, ref, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps<{
  columnsX: string[];
  series: Array<{ name: string; data: number[] }>;
  title: string;
  subtitle: string;
  chartType: 'bar' | 'line' | 'area' | 'radar' | 'scatter' | 'heatmap' | 'polarArea';
  height?: number;
  width?: number;
  showTotal?: boolean;
}>();

const totalSum = computed(() =>
  props.series?.[0]?.data?.reduce((a, b) => a + b, 0) ?? 0
);

const { appearance } = useAppearance();
const isDarkMode = computed(
  () =>
    appearance.value === 'dark' ||
    (appearance.value === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches),
);

const chartColors = ref<string[]>([]);

const getCssVariable = (variableName: string): string => {
  const color = getComputedStyle(document.documentElement).getPropertyValue(variableName).trim();
  return color.includes(' ') ? `hsl(${color})` : color;
};

const updateChartColors = () => {
  chartColors.value = ['#22c55e', '#ef4444', '#facc15']; // Verde , Rojo, Amarillo
};

onMounted(() => {
  updateChartColors();
});
watch(isDarkMode, updateChartColors);

const options = computed(() => ({
  chart: {
    height: props.height ?? '100%',
    width: props.width ?? '100%',
    background: 'transparent',
    toolbar: { show: true },
    animations: { enabled: true },
  },
  title: {
    text: props.title,
    align: 'left',
  },
subtitle: {
  text: props.showTotal
    ? `${props.subtitle} (Total: S/. ${totalSum.value.toFixed(2)})`
    : props.subtitle,
  align: 'left',
  style: {
    color: props.showTotal ? '#16a34a' : undefined, 
    fontWeight: 500,
  },
},
  xaxis: {
    categories: props.columnsX,
  },
  dataLabels: {
    enabled: false,
  },
  plotOptions: {
    bar: {
      distributed: false,
    },
  },
  colors: chartColors.value,
  theme: {
    mode: isDarkMode.value ? 'dark' : 'light',
  },
  tooltip: {
    theme: isDarkMode.value ? 'dark' : 'light',
  },
  grid: {
    borderColor: getCssVariable('--border'),
  },
}));
</script>

<style scoped></style>
