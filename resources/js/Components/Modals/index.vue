<script setup>
import MattersListView from "./Views/MattersListView.vue";
import LeadsListView from "./Views/LeadsListView.vue";
import CustomDateRangeFormView from "./Views/CustomDateRangeFormView.vue";

import { modalViewTypes } from "@/config/modalConfig";
import { useModalStore } from "@/Store/modalStore.js";

const modalStore = useModalStore();
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
            modalStore.getCurrentModal.view_type ==
            modalViewTypes.matters_list_view
         "
      />
      <LeadsListView
         v-if="
            modalStore.getCurrentModal.view_type ==
            modalViewTypes.leads_list_view
         "
      />
      <CustomDateRangeFormView
         v-if="
            modalStore.getCurrentModal.view_type ==
            modalViewTypes.custom_date_range_form_view
         "
      />
   </template>
</template>
