<script setup>
import { ref, reactive, watchEffect, onMounted, onUpdated } from "vue";
import { useElementVisibility } from "@vueuse/core";
import { Timeframe } from "@/config/timeframe";
import generateRandomId from "@/utils/randomId";
import {
   getLastMonthDates,
   getThisQuarterDates,
   getThisYearDates,
} from "@/utils/date_to_and_from_generator";

import { useLeadChartsStore } from "@/Store/leadCharts";

const leadChartStore = useLeadChartsStore();

const emits = defineEmits(["zoomLevelChanged"]);

const props = defineProps({
   timeline: {
      required: true,
      type: String,
   },
   dataSet: {
      required: true,
      type: Object,
   },
});

const series = reactive([
   {
      name: props.dataSet.name,
      data: props.dataSet.data,
   },
]);
const chartOptions = reactive({
   chart: {
      id: generateRandomId(7),
      type: "area",
      height: 350,
      zoom: {
         // autoScaleYaxis: true,
         enabled: false,
      },
   },
   dataLabels: {
      enabled: false,
   },
   markers: {
      size: 0,
      style: "hollow",
   },
   xaxis: {
      type: "datetime",
      tickAmount: 6,
   },
   tooltip: {
      x: {
         format: "dd MMM yyyy",
      },
   },
   fill: {
      type: "gradient",
      gradient: {
         shadeIntensity: 1,
         opacityFrom: 0.7,
         opacityTo: 0.9,
         stops: [0, 100],
      },
   },
   stroke: {
      width: 1,
   },
});

const chart = ref(null);
const chartIsVisible = useElementVisibility(chart);

watchEffect(() => {
   if (props.timeline && chartIsVisible.value) {
      updateData();
   }
});

const emitTotalNewLeadInTimerange = (from, to) => {
   emits("zoomLevelChanged", from, to);
};

const updateData = () => {
   const today = new Date();
   let from = null;
   let to = null;

   switch (props.timeline) {
      case Timeframe.this_week:
         from = new Date().setDate(today.getDate() - 7);
         from = new Date(from).getTime();
         chart.value.zoomX(from, today.getTime());

         break;

      case Timeframe.this_month:
         from = new Date().setMonth(today.getMonth() - 1);
         from = new Date(from).getTime();
         chart.value.zoomX(from, today.getTime());

         break;

      case Timeframe.last_month:
         let lastMonthDates = getLastMonthDates();

         to = lastMonthDates.to.getTime();
         from = lastMonthDates.from.getTime();

         chart.value.zoomX(from, to);

         break;

      case Timeframe.this_quarter:
         let thisQuarterDates = getThisQuarterDates();

         to = thisQuarterDates.to.getTime();
         from = thisQuarterDates.from.getTime();

         chart.value.zoomX(from, to);

         break;

      case Timeframe.this_year:
         let thisYearDates = getThisYearDates();

         to = thisYearDates.to.getTime();
         from = thisYearDates.from.getTime();

         chart.value.zoomX(from, to);

         break;

      case Timeframe.one_year:
         from = new Date().setFullYear(today.getFullYear() - 1);
         from = new Date(from).getTime();
         chart.value.zoomX(from, today.getTime());

         break;

      case Timeframe.custom:
         from = leadChartStore.get_customRange_leadTimeframe_from;
         to = leadChartStore.get_customRange_leadTimeframe_to;

         from = new Date(from).getTime();
         to = new Date(to).getTime();

         console.log(from);
         console.log(to);

         chart.value.zoomX(from, to);

         break;

      case Timeframe.all:
         let firstData = props.dataSet.data[0]; // data coming most be chronologically sorted, oldst first
         let firstDataTimestamp = firstData[0];

         chart.value.zoomX(firstDataTimestamp, today.getTime());

         break;
   }

   emitTotalNewLeadInTimerange(from, today.getTime());
};
</script>

<template>
   <div class="my-4">
      <div>
         <apexchart
            ref="chart"
            :options="chartOptions"
            :series="series"
         ></apexchart>
      </div>
   </div>
</template>
