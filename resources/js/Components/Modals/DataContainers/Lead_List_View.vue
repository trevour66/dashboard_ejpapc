<script setup>
import { goto_matter_actionStep } from "@/Services/caseService";
import { ref } from "vue";
import CalendarIcon from "@/Components/CalendarIcon.vue";
import Matter_List_View__InLead from "@/Components/Modals/DataContainers/Matter_List_View__InLead.vue";

const props = defineProps({
   general_accordion_id: {
      type: String,
      required: false,
      default: "",
   },
   key_index: {
      type: String,
      required: false,
      default: "",
   },
   lead_data: {
      required: true,
      type: Object,
   },
});

const showInfo = ref(false);

const parse_date = (date) => {
   if ((date ?? "") == "") return;

   const dateObj = new Date(date);

   return dateObj.toLocaleDateString();
};
</script>

<template>
   <h2 :id="`${general_accordion_id}-heading-${key_index}`">
      <button
         @click="showInfo = !showInfo"
         type="button"
         class="flex items-center justify-between w-full p-5 rtl:text-right text-gray-500 border border-gray-200 focus:ring-2 focus:ring-gray-200 hover:bg-gray-100 gap-3"
      >
         <div class="flex flex-col items-center justify-start gap-y-2">
            <div class="font-medium text-left w-full">
               {{ lead_data?.lead_name ?? "" }}
            </div>
            <div class="inline-flex items-center w-full gap-x-3">
               <div
                  v-if="(lead_data?.lead_date_created ?? '') != ''"
                  class="flex items-center gap-x-1"
               >
                  <CalendarIcon tooltip_string="Lead created date" />
                  <p class="text-sm">
                     {{ parse_date(lead_data?.lead_date_created ?? "") }}
                  </p>
               </div>
            </div>
         </div>
         <div>
            <svg
               :class="{
                  'rotate-180': !showInfo,
               }"
               class="w-3 h-3 shrink-0"
               aria-hidden="true"
               xmlns="http://www.w3.org/2000/svg"
               fill="none"
               viewBox="0 0 10 6"
            >
               <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5 5 1 1 5"
               />
            </svg>
         </div>
      </button>
   </h2>
   <div v-if="showInfo">
      <div
         class="p-5 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900 grid grid-cols-1 gap-y-6"
      >
         <div class="grid grid-cols-3 gap-2">
            <div
               v-if="(lead_data?.LS_source ?? '') != ''"
               class="flex flex-col gap-y-1 text-base font-bold text-gray-700 p-2 border rounded-lg"
            >
               <div>
                  <span
                     class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-medium text-gray-500 bg-gray-200 rounded"
                     >Lead Source</span
                  >
               </div>
               <div class="flex-1 ms-2 break-all">
                  <p
                     class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2 max-h-[100px] overflow-y-auto break-all"
                  >
                     {{ lead_data?.LS_source ?? "" }}
                  </p>
               </div>
            </div>
         </div>

         <div class="flex flex-col gap-y-1 text-base font-bold text-gray-700">
            <div>
               <span
                  class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-medium text-gray-500 bg-gray-200 rounded mb-2"
                  >Matters</span
               >
            </div>
            <div class="flex-1 ms-2 break-all">
               <template v-for="(mat, index) in lead_data.matters" :key="index">
                  <Matter_List_View__InLead
                     :matter_data="mat"
                     :key_index="String(index)"
                     :general_accordion_id="general_accordion_id"
               /></template>
            </div>
         </div>
      </div>
   </div>
</template>
