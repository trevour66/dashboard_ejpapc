<script setup>
import { ref, reactive, watchEffect, computed, onMounted, watch } from "vue";
import { useLeadChartsStore } from "../../../Store/leadCharts";
import { timeframeButtons } from "@/config/timeframe";
import NewLeads from "./NewLeads.vue";
import RetainedLeads from "./RetainedLeads.vue";
import LeadByStep from "./LeadByStep.vue";
import LeadBySource from "./LeadBySource.vue";
import DataCard from "@/Components/DataCard.vue";

import {
   getLeadByStepAndSource_dashboard,
   getAverageData,
} from "@/Services/leadService";

const props = defineProps({
   leads: {
      required: true,
      type: Object,
   },
});

const leadChartStore = useLeadChartsStore();
const loadingLeadCategorization = ref(true);
const loadingAverages = ref(true);

const loadingLeadsByStep = ref(true);
const errorLoadingLeadsByStep = ref(false);

const loadingLeadsBySource = ref(true);
const errorLoadingLeadsBySource = ref(false);

const leadsByStep = reactive({
   data: [],
   label: "Leads by Step",
   meta: {
      label: "Leads by Step",
      dataUrl: route("leads.getLeadsByStep"),
      data: {
         count: null,
      },
      date_used: {
         to: null,
         from: null,
      },
   },
});

const leadsBySource = reactive({
   data: [],
   label: "Leads by Source",
   meta: {
      label: "Leads by Source",
      dataUrl: route("leads.getLeadsBySource"),
      data: {
         count: null,
      },
      date_used: {
         to: null,
         from: null,
      },
   },
});

const averagesOutput = reactive({
   lead_is_open: {
      data: "__",
      label: "New lead stays open",
   },
   newLead_to_retained: {
      data: "__",
      label: "New lead to Retained",
   },
   newLead_to_intake: {
      data: "__",
      label: "New lead to Intake",
   },
   newLead_to_consultatn_scheduled: {
      data: "__",
      label: "New lead to Consultation Scheduled",
   },
   newLead_to_retainer_meeting: {
      data: "__",
      label: "New lead to Retainer Meeting",
   },
});

const parse_new_leads = computed(() => {
   const new_leads = {
      data: [],
      label: "New Leads",
   };

   const new_leads_data = [];

   for (const new_lead_timestamp of Object.keys(props.leads.new_leads)) {
      new_leads_data.push([
         Number(new_lead_timestamp),
         props.leads.new_leads[new_lead_timestamp],
      ]);
   }

   new_leads.data = {
      name: "New leads",
      data: new_leads_data,
   };

   return new_leads;
});

const parse_retained_leads = computed(() => {
   const retained_leads = {
      data: [],
      label: "Retained Leads",
   };

   const retained_leads_data = [];

   for (const retained_lead_timestamp of Object.keys(
      props.leads.retained_leads
   )) {
      retained_leads_data.push([
         Number(retained_lead_timestamp),
         props.leads.retained_leads[retained_lead_timestamp],
      ]);
   }

   retained_leads.data = {
      name: "Retained leads",
      data: retained_leads_data,
   };

   return retained_leads;
});

const parse_leads_by_step_and_source = async () => {
   loadingLeadCategorization.value = true;
   const toAndFrom = leadChartStore.getFromAndToDatetimeOfCurrentLeadTimeframe;

   const to = toAndFrom?.to ?? false;
   const from = toAndFrom?.from ?? false;

   const res = await getLeadByStepAndSource_dashboard(to, from, "count");

   await parse_leads_by_step(res?.step ?? [], to, from);
   await parse_leads_by_source(res?.source ?? [], to, from);

   loadingLeadCategorization.value = false;
};

const parse_leads_by_source = async (leads_by_step, to, from) => {
   loadingLeadsBySource.value = true;
   errorLoadingLeadsBySource.value = false;

   try {
      leadsBySource.data = leads_by_step ?? [];
      leadsBySource.meta.data.count = leads_by_step ?? [];
      leadsBySource.meta.date_used.to = to ?? null;
      leadsBySource.meta.date_used.from = from ?? null;

      if ((leadsBySource?.data ?? []).length == 0) {
         throw new Error();
      }
   } catch (error) {
      errorLoadingLeadsBySource.value = true;
   }

   setTimeout(() => {
      loadingLeadsBySource.value = false;
   }, 1000);
};

const parse_leads_by_step = async (leads_by_step, to, from) => {
   loadingLeadsByStep.value = true;
   errorLoadingLeadsByStep.value = false;

   try {
      leadsByStep.data = leads_by_step ?? [];
      leadsByStep.meta.data.count = leads_by_step ?? [];
      leadsByStep.meta.date_used.to = to ?? null;
      leadsByStep.meta.date_used.from = from ?? null;

      if ((leadsByStep?.data ?? []).length == 0) {
         throw new Error();
      }
   } catch (error) {
      errorLoadingLeadsByStep.value = true;
   }

   setTimeout(() => {
      loadingLeadsByStep.value = false;
   }, 1000);
};

