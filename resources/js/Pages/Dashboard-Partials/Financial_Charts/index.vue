<script setup>
import { ref, reactive, watchEffect, computed, onMounted, watch } from "vue";
import { useLeadChartsStore } from "../../../Store/leadCharts";
import { timeframeButtons } from "@/config/timeframe";
import { ModalTypes } from "@/config/modalConfig";
import DataCard from "@/Components/DataCard.vue";
import feesByAtty from "./feesByAtty.vue";

import { getGeneralFinanceData } from "@/Services/financeService";

import { useModalStore } from "@/Store/modalStore.js";

const modalStore = useModalStore();

const leadChartStore = useLeadChartsStore();
const loadingLeadCategorization = ref(true);

const loadingFeesByResponsibleAtty = ref(true);
const errorLoadingFeesByResponsibleAtty = ref(false);

const dataCard_Data = reactive({
	anticipate_funds: {
		data: "__",
		label: "Anticipated Funds",
		meta: {
			label: "Anticipated Funds",
			dataUrl: route("finance.getAnticipatedFunds"),
			data: null,
			date_used: null,
		},
	},
	atty_fees_collected: {
		data: "__",
		label: "Atty Fees Collected",
		meta: {
			label: "Atty Fees Collected",
			dataUrl: route("finance.getAttyFeesCollected"),
			data: null,
			date_used: null,
		},
	},
	average_atty_fees_collected: {
		data: "__",
		label: "Atty Fees Collected(average)",
		meta: {
			label: "Atty Fees Collected(average)",
			dataUrl: route("finance.getAttyFeesCollected"),
			data: null,
			date_used: null,
		},
	},
});

const feesByResponsibleAtty = reactive({
	data: [],
	label: "Fees by Attorney",
	meta: {
		label: "Fees by Attorney",
		dataUrl: route("finance.getFeesByResponsibleAtty"),
		data: null,
		date_used: null,
	},
});

const formatCurrency = (amount) => {
	if ((amount ?? "") == "" || (amount ?? "__") == "__") {
		return "__";
	}
	return amount.toLocaleString("en-US", { style: "currency", currency: "USD" });
};

const getData = async () => {
	loadingLeadCategorization.value = true;
	const toAndFrom = leadChartStore.getFromAndToDatetimeOfCurrentLeadTimeframe;

	const to = toAndFrom?.to ?? false;
	const from = toAndFrom?.from ?? false;

	const res = await getGeneralFinanceData(to, from, "count");

	// console.log(res);

	const anticipate_funds = res?.financial_data?.anticipate_funds ?? null;
	const atty_fees_by_responsible_atty =
		res?.financial_data?.atty_fees_by_responsible_atty ?? null;
	const atty_fees_collected = res?.financial_data?.atty_fees_collected ?? null;
	const average_atty_fees_collected =
		res?.financial_data?.average_atty_fees_collected ?? null;

	parse_atty_fees_by_responsible_atty(atty_fees_by_responsible_atty);
	parse_anticipate_funds(anticipate_funds);
	parse_atty_fees_collected(atty_fees_collected);
	parse_average_atty_fees_collected(average_atty_fees_collected);
};

const parse_average_atty_fees_collected = (average_atty_fees_collected) => {
	try {
		dataCard_Data.average_atty_fees_collected.data =
			average_atty_fees_collected?.data?.count ?? "__";
		dataCard_Data.average_atty_fees_collected.meta.data =
			average_atty_fees_collected?.data ?? null;
		dataCard_Data.average_atty_fees_collected.meta.date_used =
			average_atty_fees_collected?.date_used ?? null;

		dataCard_Data.average_atty_fees_collected.data = formatCurrency(
			dataCard_Data.average_atty_fees_collected.data
		);
	} catch (error) {
		console.log(error.message);
	}
};

const parse_atty_fees_collected = (atty_fees_collected) => {
	try {
		dataCard_Data.atty_fees_collected.data =
			atty_fees_collected?.data?.count ?? "__";
		dataCard_Data.atty_fees_collected.meta.data =
			atty_fees_collected?.data ?? null;
		dataCard_Data.atty_fees_collected.meta.date_used =
			atty_fees_collected?.date_used ?? null;

		dataCard_Data.atty_fees_collected.data = formatCurrency(
			dataCard_Data.atty_fees_collected.data
		);
	} catch (error) {
		console.log(error.message);
	}
};

