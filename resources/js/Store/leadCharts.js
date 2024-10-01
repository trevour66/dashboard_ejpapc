import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { Timeframe } from "@/config/timeframe";

export const useLeadChartsStore = defineStore("leadCharts", () => {
	const leadTimeframe = ref(Timeframe.this_month);

	const customRange_leadTimeframe_to = ref();
	const customRange_leadTimeframe_from = ref();

	const getCurrentLeadTimeframe = computed(() => {
		return leadTimeframe.value;
	});

	const resetRange = () => {
		customRange_leadTimeframe_from.value = ''
		customRange_leadTimeframe_to.value = ''
	}

	const getThisWeekDates = () => {
		// Ensure the current date is a valid Date object
		let currentDate = new Date();
	
		// Calculate the start (Monday) and end (Sunday) of "this week"
		let dayOfWeek = currentDate.getDay();
		let diffToMonday = (dayOfWeek === 0 ? 6 : dayOfWeek - 1); // If it's Sunday, treat it as the last day of the week
		let startOfWeek = new Date(currentDate);
		startOfWeek.setDate(currentDate.getDate() - diffToMonday); // Go back to Monday
		let endOfWeek = new Date(startOfWeek);
		endOfWeek.setDate(startOfWeek.getDate() + 6); // Go forward to Sunday
	
		return {
			from: startOfWeek, // Format as YYYY-MM-DD
			to: endOfWeek
		};
	}	

	const getThisMonthDates = () => {
		// Ensure the current date is a valid Date object
		let currentDate = new Date();
	
		// Calculate the start (1st) and end (last day) of "this month"
		let startOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1); // 1st day of current month
		let endOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0); // Last day of current month
	
		return {			
			from: startOfMonth,
			to: endOfMonth
		};
	}	

	const getFromAndToDatetimeOfCurrentLeadTimeframe = computed(() => {
		const today = new Date();
		let from = null;
		let to = today

		switch (leadTimeframe.value) {
			case Timeframe.this_week:
				const thisWeekDates = getThisWeekDates()
				from = thisWeekDates.from
				to = thisWeekDates.to
				resetRange()
				break;

			case Timeframe.this_month:
				const thisMonthDates = getThisMonthDates()
				from = thisMonthDates.from
				to = thisMonthDates.to
				resetRange()
				break;

			case Timeframe.custom:
				from = customRange_leadTimeframe_from.value
				to = customRange_leadTimeframe_to.value
				
				break;

			case Timeframe.all:
				from = "all";
				resetRange()
				break;
		}

		return {
			to: to,
			from: from,
		};
	});

	const getRangeDateString = computed(() => {
		let fromDate = customRange_leadTimeframe_from.value ?? false
		let toDate = customRange_leadTimeframe_to.value ?? false

		if(
			!fromDate ||
			!toDate 
		){
			return false
		}

		return `${new Date(fromDate).toDateString()} --- ${new Date(toDate).toDateString()}`
	})

	const get_customRange_leadTimeframe_to = computed(() => {
		return customRange_leadTimeframe_to.value
	})

	const get_customRange_leadTimeframe_from = computed(() => {
		return customRange_leadTimeframe_from.value
	})

	const set_customRange_leadTimeframe_to = (dateTime) => {
		customRange_leadTimeframe_to.value = dateTime
	};

	const set_customRange_leadTimeframe_from = (dateTime) => {
		customRange_leadTimeframe_from.value = dateTime
	};

	const setLeadTimeframe = (timeframe) => {
		// console.log(timeframe)
		// return
		for (let timeframeValues of Object.values(Timeframe)) {
			if ((timeframe ?? false) && timeframe === timeframeValues) {
				leadTimeframe.value = timeframe;
			}
		}
	};

	return {
		getCurrentLeadTimeframe,
		getFromAndToDatetimeOfCurrentLeadTimeframe,
		getRangeDateString,
		get_customRange_leadTimeframe_to,
		get_customRange_leadTimeframe_from,
		set_customRange_leadTimeframe_to,
		set_customRange_leadTimeframe_from,
		setLeadTimeframe,
	};
});
