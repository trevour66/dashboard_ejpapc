<script setup>
import { onMounted } from "vue";
import MattersListView from "./Views/MattersListView.vue";
import LeadsListView from "./Views/LeadsListView.vue";
import { modalViewTypes } from "@/config/modalConfig";

import { useModalStore } from "@/Store/modalStore.js";

const modalStore = useModalStore();

onMounted(() => {
	// console.log(modalStore.getCurrentModal);
});
</script>

<template>
	<template
		v-if="
			(modalStore?.getCurrentModal ?? false) &&
			(modalStore?.getCurrentModal?.name ?? 'none') !== 'none'
		"
	>
		<MattersListView
			v-if="
				modalStore.getCurrentModal.view_type == modalViewTypes.matters_list_view
			"
		/>
		<LeadsListView
			v-if="
				modalStore.getCurrentModal.view_type == modalViewTypes.leads_list_view
			"
		/>
	</template>
</template>