const parse_anticipate_funds = (anticipate_funds) => {
	try {
		dataCard_Data.anticipate_funds.data = anticipate_funds?.data?.count ?? "__";
		dataCard_Data.anticipate_funds.meta.data = anticipate_funds?.data ?? null;
		dataCard_Data.anticipate_funds.meta.date_used =
			anticipate_funds?.date_used ?? null;

		dataCard_Data.anticipate_funds.data = formatCurrency(
			dataCard_Data.anticipate_funds.data
		);
	} catch (error) {
		console.log(error.message);
	}
};

const parse_atty_fees_by_responsible_atty = async (
	fees_by_responsible_atty
) => {
	loadingFeesByResponsibleAtty.value = true;
	errorLoadingFeesByResponsibleAtty.value = false;

	try {
		feesByResponsibleAtty.data = fees_by_responsible_atty?.data?.count ?? null;
		feesByResponsibleAtty.meta.data = fees_by_responsible_atty?.data ?? null;
		feesByResponsibleAtty.meta.date_used =
			fees_by_responsible_atty?.date_used ?? null;

		if (feesByResponsibleAtty?.data == null || (feesByResponsibleAtty?.data ?? []).length == 0) {
			throw new Error();
		}
	} catch (error) {
		errorLoadingFeesByResponsibleAtty.value = true;
	}

	setTimeout(() => {
		loadingFeesByResponsibleAtty.value = false;
	}, 1000);
};

const fetchData = (avg) => {
	console.log("called");
	// console.log(avg);

	const avgMeta = avg?.meta ?? false;

	// console.log(avgMeta);

	if (!avgMeta) return;

	// console.log(avgMeta);

	let modal = "";

	for (const key in ModalTypes) {
		if (Object.hasOwnProperty.call(ModalTypes, key)) {
			const element = ModalTypes[key];
			if (element.label === avgMeta.label) {
				modal = key;
			}
		}
	}

	if (!modal) return;

	let selectedModal = ModalTypes[modal] ?? null;

	if (selectedModal == null) return;

	const modalInitData = { ...avgMeta };

	// console.log(modalInitData);

	modalStore.setModalIntializationData(modalInitData);

	modalStore.setModal(selectedModal);
};

watchEffect(async () => {
	const toAndFrom = leadChartStore.getFromAndToDatetimeOfCurrentLeadTimeframe;

	const to = toAndFrom?.to ?? false;
	const from = toAndFrom?.from ?? false;

	if (to && from) {
		await getData();
	}
});

onMounted(async () => {
	await getData();
});
</script>

