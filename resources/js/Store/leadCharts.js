import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { Timeframe } from "@/config/timeframe";

export const useLeadChartsStore = defineStore("leadCharts", () => {
	const leadTimeframe = ref(Timeframe.this_month);

	const getCurrentLeadTimeframe = computed(() => {
		return leadTimeframe.value;
	});
	const getFromAndToDatetimeOfCurrentLeadTimeframe = computed(() => {
		const today = new Date();
		let from = null;

		switch (leadTimeframe.value) {
			case Timeframe.this_week:
				from = new Date().setDate(today.getDate() - 7);
				from = new Date(from);
				break;

			case Timeframe.this_month:
				from = new Date().setMonth(today.getMonth() - 1);
				from = new Date(from);
				break;

			case Timeframe.two_months:
				from = new Date().setMonth(today.getMonth() - 2);
				from = new Date(from);
				break;

			case Timeframe.three_months:
				from = new Date().setMonth(today.getMonth() - 3);
				from = new Date(from);
				break;

			case Timeframe.six_months:
				from = new Date().setMonth(today.getMonth() - 6);
				from = new Date(from);
				break;

			case Timeframe.one_year:
				from = new Date().setFullYear(today.getFullYear() - 1);
				from = new Date(from);
				break;

			case Timeframe.all:
				from = "all";

				break;
		}

		return {
			to: today,
			from: from,
		};
	});

	const setLeadTimeframe = (timeframe) => {
		for (let timeframeValues of Object.values(Timeframe)) {
			if ((timeframe ?? false) && timeframe === timeframeValues) {
				leadTimeframe.value = timeframe;
			}
		}
	};

	return {
		getCurrentLeadTimeframe,
		getFromAndToDatetimeOfCurrentLeadTimeframe,
		setLeadTimeframe,
	};
});
