<script setup lang="ts">
import { onMounted, PropType, ref, watch } from "vue";
import { ChartData, ChartOptions } from "chart.js";
import Chart from "primevue/chart";
import { useToast } from "primevue/usetoast";
import { getHoursCharted } from "@/Queries/Chart";
import { DateTime } from "luxon";

const props = defineProps({
    projectId: {
        type: Number as PropType<number>,
        required: true,
    },
    startDate: {
        type: DateTime,
        required: true,
    },
    endDate: {
        type: DateTime,
        required: true,
    },
    refresh: {
        type: Number as PropType<number>,
        required: false,
        default: 0,
    },
});
const toast = useToast();
const chartData = ref<ChartData>({
    labels: [],
    datasets: [
        {
            label: "Hours",
            data: [],
            backgroundColor: ["rgba(17, 115, 194, 0.2)"],
            borderColor: ["rgba(17, 115, 194, 0.2)"],
            borderWidth: 1,
        },
    ],
});
const chartOptions = ref<ChartOptions>({
    scales: {
        y: {
            beginAtZero: true,
        },
    },
    plugins: {
        legend: {
            display: false,
        },
    },
    responsive: true,
    maintainAspectRatio: false,
});
const getData = async function () {
    try {
        const { data, labels } = await getHoursCharted(
            parseInt(props.projectId),
            `${props.startDate.toFormat("yyyy-M-dd")} 00:00:01`,
            `${props.endDate.toFormat("yyyy-M-dd")} 23:59:59`,
        );
        chartData.value.labels = labels;
        chartData.value.datasets[0].data = data;
    } catch (error) {
        toast.add({ severity: "error", detail: error, life: 5000 });
    }
};
onMounted(() => {
    getData();
});
/**
 * Watchers
 */
watch(
    () => props.refresh,
    () => {
        getData();
    },
);
</script>

<template>
    <Chart
        type="bar"
        :data="chartData"
        :options="chartOptions"
        :width="800"
        :height="600"
    />
</template>
