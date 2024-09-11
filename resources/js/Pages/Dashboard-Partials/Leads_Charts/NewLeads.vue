<script setup>
import { ref, reactive, watchEffect, onMounted } from "vue";
import DatetimeChart from "../../../Components/Charts/DatetimeChart.vue";
import { useLeadChartsStore } from "../../../Store/leadCharts";

import { ModalTypes } from "@/config/modalConfig";
import { useModalStore } from "@/Store/modalStore.js";

const props = defineProps({
	dataSet: {
		required: true,
		type: Object,
	},
});

const leadChartStore = useLeadChartsStore();
const modalStore = useModalStore();

const meta = reactive(
	{
		label: "New Leads",
		dataUrl: route("leads.getSelectedNewLeads"),
		data: {
			count: null,
		},
		date_used: {
			to: null,
			from: null,
		},
	},
)

const sumOfVisibleNewLeads = ref(0);

const calculateTotal = (from, to) => {
	meta.date_used.to = new Date(to).toISOString() ?? null
	meta.date_used.from = new Date(from).toISOString() ?? null

	let sum = 0;
	for (let i = 0; i < props.dataSet.data.length; i++) {
		if (from === null) {
			sum += props.dataSet.data[i][1];
		} else {
			if (
				props.dataSet.data[i][0] >= Number(from) &&
				Number(props.dataSet.data[i][0]) <= Number(to)
			) {
				sum += props.dataSet.data[i][1];
			}
		}
	}

	sumOfVisibleNewLeads.value = sum ?? "__";
};

const hasData = () => {
	if(
		(props?.dataSet?.data ?? []).length > 0
	){
		return true
	}

	return false
}


const initModal = () => {
	// console.log(selectedData);

	if (!meta) return;

	let modal = "";

	for (const key in ModalTypes) {
		if (Object.hasOwnProperty.call(ModalTypes, key)) {
			const element = ModalTypes[key];
			if (element.label === meta.label) {
				modal = key;
			}
		}
	}

	console.log(modal);

	if (!modal) return;

	let selectedModal = ModalTypes[modal] ?? null;

	if (selectedModal == null) return;

	modalStore.setModal(selectedModal);

	const modalInitData = { ...meta };

	modalStore.setModalIntializationData(modalInitData);
};

</script>

<template>
	<div>
		<!-- {{ dataSet }} -->
		<div class="w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4">
			<div
				class="flex justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700"
			>
				<div class="flex items-center">
					<div>
						<h5
							class="leading-none text-lg font-bold text-gray-700 dark:text-white pb-1"
						>
							New leads: {{ sumOfVisibleNewLeads }}
						</h5>
					</div>
				</div>
				<div>
					<div v-if="meta != null && hasData()">
						<button
							@click="initModal(meta)"
							type="button"
							class="text-white bg-gray-200 hover:bg-gray-300 focus:ring-2 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center me-2"
						>
							<svg
								class="w-3 h-3"
								xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 512 512"
							>
								<path
									d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l82.7 0L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3l0 82.7c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160c0-17.7-14.3-32-32-32L320 0zM80 32C35.8 32 0 67.8 0 112L0 432c0 44.2 35.8 80 80 80l320 0c44.2 0 80-35.8 80-80l0-112c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 112c0 8.8-7.2 16-16 16L80 448c-8.8 0-16-7.2-16-16l0-320c0-8.8 7.2-16 16-16l112 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 32z"
								/>
							</svg>
						</button>
					</div>
				</div>
			</div>
			<div class="my-2">
				<DatetimeChart
					@zoomLevelChanged="calculateTotal"
					:timeline="leadChartStore.getCurrentLeadTimeframe"
					:dataSet="dataSet"
				/>
			</div>
		</div>
	</div>
</template>