<template>
	<section class="mb-6 grid grid-cols-1 gap-4">
		<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg py-4">
			<div class="p-6 text-gray-900">
				<div class="flex items-center mb-6">
					<div
						class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center me-3"
					>
						<svg
							class="w-8 h-8 text-gray-500"
							viewBox="0 0 512 512"
							id="Layer_1"
							version="1.1"
							xml:space="preserve"
							xmlns="http://www.w3.org/2000/svg"
							xmlns:xlink="http://www.w3.org/1999/xlink"
							fill="#333333"
						>
							<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
							<g
								id="SVGRepo_tracerCarrier"
								stroke-linecap="round"
								stroke-linejoin="round"
							></g>
							<g id="SVGRepo_iconCarrier">
								<g>
									<path
										class="#333333"
										d="M85.9,381c10,7.1,21.3,11.5,33.6,12.8V404c0,3.9,3.3,7.1,7.2,7.1c3.9,0,7.1-3.1,7.1-7.1v-9.8 c20.8-2,34.9-13.9,34.9-32c0-17.6-10.7-27.6-35.6-33.8v-31.5c5.6,1.3,11.2,3.8,16.9,7.2c1.6,1,3.3,1.5,4.9,1.5c5.1,0,9.2-3.9,9.2-9 c0-3.9-2.3-6.4-4.9-7.9c-7.5-4.8-15.7-7.9-25.4-9v-3.6c0-3.9-3.1-7.1-7.1-7.1c-3.9,0-7.2,3.1-7.2,7.1v3.4 c-20.5,1.6-34.4,13.8-34.4,31.3c0,18.5,11.2,27.6,35.1,33.8v32.3c-9-1.6-16.6-5.4-24.3-11c-1.5-1.1-3.4-1.8-5.4-1.8 c-5.1,0-9,3.9-9,9C81.5,376.6,83.2,379.2,85.9,381z M133.2,347.7c12.6,3.9,16.2,8.4,16.2,15.6c0,7.9-5.7,13.1-16.2,14.3V347.7z M104.5,309.7c0-7.2,5.2-12.8,15.7-13.8v29C107.6,320.8,104.5,316.6,104.5,309.7z"
									></path>
									<path
										class="#333333"
										d="M49.4,418.2c20.4,19.8,47.3,30.7,75.8,30.7c30.1,0,58.9-12.5,79.4-34.4h171.7c9.9,0,17.9-8,17.9-17.9v-45.7 c0-4.1-1.4-7.9-3.7-10.9c2.3-3,3.7-6.8,3.7-10.9v-45.7c0-1.3-0.2-2.6-0.4-3.9h52c9.6,0,17.3-7.8,17.3-17.3v-46.8 c0-1.1-0.1-2.3-0.3-3.3h15.7c9.6,0,17.3-7.8,17.3-17.3v-46.8c0-9.6-7.8-17.3-17.3-17.3h-84.6c0.2-1.1,0.3-2.2,0.3-3.3V80.4 c0-9.6-7.8-17.3-17.3-17.3H156.6c-9.6,0-17.3,7.8-17.3,17.3v46.8c0,9.6,7.8,17.3,17.3,17.3h84.6c-0.2,1.1-0.3,2.2-0.3,3.3v46.8 c0,1.1,0.1,2.3,0.3,3.3h-15.7c-9.6,0-17.3,7.8-17.3,17.3v46.8c0,1.1,0.1,2.3,0.3,3.3h-3.9c-20.5-21.9-49.3-34.4-79.4-34.4 c-15.6,0-30.6,3.2-44.8,9.6c-38.9,17.6-64.1,56.6-64.1,99.3c0,1.4,0,2.5,0.1,3.6C17.3,371.9,29,398.4,49.4,418.2z M380.1,396.6 c0,2.1-1.8,3.9-3.9,3.9H215.7c0,0,0,0,0-0.1c0.6-0.9,1.1-1.7,1.7-2.6c0.2-0.2,0.3-0.5,0.5-0.7c0.5-0.7,0.9-1.5,1.3-2.2 c0.1-0.2,0.2-0.4,0.3-0.5c0.5-0.9,1-1.8,1.5-2.7c0.1-0.2,0.2-0.5,0.4-0.7c0.4-0.8,0.8-1.5,1.2-2.3c0.1-0.2,0.2-0.4,0.3-0.7 c0.5-0.9,0.9-1.9,1.3-2.8c0.1-0.2,0.2-0.4,0.3-0.6c0.4-0.8,0.7-1.6,1-2.4c0.1-0.2,0.2-0.5,0.3-0.7c0.4-1,0.8-1.9,1.2-2.9 c0.1-0.1,0.1-0.3,0.2-0.4c0.3-0.9,0.6-1.7,0.9-2.6c0.1-0.3,0.2-0.5,0.3-0.8c0.3-1,0.7-2,1-3c0,0,0-0.1,0-0.1c0.3-1,0.6-2,0.8-3 c0.1-0.3,0.1-0.5,0.2-0.8c0.2-0.9,0.5-1.8,0.7-2.8c0-0.1,0.1-0.2,0.1-0.3c0.2-1,0.5-2.1,0.7-3.1c0.1-0.3,0.1-0.5,0.2-0.8 c0.2-0.9,0.3-1.8,0.5-2.6c0-0.2,0.1-0.4,0.1-0.6c0.2-1,0.3-2.1,0.5-3.1c0-0.2,0.1-0.5,0.1-0.7c0.1-0.9,0.2-1.8,0.3-2.6 c0-0.2,0-0.5,0.1-0.7c0.1-1.1,0.2-2.1,0.3-3.2c0-0.1,0-0.1,0-0.2h142.4c2.2,0,3.9,1.7,3.9,3.9V396.6z M380.1,283.4v45.7 c0,2.1-1.8,3.9-3.9,3.9H233.8c0-0.1,0-0.1,0-0.2c-0.1-1.1-0.2-2.1-0.3-3.2c0-0.2,0-0.5-0.1-0.7c-0.1-0.9-0.2-1.8-0.3-2.6 c0-0.2-0.1-0.5-0.1-0.7c-0.1-1-0.3-2.1-0.5-3.1c0-0.2-0.1-0.4-0.1-0.6c-0.1-0.9-0.3-1.8-0.5-2.6c-0.1-0.3-0.1-0.5-0.2-0.8 c-0.2-1-0.4-2.1-0.7-3.1c0-0.1-0.1-0.2-0.1-0.3c-0.2-0.9-0.4-1.8-0.7-2.8c-0.1-0.3-0.1-0.5-0.2-0.8c-0.3-1-0.6-2-0.9-3c0,0,0,0,0,0 c-0.3-1-0.6-2-1-3c-0.1-0.3-0.2-0.5-0.3-0.8c-0.3-0.9-0.6-1.7-0.9-2.6c-0.1-0.1-0.1-0.3-0.1-0.4c-0.4-1-0.8-2-1.2-2.9 c-0.1-0.2-0.2-0.5-0.3-0.7c-0.3-0.8-0.7-1.6-1.1-2.4c-0.1-0.2-0.2-0.4-0.3-0.6c-0.4-1-0.9-1.9-1.3-2.9c-0.1-0.2-0.2-0.4-0.3-0.6 c-0.4-0.8-0.8-1.6-1.2-2.4c-0.1-0.2-0.2-0.4-0.3-0.6c-0.5-0.9-1-1.9-1.5-2.8c-0.1-0.1-0.2-0.3-0.3-0.4c-0.5-0.8-0.9-1.6-1.4-2.4 c-0.1-0.2-0.3-0.4-0.4-0.7c-0.6-0.9-1.1-1.8-1.7-2.7c0,0,0,0,0,0h9.8h150.8C378.4,279.5,380.1,281.3,380.1,283.4z M153.2,127.3 V80.4c0-1.8,1.5-3.3,3.3-3.3h220.2c1.8,0,3.3,1.5,3.3,3.3v46.8c0,1.8-1.5,3.3-3.3,3.3H258.1H156.6 C154.7,130.6,153.2,129.1,153.2,127.3z M254.8,194.7v-46.8c0-1.8,1.5-3.3,3.3-3.3h118.7h101.6c1.8,0,3.3,1.5,3.3,3.3v46.8 c0,1.8-1.5,3.3-3.3,3.3h-32.7H258.1C256.3,198.1,254.8,196.6,254.8,194.7z M222.1,262.2v-46.8c0-1.8,1.5-3.3,3.3-3.3h32.7h187.6 c1.8,0,3.3,1.5,3.3,3.3v46.8c0,1.8-1.5,3.3-3.3,3.3h-69.5H225.5C223.6,265.5,222.1,264,222.1,262.2z M86.2,253.5 c12.3-5.5,25.4-8.4,39-8.4c27.2,0,53.1,11.7,71.1,32c15.3,17.3,23.8,39.6,23.8,62.8c0,2.9-0.1,5.8-0.4,8.6 c-0.1,1.6-0.4,3.2-0.6,4.9c-2.6,18.2-10.5,35.4-22.8,49.3c-18,20.4-43.9,32-71.1,32c-51.5,0-93.2-40.3-94.8-91.8c0-1,0-2,0-3.1 C30.3,302.8,52.2,268.8,86.2,253.5z"
									></path>
								</g>
							</g>
						</svg>
					</div>
					<div>
						<h5
							class="leading-none text-2xl font-bold text-gray-900 dark:text-white pb-1"
						>
							Finances
						</h5>
						<p class="text-sm font-normal text-gray-500 dark:text-gray-400">
							Funds received; Expected settlement funds; Attorney fees
							categorized by received date and expected date.
						</p>
					</div>
				</div>

				<div class="flex flex-col gap-6">
					<div>
						<div class="flex flex-row gap-6">
							<DataCard
								@open-modal="fetchData(avg)"
								v-for="(avg, index) in dataCard_Data"
								:key="index"
								:meta="avg?.meta ?? null"
								:avg="avg ?? null"
							>
								<template v-slot:header>
									<span>{{ avg.label }}</span>
								</template>
								<template v-slot:content>
									<span>{{ avg.data }}</span>
								</template>
							</DataCard>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-4">
			<div
				v-if="
					!loadingFeesByResponsibleAtty && !errorLoadingFeesByResponsibleAtty
				"
				class="relative min-h-[550px]"
			>
				<feesByAtty
					:dataSet="feesByResponsibleAtty.data"
					:meta="feesByResponsibleAtty.meta"
				/>
			</div>
		</div>
	</section>
</template>
