<script setup>
import { ref, reactive, watchEffect, computed, onMounted, watch } from "vue";
import { useLeadChartsStore } from "../../../Store/leadCharts";
import { ModalTypes } from "@/config/modalConfig";
import DataCard from "@/Components/DataCard.vue";
import { getCasesStartupData_dashboard } from "@/Services/caseService";
import { useModalStore } from "@/Store/modalStore.js";
import CasesByStep from "./CasesByStep.vue";
import CasesByAtty from "./CasesByAtty.vue";

const modalStore = useModalStore();
const leadChartStore = useLeadChartsStore();
const loadingCasesByStep = ref(true);
const errorLoadingCasesByStep = ref(false);

const loadingCasesByAtty = ref(true);
const errorLoadingCasesByAtty = ref(false);

const casesBySteps = reactive({
	data: [],
	label: "Cases by Step",
	meta: {
		label: "Cases by Step",
		dataUrl: route("cases.getCasesByStep"),
		data: null,
		date_used: null,
	},
});

const casesByAtty = reactive({
	data: [],
	label: "Cases by Attorney",
	meta: {
		label: "Cases by Attorney",
		dataUrl: route("cases.getCasesByAtty"),
		data: null,
		date_used: null,
	},
});

const dataCard_Data = reactive({
	open_cases: {
		data: "__",
		label: "Open Cases",
		meta: {
			label: "Open Cases",
			dataUrl: route("cases.getOpenCases"),
			data: null,
			date_used: null,
		},
	},
	last_accessed_within_ten_days: {
		data: "__",
		label: "Last Accessed within 10 Days",
		meta: {
			label: "Last Accessed within 10 Days",
			dataUrl: route("cases.getCaseAccessedWithin10Days"),
			data: null,
			date_used: null,
		},
	},
	cases_with_next_step_overdue: {
		data: "__",
		label: "NextStep Overdue",
		meta: {
			label: "NextStep Overdue",
			dataUrl: route("cases.getCasesWithNextStepOverDue"),
			data: null,
			date_used: null,
		},
	},
});

const init_getCasesStartupData_dashboard = async () => {
	const toAndFrom = leadChartStore.getFromAndToDatetimeOfCurrentLeadTimeframe;

	const to = toAndFrom?.to ?? false;
	const from = toAndFrom?.from ?? false;

	const res = (await getCasesStartupData_dashboard(to, from, "count")) ?? false;

	// console.log(res);

	if (!res) {
		return;
	}

	const cases_data = res?.cases_data ?? false;

	if (!cases_data) {
		return;
	}

	// console.log(cases_data.cases_accessed_within_more_than_ten_day_ago);

	const cases_accessed_within_more_than_ten_day_ago =
		cases_data?.cases_accessed_within_more_than_ten_day_ago ?? null;
	const cases_by_atty = cases_data?.cases_by_atty ?? null;
	const cases_by_current_step = cases_data?.cases_by_current_step ?? null;
	const cases_with_next_step_overdue =
		cases_data?.cases_with_next_step_overdue ?? null;
	const open_case = cases_data?.open_case ?? null;

	dataCard_Data.open_cases.data = open_case?.data?.count ?? "__";
	dataCard_Data.open_cases.meta.data = open_case?.data ?? null;
	dataCard_Data.open_cases.meta.date_used = open_case?.date_used ?? null;

	// console.log(cases_by_atty);
	// console.log(cases_by_current_step);
	await parse_cases_by_current_step(cases_by_current_step);
	await parse_cases_by_atty(cases_by_atty);
	parse_cases_accessed_within_more_than_ten_day_ago(
		cases_accessed_within_more_than_ten_day_ago
	);
	parse_cases_with_next_step_overdue(cases_with_next_step_overdue);
};

const parse_cases_with_next_step_overdue = (cases) => {
	try {
		dataCard_Data.cases_with_next_step_overdue.data =
			cases?.data?.count ?? "__";
		dataCard_Data.cases_with_next_step_overdue.meta.data = cases?.data ?? null;
		dataCard_Data.cases_with_next_step_overdue.meta.date_used =
			cases?.date_used ?? null;
	} catch (error) {
		console.log(error.message);
	}
};

const parse_cases_accessed_within_more_than_ten_day_ago = (cases) => {
	try {
		dataCard_Data.last_accessed_within_ten_days.data =
			cases?.data?.count ?? "__";
		dataCard_Data.last_accessed_within_ten_days.meta.data = cases?.data ?? null;
		dataCard_Data.last_accessed_within_ten_days.meta.date_used =
			cases?.date_used ?? null;
	} catch (error) {
		console.log(error.message);
	}
};

const parse_cases_by_atty = async (cases_by_atty) => {
	loadingCasesByAtty.value = true;
	errorLoadingCasesByAtty.value = false;

	try {
		casesByAtty.data = cases_by_atty?.data?.count ?? [];
		casesByAtty.meta.data = cases_by_atty?.data ?? null;
		casesByAtty.meta.date_used = cases_by_atty?.date_used ?? null;

		if ((casesByAtty?.data ?? []).length == 0) {
			throw new Error();
		}
	} catch (error) {
		errorLoadingCasesByAtty.value = true;
	}

	setTimeout(() => {
		loadingCasesByAtty.value = false;
	}, 1000);
};

