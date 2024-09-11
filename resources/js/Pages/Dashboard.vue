<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ModalWrapper from "@/Components/Modals/index.vue";
import {
	ref,
	reactive,
	watchEffect,
	computed,
	onMounted,
	watch,
	onBeforeUnmount,
} from "vue";
import { timeframeButtons } from "@/config/timeframe";
import { Head } from "@inertiajs/vue3";

import LeadsChart from "./Dashboard-Partials/Leads_Charts/index.vue";
import Financial_Charts from "@/Pages/Dashboard-Partials/Financial_Charts/index.vue";
import Cases_Charts from "@/Pages/Dashboard-Partials/Cases_Charts/index.vue";

import { useLeadChartsStore } from "@/Store/leadCharts";

const props = defineProps({
	leads: Object,
});

const leadChartStore = useLeadChartsStore();

const isFixed = ref(false);
const toolbar = ref(null);
const toolbarOffsetTop = ref(0);

const updateTimeframe = async (timeframe) => {
	leadChartStore.setLeadTimeframe(timeframe);
};

const handleScroll = () => {
	const scrollPosition = window.scrollY;
	isFixed.value = scrollPosition >= toolbarOffsetTop.value;
};

onMounted(() => {
	const toolbarVal = toolbar.value;
	toolbarOffsetTop.value = toolbarVal.offsetTop;
	window.addEventListener("scroll", handleScroll);
});
onBeforeUnmount(() => {
	window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
	<Head title="Dashboard" />

	<AuthenticatedLayout>
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">
				Dashboard
			</h2>
		</template>

		<ModalWrapper />

		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
				<section
					ref="toolbar"
					:class="{
						'fixed top-2 right-0 md:right-5 p-2 md:p-4 bg-gray-200 z-[1000]': isFixed,
					}"
					class="mb-4 rounded"
				>
					<div class="flex flex-col gap-6">
						<div>
							<div class="toolbar float-right">
								<div class="inline-flex rounded-md shadow-sm" role="group">
									<button
										v-for="(button, index) in timeframeButtons"
										:key="index"
										@click="updateTimeframe(button.data)"
										:class="{
											'bg-gray-200':
												leadChartStore.getCurrentLeadTimeframe == button.data,
											'bg-white':
												leadChartStore.getCurrentLeadTimeframe != button.data,
											'rounded-s-lg': index === 0,
											'rounded-e-lg': index === timeframeButtons.length - 1,
										}"
										class="px-1.5 md:px-4 py-1 md:py-2 text-sm font-medium text-gray-900 border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10"
									>
										{{ button.name }}
									</button>
								</div>
							</div>
							<div class="clearboth"></div>
						</div>
					</div>
				</section>
				<div class="flex flex-col gap-10 relative">
					<section>
						<LeadsChart :leads="leads" />
					</section>
					<section>
						<Financial_Charts />
					</section>
					<section>
						<Cases_Charts />
					</section>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
