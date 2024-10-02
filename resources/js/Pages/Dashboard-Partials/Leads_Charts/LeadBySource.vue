<script setup>
import { ref, reactive, watchEffect, computed, onMounted } from "vue";
import Categorization_TreeChart from "../../../Components/Charts/Categorization_Chart.vue";
import { useLeadChartsStore } from "../../../Store/leadCharts";

import { ModalTypes } from "@/config/modalConfig";
import { useModalStore } from "@/Store/modalStore.js";

import TreeMapIcon from "@/Components/TreeMapIcon.vue";
import ListIcon from "@/Components/ListIcon.vue";

import LeadLists from "@/Components/Lists/LeadLists.vue";
import { viewTypes } from "@/config/listViewTypes";

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

const tableHeaders = ref(["Source", "Count"]);
const tableRows = ref([]);

const activeView = ref(viewTypes.list);

const switchView = (viewType) => {
   activeView.value = viewType;
};

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

            tableRows.value.push([LS_source, lead_count]);

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

   //    console.log(modal);

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
            <div class="flex items-center gap-x-4">
               <div>
                  <h5
                     class="leading-none text-lg font-bold text-gray-700 dark:text-white pb-1"
                  >
                     Leads by Source:
                  </h5>
               </div>
               <div class="flex justify-center items-center gap-x-3">
                  <div
                     @click="switchView(viewTypes.graph)"
                     :class="{
                        'border-black': activeView === viewTypes.graph,
                     }"
                     class="p-1 rounded-lg border-2 shadow-md hover:shadow-sm hover:cursor-pointer"
                  >
                     <TreeMapIcon />
                  </div>
                  <div
                     @click="switchView(viewTypes.list)"
                     :class="{
                        'border-black': activeView === viewTypes.list,
                     }"
                     class="p-1 rounded-lg border-2 shadow-md hover:shadow-sm hover:cursor-pointer"
                  >
                     <ListIcon />
                  </div>
               </div>
            </div>
         </div>

         <template v-if="activeView == viewTypes.graph">
            <div class="my-3">
               <Categorization_TreeChart
                  :chartID="'lead-by-source'"
                  :timeline="leadChartStore.getCurrentLeadTimeframe"
                  :series="dataSet_series"
                  :total="dataSet_total"
                  :chart_height="400"
                  @dataSelected="loadModal"
               />
            </div>
         </template>
         <template v-else>
            <div class="my-3 max-h-[400px] overflow-y-scroll">
               <LeadLists
                  :headers="tableHeaders"
                  :rows="tableRows"
                  @dataSelected="loadModal"
               />
            </div>
         </template>
      </div>
   </div>
</template>
