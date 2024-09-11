<script setup>
import Modal_Structure from "../Modal_Structure.vue";
import { modalViewTypes } from "@/config/modalConfig";
import { computed, onMounted, ref } from "vue";
import {
	getLeadsByStepOrSource,
	getRetainedLeads,
	getNewLeads
} from "@/Services/leadService";

import { useModalStore } from "@/Store/modalStore.js";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import Lead_List_View from "@/Components/Modals/DataContainers/Lead_List_View.vue";

const modalStore = useModalStore();

const moreDataLoading = ref(true);
const initialDataLoading = ref(true);
const initialDataLoaded = ref(false);
const errorLoadingData = ref("");

const dataFromParent = ref("");
const dataUrl = ref("");
const to = ref("");
const from = ref("");
const title = ref("");
const filter = ref("");

const dataforView = ref([]);
const data_container = ref(null);

const fetchData = async (isInitial = true) => {
	try {
		if (isInitial) {
			initialDataLoading.value = true;
		} else {
			moreDataLoading.value = true;
		}

		errorLoadingData.value = "";

		let res = null;
		let success = false;

		// console.log(title.value);

		switch (title.value) {
			case "Leads by Step":
				res = await getLeadsByStepOrSource(
					dataUrl.value,
					to.value,
					from.value,
					filter.value
				);
				success = res.success ?? false;
				break;
			case "Leads by Source":
				res = await getLeadsByStepOrSource(
					dataUrl.value,
					to.value,
					from.value,
					filter.value
				);
				success = res.success ?? false;
				break;
			case "Retained Leads":
				res = await getRetainedLeads(dataUrl.value, to.value, from.value);
				success = res.success ?? false;
				break;
			case "New Leads":
				res = await getNewLeads(dataUrl.value, to.value, from.value);
				success = res.success ?? false;
				break;

				


			default:
				break;
		}

		if (success) {
			const fetchedData = res?.fetchedData ?? [];
			dataUrl.value = res?.next_page_url ?? false;

			if (fetchedData && fetchedData.length > 0) {
				Array.prototype.push.apply(dataforView.value, fetchedData);
			}
		} else {
			errorLoadingData.value = "Error fetching data";
		}

		if (isInitial) {
			initialDataLoading.value = false;
		} else {
			moreDataLoading.value = false;
		}
	} catch (error) {
		if (isInitial) {
			initialDataLoading.value = false;
		} else {
			moreDataLoading.value = false;
		}

		errorLoadingData.value = error.message;
	}
};

const getTitle = computed(() => {
	if ((title.value ?? "") != "" && (filter.value ?? "") != "") {
		return `${title.value} - ${filter.value}`;
	}

	if ((title.value ?? "") != "") {
		return `${title.value}`;
	}

	return modalStore?.getCurrentModal?.category ?? "";
});

const handleInfiniteScroll = () => {
	if (!(dataUrl.value ?? false)) {
		return;
	}
	const endOfContainer =
		data_container.value.scrollHeight - data_container.value.scrollTop ===
		data_container.value.clientHeight;

	if (endOfContainer) {
		fetchData(false);
	}
};

onMounted(async () => {
	let initData = modalStore.getCurrentModalIntializationData;

	// console.log(initData);

	dataFromParent.value = initData?.data ?? "";
	dataUrl.value = initData?.dataUrl ?? "";
	to.value = initData?.date_used?.to ?? "";
	from.value = initData?.date_used?.from ?? "";
	title.value = initData?.label ?? "";
	filter.value = initData?.filter ?? "";

	await fetchData();

	if (!initialDataLoading.value && data_container.value != null) {
		// console.log(data_container);
		data_container.value.addEventListener("scroll", handleInfiniteScroll);
	}
});
</script>

<template>
	<Modal_Structure>
		<template #title>
			<span>{{ getTitle }}</span>
		</template>
		<template #content>
			<template v-if="initialDataLoading">
				<div class="flex items-center justify-center w-full h-32">
					<InfinityScrollLoader />
				</div>
			</template>
			<template v-else>
				<section
					class="h-full max-h-[500px] overflow-y-auto grid md:grid-cols-2 gap-4 gap-x-10 grid-cols-1"
					ref="data_container"
				>
					<template v-if="(dataforView ?? []).length > 0">
						<div v-for="(data, index) in dataforView" :key="index">
							<Lead_List_View
								:lead_data="data"
							/>
						</div>
						<!-- {{ dataforView }} -->
					</template>
					<template v-else></template>
				</section>
			</template>
		</template>
	</Modal_Structure>
</template>
