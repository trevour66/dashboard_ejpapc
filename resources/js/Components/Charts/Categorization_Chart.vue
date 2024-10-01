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

	chart_height: {
		required: true,
		type: Number,
	},
});

const roundToTwoDecimals = (value) => {
    // Check if the value is a number and not NaN
    if (typeof value === 'number' && !isNaN(value)) {
        // Round to 2 decimal places
        return `${Math.round(value * 100) / 100} -`;
    } else {
        return '';
    }
}

const chartOptions = reactive({
	chart: {
		type: "treemap",
		zoom: {
			// autoScaleYaxis: true,
			enabled: false,
		},
		events: {},
	},
	theme: {
		palette: randomPalette(), // upto palette10
	},
	dataLabels: {
		enabled: true,
		formatter: function (text, opt) {
			let percentage = (opt.value / props.total) * 100;
			percentage = `${roundToTwoDecimals(opt.value)} ${Math.ceil(percentage)}%`;
			return [text, percentage];
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
	let chart_series = [{ data: props.series }];

	return chart_series;
});

const chartClicked = (event, chartContext, opts) => {
	const selectedDataPoint = opts?.dataPointIndex ?? false;

	if (selectedDataPoint === false) {
		return;
	}

	let selectedData = computedSeries?.value[0]?.data[selectedDataPoint] ?? false;

	if (selectedData === false) {
		return;
	}

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