const parse_averages = async () => {
   loadingAverages.value = true;
   const toAndFrom = leadChartStore.getFromAndToDatetimeOfCurrentLeadTimeframe;

   const to = toAndFrom?.to ?? false;
   const from = toAndFrom?.from ?? false;

   const res = await getAverageData(to, from, "count");

   // console.log(res);

   const averages = res?.averages ?? false;

   if (!averages) return;

   const newLead_to_retained = averages?.newLead_to_retained ?? false;
   const newLead_to_intake = averages?.newLead_to_intake_on_leadStatus ?? false;
   const newLead_to_consultatn_scheduled =
      averages?.newLead_to_consultation_scheduled_on_leadStatus ?? false;
   const newLead_to_retainer_meeting =
      averages?.newLead_to_retainer_meeting_on_consultation_change_log ?? false;
   const lead_is_open = averages?.lead_is_open ?? false;

   if (newLead_to_retained && newLead_to_retained !== "undefined") {
      averagesOutput.newLead_to_retained.data = newLead_to_retained;
   } else {
      averagesOutput.newLead_to_retained.data = "__";
   }

   if (newLead_to_intake && newLead_to_intake !== "undefined") {
      averagesOutput.newLead_to_intake.data = newLead_to_intake;
   } else {
      averagesOutput.newLead_to_intake.data = "__";
   }

   if (
      newLead_to_consultatn_scheduled &&
      newLead_to_consultatn_scheduled !== "undefined"
   ) {
      averagesOutput.newLead_to_consultatn_scheduled.data =
         newLead_to_consultatn_scheduled;
   } else {
      averagesOutput.newLead_to_consultatn_scheduled.data = "__";
   }

   if (
      newLead_to_retainer_meeting &&
      newLead_to_retainer_meeting !== "undefined"
   ) {
      averagesOutput.newLead_to_retainer_meeting.data =
         newLead_to_retainer_meeting;
   } else {
      averagesOutput.newLead_to_retainer_meeting.data = "__";
   }

   if (lead_is_open && lead_is_open !== "undefined") {
      averagesOutput.lead_is_open.data = lead_is_open;
   } else {
      averagesOutput.lead_is_open.data = "__";
   }

   loadingAverages.value = false;
};

watchEffect(async () => {
   const toAndFrom = leadChartStore.getFromAndToDatetimeOfCurrentLeadTimeframe;

   const to = toAndFrom?.to ?? false;
   const from = toAndFrom?.from ?? false;

   if (to && from) {
      await parse_leads_by_step_and_source();
      await parse_averages();
   }
});

onMounted(async () => {
   await parse_leads_by_step_and_source();
   await parse_averages();
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
                     class="w-6 h-6 text-gray-500 dark:text-gray-400"
                     aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg"
                     fill="currentColor"
                     viewBox="0 0 20 19"
                  >
                     <path
                        d="M14.5 0A3.987 3.987 0 0 0 11 2.1a4.977 4.977 0 0 1 3.9 5.858A3.989 3.989 0 0 0 14.5 0ZM9 13h2a4 4 0 0 1 4 4v2H5v-2a4 4 0 0 1 4-4Z"
                     />
                     <path
                        d="M5 19h10v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2ZM5 7a5.008 5.008 0 0 1 4-4.9 3.988 3.988 0 1 0-3.9 5.859A4.974 4.974 0 0 1 5 7Zm5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm5-1h-.424a5.016 5.016 0 0 1-1.942 2.232A6.007 6.007 0 0 1 17 17h2a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5ZM5.424 9H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h2a6.007 6.007 0 0 1 4.366-5.768A5.016 5.016 0 0 1 5.424 9Z"
                     />
                  </svg>
               </div>
               <div>
                  <h5
                     class="leading-none text-2xl font-bold text-gray-900 dark:text-white pb-1"
                  >
                     Leads
                  </h5>
                  <p
                     class="text-sm font-normal text-gray-500 dark:text-gray-400"
                  >
                     New leads; Retained leads; Leads categorized by current
                     step as well as source
                  </p>
               </div>
            </div>

            <div class="flex flex-col gap-6 max-w-full overflow-x-auto py-4">
               <div>
                  <div class="flex flex-row gap-6">
                     <DataCard
                        v-for="(avg, index) in averagesOutput"
                        :key="index"
                     >
                        <template v-slot:header>
                           <span> Avg. time </span> <br />
                           <span>{{ avg.label }}</span>
                        </template>
                        <template v-slot:content>
                           <span>{{
                              (avg.data ?? "__") != "__"
                                 ? `${avg.data} day(s)`
                                 : avg.data
                           }}</span>
                        </template>
                     </DataCard>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
         <NewLeads :dataSet="parse_new_leads.data" />
         <RetainedLeads :dataSet="parse_retained_leads.data" />
      </div>

      <div
         class="grid grid-cols-1 gap-4"
         v-if="!loadingLeadsByStep && !errorLoadingLeadsByStep"
      >
         <div class="relative min-h-[150px]">
            <LeadByStep :dataSet="leadsByStep.data" :meta="leadsByStep.meta" />
         </div>
      </div>
      <div
         class="grid grid-cols-1 gap-4"
         v-if="!loadingLeadsBySource && !errorLoadingLeadsBySource"
      >
         <div class="relative min-h-[150px]">
            <LeadBySource
               :dataSet="leadsBySource.data"
               :meta="leadsBySource.meta"
            />
         </div>
      </div>
   </section>
</template>
