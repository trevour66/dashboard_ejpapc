<script setup>
import { goto_matter_actionStep } from "@/Services/caseService";
import { onMounted, ref } from "vue";

import IdIcon from "@/Components/IdIcon.vue";
import CalendarIcon from "@/Components/CalendarIcon.vue";
import DuedIcon from "@/Components/DuedIcon.vue";
import OfferTagIcon from "@/Components/OfferTagIcon.vue";
import CoinsIcon from "@/Components/CoinsIcon.vue";
import Matter_Leads_LinkCopier from "@/Components/Matter_Leads_LinkCopier.vue";

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
   matter_data: {
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

const formatCurrency = (amount) => {
   if ((amount ?? "") == "") return;
   return amount.toLocaleString("en-US", {
      style: "currency",
      currency: "USD",
   });
};

onMounted(() => {});
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
               {{ matter_data?.matter_current_name ?? "" }}

               <Matter_Leads_LinkCopier
                  v-if="(matter_data?.matter_actionstep_id ?? '') != ''"
                  :entityId="String(matter_data?.matter_actionstep_id ?? '')"
               />
            </div>
            <div class="inline-flex items-center w-full gap-x-3">
               <div
                  v-if="(matter_data?.matter_actionstep_id ?? '') != ''"
                  class="flex items-center gap-x-1"
               >
                  <IdIcon tooltip_string="Matter ID" />
                  <p class="text-sm">
                     {{ matter_data?.matter_actionstep_id ?? "" }}
                  </p>
               </div>
               <div
                  v-if="(matter_data?.matter_date_created ?? '') != ''"
                  class="flex items-center gap-x-1"
               >
                  <CalendarIcon tooltip_string="Matter created date" />
                  <p class="text-sm">
                     {{ parse_date(matter_data?.matter_date_created ?? "") }}
                  </p>
               </div>
               <div
                  v-if="(matter_data?.matter_next_task_due_date ?? '') != ''"
                  class="flex items-center gap-x-1"
               >
                  <DuedIcon tooltip_string="Next Due Date" />
                  <p class="text-sm">
                     {{
                        parse_date(matter_data?.matter_next_task_due_date ?? "")
                     }}
                  </p>
               </div>
               <div
                  v-if="(matter_data?.matter_current_offer ?? '') != ''"
                  class="flex items-center gap-x-1"
               >
                  <OfferTagIcon tooltip_string="Matter current offer" />
                  <p class="text-sm">
                     {{
                        formatCurrency(matter_data?.matter_current_offer ?? "")
                     }}
                  </p>
               </div>
               <div
                  v-if="(matter_data?.matter_atty_fees ?? '') != ''"
                  class="flex items-center gap-x-1"
               >
                  <CoinsIcon tooltip_string="Attorney fees" />
                  <p class="text-sm">
                     {{ formatCurrency(matter_data?.matter_atty_fees ?? "") }}
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
         <div
            class="flex flex-col gap-y-1 text-base font-bold text-gray-700 p-2 border rounded-lg"
         >
            <div>
               <span
                  class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-medium text-gray-500 bg-gray-200 rounded"
                  >Last Filenote</span
               >
            </div>
            <div class="flex-1 ms-2 break-all">
               <p
                  class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2 max-h-[100px] overflow-y-auto break-all"
               >
                  {{ matter_data?.matter_last_filenote ?? "" }}
               </p>
            </div>
         </div>

         <div class="grid grid-cols-3 gap-2">
            <div
               v-if="(matter_data?.current_step?.step_name ?? '') != ''"
               class="flex flex-col gap-y-1 text-base font-bold text-gray-700 p-2 border rounded-lg"
            >
               <div>
                  <span
                     class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-medium text-gray-500 bg-gray-200 rounded"
                     >Current Step</span
                  >
               </div>
               <div class="flex-1 ms-2 break-all">
                  <p
                     class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2 max-h-[100px] overflow-y-auto break-all"
                  >
                     {{ matter_data?.current_step?.step_name ?? "" }}
                  </p>
               </div>
            </div>

            <div
               v-if="
                  (matter_data?.current_matter_attorney?.ASALA_name ?? '') != ''
               "
               class="flex flex-col gap-y-1 text-base font-bold text-gray-700 p-2 border rounded-lg"
            >
               <div>
                  <span
                     class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-medium text-gray-500 bg-gray-200 rounded"
                     >Attorney</span
                  >
               </div>
               <div class="flex-1 ms-2 break-all">
                  <p
                     class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2 max-h-[100px] overflow-y-auto break-all"
                  >
                     {{
                        matter_data?.current_matter_attorney?.ASALA_name ?? ""
                     }}
                  </p>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>
