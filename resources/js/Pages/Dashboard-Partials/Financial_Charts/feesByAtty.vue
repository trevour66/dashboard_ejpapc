<script setup>
import { ref, reactive, watchEffect, computed, onMounted } from "vue";
import Categorization_TreeChart from "../../../Components/Charts/Categorization_Chart.vue";
import { useLeadChartsStore } from "../../../Store/leadCharts";

import { ModalTypes } from "@/config/modalConfig";
import { useModalStore } from "@/Store/modalStore.js";

const props = defineProps({
	dataSet: {
		required: true,
		type: Object,
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
	if ((Object.keys(props.dataSet) ?? []).length > 0) {
		for (const key in props.dataSet) {
			// console.log(key);
			if (Object.hasOwnProperty.call(props.dataSet, key)) {
				const element = props.dataSet[key];

				dataSet_labels.value.push(key);
				dataSet_series.value.push({
					x: key,
					y: element,
				});

				dataSet_total.value = dataSet_total.value + element;
			}
		}
	}
});

const loadModal = (selectedData) => {
	console.log(selectedData);

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
							Fees by Attorney:
						</h5>
					</div>
				</div>
			</div>
			<div class="my-2">
				<Categorization_TreeChart
					:chartID="'fees-by-atty'"
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
