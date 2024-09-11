<script setup>
import { ref, reactive, computed } from "vue";
import { randomPalette } from "@/config/chartPalette";

const emits = defineEmits(["zoomLevelChanged", "dataSelected"]);

const props = defineProps({
	timeline: {
		required: true,
		type: String,
	},
	chartID: {
		required: true,
		type: String,
	},
	total: {
		required: true,
		type: Number,
	},
	series: {
		required: true,
		type: Array,
	},

	labels: {
		required: true,
		type: Array,
	},

	chart_height: {
		required: true,
		type: Number,
	},
});

// console.log(props.labels);

const chartOptions = reactive({
	labels: props.labels,
	chart: {
		type: "pie",
		zoom: {
			// autoScaleYaxis: true,
			enabled: false,
		},
	},
	
	theme: {
		palette: randomPalette(), // upto palette10
		monochrome: {
			enabled: true,
		},
	},
	dataLabels: {
		enabled: true,
		formatter: function (text, opt) {
			// console.log(text);
			// console.log(opt);

			let percentage = (text.value / props.total) * 100;
			percentage = `${Math.ceil(text)}%`;

			const seriesIndex = opt?.seriesIndex ?? null;

			if (seriesIndex == null) {
				return ["--", percentage];
			}

			return [props.series[seriesIndex] ?? "--", percentage];
		},
	},
	tooltip: {
		intersect: true,
		shared: false,
	},
	markers: {
		size: 10,
	},
});

const chart = ref(null);

const computedSeries = computed(() => {
	let chart_series = props.series;

	return chart_series;
});

const chartClicked = (event, chartContext, opts) => {
	const selectedDataPoint = opts?.dataPointIndex ?? false;

	if (selectedDataPoint === false) {
		return;
	}
	// console.log(selectedDataPoint);
	let selectedData = props.labels[selectedDataPoint] ?? false;

	if (selectedData === false) {
		return;
	}
	// console.log(selectedData);

	emits("dataSelected", selectedData);
};
</script>

<template>
	<div class="my-4">
		<div>
			<apexchart
				:id="chartID"
				ref="chart"
				:height="`${chart_height}px`"
				:options="chartOptions"
				:series="computedSeries"
				@dataPointSelection="chartClicked"
			></apexchart>
		</div>
	</div>
</template>