const parse_cases_by_current_step = async (cases_by_current_step) => {
	loadingCasesByStep.value = true;
	errorLoadingCasesByStep.value = false;

	try {
		casesBySteps.data = cases_by_current_step?.data?.count ?? [];
		casesBySteps.meta.data = cases_by_current_step?.data ?? null;
		casesBySteps.meta.date_used = cases_by_current_step?.date_used ?? null;

		if ((casesBySteps?.data ?? []).length == 0) {
			throw new Error();
		}
	} catch (error) {
		errorLoadingCasesByStep.value = true;
	}

	setTimeout(() => {
		loadingCasesByStep.value = false;
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
		await init_getCasesStartupData_dashboard();
	}
});

onMounted(async () => {
	await init_getCasesStartupData_dashboard();
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
							viewBox="0 0 24 24"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
							<g
								id="SVGRepo_tracerCarrier"
								stroke-linecap="round"
								stroke-linejoin="round"
							></g>
							<g id="SVGRepo_iconCarrier">
								<path
									d="M9.1709 4C9.58273 2.83481 10.694 2 12.0002 2C13.3064 2 14.4177 2.83481 14.8295 4"
									stroke="#333333"
									stroke-width="1.5"
									stroke-linecap="round"
								></path>
								<path
									d="M12 22.25C11.5858 22.25 11.25 22.5858 11.25 23C11.25 23.4142 11.5858 23.75 12 23.75V22.25ZM4.31802 21.8284L4.81629 21.2679L4.31802 21.8284ZM19.682 21.8284L19.1837 21.2679L19.682 21.8284ZM7.95476 23.6837C8.36822 23.7087 8.72365 23.3938 8.74863 22.9804C8.77362 22.5669 8.4587 22.2115 8.04524 22.1865L7.95476 23.6837ZM2.25 13C2.25 14.8741 2.24918 16.8307 2.41875 18.4664C2.50368 19.2856 2.63401 20.0548 2.84074 20.7105C3.04411 21.3557 3.3444 21.9665 3.81975 22.389L4.81629 21.2679C4.63262 21.1046 4.43867 20.7904 4.27134 20.2596C4.10738 19.7394 3.99058 19.0818 3.91075 18.3118C3.75082 16.769 3.75 14.8971 3.75 13H2.25ZM12 23.75C14.1024 23.75 15.7464 23.7513 17.0267 23.5983C18.3204 23.4436 19.3568 23.1209 20.1803 22.389L19.1837 21.2679C18.6891 21.7075 18.0058 21.9706 16.8487 22.1089C15.6782 22.2487 14.1402 22.25 12 22.25V23.75ZM8.04524 22.1865C6.33099 22.0829 5.4315 21.8147 4.81629 21.2679L3.81975 22.389C4.83576 23.2921 6.18271 23.5767 7.95476 23.6837L8.04524 22.1865ZM20.2499 13.3873C20.2492 15.2185 20.2371 17.0032 20.0724 18.4673C19.9902 19.1982 19.8728 19.8201 19.7119 20.3117C19.548 20.8126 19.3605 21.1108 19.1837 21.2679L20.1803 22.389C20.6399 21.9804 20.9352 21.3963 21.1375 20.7783C21.3428 20.1509 21.4751 19.4171 21.563 18.635C21.7387 17.0731 21.7492 15.2032 21.7499 13.3879L20.2499 13.3873Z"
									fill="#333333"
								></path>
								<path
									d="M14.6603 15L17.664 14.2166M9.33968 15L3.3324 13.4332C2.7369 13.2779 2.43915 13.2002 2.25021 13.0141C2.21341 12.9778 2.18015 12.939 2.15078 12.8979C2 12.6871 2 12.4168 2 11.8763C2 9.74619 2 8.68113 2.67298 7.96206C2.80233 7.82385 2.94763 7.69753 3.10659 7.58508C3.9337 7 5.15877 7 7.60893 7H16.3911C18.8412 7 20.0663 7 20.8934 7.58508C21.0524 7.69753 21.1977 7.82385 21.327 7.96206C22 8.68113 22 9.74619 22 11.8763C22 12.4168 22 12.6871 21.8492 12.8979C21.8199 12.939 21.7866 12.9778 21.7498 13.0141C21.5999 13.1618 21.3814 13.2412 21 13.3453"
									stroke="#333333"
									stroke-width="1.5"
									stroke-linecap="round"
								></path>
								<path
									d="M14 13.5H10C9.72386 13.5 9.5 13.7239 9.5 14V16.1615C9.5 16.3659 9.62448 16.5498 9.8143 16.6257L10.5144 16.9058C11.4681 17.2872 12.5319 17.2872 13.4856 16.9058L14.1857 16.6257C14.3755 16.5498 14.5 16.3659 14.5 16.1615V14C14.5 13.7239 14.2761 13.5 14 13.5Z"
									stroke="#333333"
									stroke-width="1.5"
									stroke-linecap="round"
								></path>
							</g>
						</svg>
					</div>
					<div>
						<h5
							class="leading-none text-2xl font-bold text-gray-900 dark:text-white pb-1"
						>
							Cases
						</h5>
						<p class="text-sm font-normal text-gray-500 dark:text-gray-400">
							Cases categorized by Current Step (special section for
							Negotiations, Presettled, and Settled); Cases per responsible
							attorney and Last Accessed Date( > 10 days)
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
				v-if="!loadingCasesByStep && !errorLoadingCasesByStep"
				class="relative min-h-[550px]"
			>
				<CasesByStep :dataSet="casesBySteps.data" :meta="casesBySteps.meta" />
			</div>
			<div
				v-if="!loadingCasesByAtty && !errorLoadingCasesByAtty"
				class="relative min-h-[550px]"
			>
				<CasesByAtty :dataSet="casesByAtty.data" :meta="casesByAtty.meta" />
			</div>
		</div>
	</section>
</template>
