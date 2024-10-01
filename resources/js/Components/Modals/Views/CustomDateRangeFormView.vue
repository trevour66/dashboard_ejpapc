<script setup>
import Modal_Structure from "../Modal_Structure.vue";
import { computed, onBeforeUnmount, onMounted, ref } from "vue";

import { useModalStore } from "@/Store/modalStore.js";
import { useLeadChartsStore } from "@/Store/leadCharts";

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const modalStore = useModalStore();
const leadChartStore = useLeadChartsStore();

const date = ref()

const getSelectedDate = computed(() => {
	if(!date.value) return null

	let fromDate = date.value[0]
	let toDate = date.value[1]

	return `${new Date(fromDate).toDateString()} --- ${new Date(toDate).toDateString()}`
})


onMounted(async () => {
	document.body.style.overflow = 'hidden';
});

onBeforeUnmount(() => {
	document.body.style.overflow = 'auto';
})

const saveDate = () => {
	let fromDate = date.value[0] ?? false
	let toDate = date.value[1] ?? false

	if(
		!fromDate ||
		!toDate 
	){
		return null
	}

	leadChartStore.set_customRange_leadTimeframe_to(toDate)
	leadChartStore.set_customRange_leadTimeframe_from(fromDate)
	leadChartStore.setLeadTimeframe('custom');

	modalStore.closeModal();
}

</script>

<template>
	<Modal_Structure>
		<template #title>
			<span>Select Date</span>
		</template>
		<template #content>
			
				<section
					class="h-full min-h-[300px] overflow-y-auto flex flex-col my-3"
				>
					<div class="min-h-[100px] min-w-full">
						<VueDatePicker v-model="date"  range :teleport="true"></VueDatePicker>
					</div>
					<template v-if="getSelectedDate">
						<p class="font-semibold text-gray-700">Selected Date :</p>
						<p class="text-sm text-gray-700">{{ getSelectedDate }}</p>
					</template>
				</section>
		</template>
		<template #footer>
			<button
			@click="saveDate"
				type="button"
				class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center "
			>
				Save Date
			</button>
		</template>
	</Modal_Structure>
</template>
