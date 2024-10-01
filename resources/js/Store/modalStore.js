import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { ModalTypes } from "@/config/modalConfig";

export const useModalStore = defineStore("modalStore", () => {
	const currentModal = ref(ModalTypes.none);
	const currentModalIntializationData = ref(null);

	const getCurrentModal = computed(() => {
		return currentModal.value;
	});

	const getCurrentModalIntializationData = computed(() => {
		return currentModalIntializationData.value;
	});

	const setModal = (modal) => {
		currentModal.value = modal;
	};

	const setCustomDateRangeModal = () => {
		currentModal.value = ModalTypes.custom_date_range_form_view
	}

	const setModalIntializationData = (modalIntializationData) => {
		currentModalIntializationData.value = modalIntializationData;
	};

	const closeModal = () => {
		currentModal.value = ModalTypes.none;
	};

	return {
		getCurrentModal,
		getCurrentModalIntializationData,
		setModal,
		setCustomDateRangeModal,
		setModalIntializationData,
		closeModal,
	};
});
