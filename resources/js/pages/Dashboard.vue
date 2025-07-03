<script setup lang="ts">
import CustomerChart from '@/components/Charts/CustomerChart.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

const props = defineProps<{
  labels: string[];
  pagados: number[];
  noPagados: number[];
  pendientes: number[];
  montosPagados: number;
  labelsMontos: string[];
  valoresMontos: number[];
  labelsPorAnio: string[];
  montosPorAnio: number[];
  clientesChart: {
    labels: string[];
    values: number[];
  };

}>();

const chartData = {
  comprobantes: {
    labels: props.labels,
    series: [
      { name: 'Pagados', data: props.pagados },
      { name: 'Vencidos', data: props.noPagados },
      { name: 'Pendientes', data: props.pendientes },
    ],
  },
  montos: {
    labels: props.labelsMontos,
    values: props.valoresMontos,
  },
    ventasAnuales: {
    labels: props.labelsPorAnio,
    values: props.montosPorAnio,
  },
  clientes: {
    labels: props.clientesChart.labels,
    values: props.clientesChart.values,
  },
};

console.log('Clientes Labels:', chartData.clientes.labels);
console.log('Clientes Values:', chartData.clientes.values);

</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <!-- Módulo 1: Comprobantes por mes -->
        <div
          class="relative aspect-auto overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
          <CustomerChart
            :columns-x="chartData.comprobantes.labels"
            :series="chartData.comprobantes.series"
            title="Comprobantes por mes en el año"
            subtitle="Pagados - Vencidos - Pendientes"
            chart-type="bar"
          />
        </div>

          <!-- Módulo 2: Ventas anuales -->
          <div
            class="relative aspect-auto overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
          >
            <CustomerChart
              :columns-x="chartData.ventasAnuales.labels"
              :series="[
                { name: 'Ventas Pagadas', data: chartData.ventasAnuales.values }
              ]"
              title="Ventas Anuales"
              subtitle="Montos pagados por año"
              chart-type="area"
            />
          </div>

        <!-- Módulo 3: Clientes con mayor monto pagado -->
          <div class="relative aspect-auto overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <CustomerChart
  :columns-x="chartData.clientes.labels"
  :series="[
    { name: 'Clientes', data: chartData.clientes.values }
  ]"
  title="Top Clientes"
  subtitle="Clientes con mayores montos pagados"
  chart-type="bar"
/>
          </div>
      </div>
      <!-- Módulo grande: Gráfico de Montos Pagados -->
<div class="relative h-[400px] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6">
<CustomerChart
  :columns-x="chartData.montos.labels"
  :series="[
    { name: 'Montos Pagados', data: chartData.montos.values }
  ]"
  title="Montos Pagados"
  subtitle="Total de montos pagados por mes"
  chart-type="radar"
  :show-total="true"
/>
</div>
    </div>
  </AppLayout>
</template>