<script setup>
import { ref, reactive, watchEffect, computed, onMounted } from "vue";
import Categorization_PieChart from "../../../Components/Charts/Categorization_Chart.vue";
import { useLeadChartsStore } from "../../../Store/leadCharts";

import { ModalTypes } from "@/config/modalConfig";
import { useModalStore } from "@/Store/modalStore.js";

const props = defineProps({
	dataSet: {
		required: true,
		type: Array,
	},
	meta: {
		required: true,
		type: Object,
	},
});

const leadChartStore = useLeadChartsStore();
const modalStore = useModalStore();

const dataSet_labels = ref([]);
const dataSet_series = ref([]);
const dataSet_total = ref(0);

onMounted(() => {
	if ((props.dataSet ?? []).length > 0) {
		for (let index = 0; index < props.dataSet.length; index++) {
			const el = props.dataSet[index];
			const LS_source = el?.LS_source ?? false;
			const lead_count = el?.lead_count ?? 0;

			if (LS_source) {
				dataSet_labels.value.push(LS_source);
				// dataSet_series.value.push(lead_count);
				dataSet_series.value.push({
					x: LS_source,
					y: lead_count,
				});

				dataSet_total.value = dataSet_total.value + lead_count;
			}
		}
	}
});

const loadModal = (selectedData) => {
	// console.log(selectedData);

	const selected_x = selectedData?.x ?? false;

	if (!selected_x) {
		return;
	}

	if (!props.meta) return;

	let modal = "";

	for (const key in ModalTypes) {
		if (Object.hasOwnProperty.call(ModalTypes, key)) {
			const element = ModalTypes[key];
			if (element.label === props.meta.label) {
				modal = key;
			}
		}
	}

	console.log(modal);

	if (!modal) return;

	let selectedModal = ModalTypes[modal] ?? null;

	if (selectedModal == null) return;

	modalStore.setModal(selectedModal);

	const modalInitData = { ...props.meta };

	if (selectedModal?.has_filter ?? false) {
		modalInitData.filter = selected_x;
	}

	modalStore.setModalIntializationData(modalInitData);
};
</script>

<template>
	<div>
		<div class="w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4">
			<div
				class="flex justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700"
			>
				<div class="flex items-center">
					<div>
						<h5
							class="leading-none text-lg font-bold text-gray-700 dark:text-white pb-1"
						>
							Leads by Source:
						</h5>
					</div>
				</div>
			</div>
			<div class="my-2">
				<Categorization_PieChart
					:chartID="'lead-by-source'"
					:timeline="leadChartStore.getCurrentLeadTimeframe"
					:series="dataSet_series"
					:total="dataSet_total"
					:chart_height="400"
					@dataSelected="loadModal"
				/>
			</div>
		</div>
	</div>
</template>
