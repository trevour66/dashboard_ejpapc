<script setup>
import { ref, reactive, computed } from "vue";
import { randomPalette } from "@/config/chartPalette";
import ExpandIcon from "../ExpandIcon.vue";

const emits = defineEmits(["zoomLevelChanged", "dataSelected"]);

const props = defineProps({
   headers: {
      required: true,
      type: Array,
   },
   rows: {
      required: true,
      type: Array,
   },
});

const computedSeries = computed(() => {
   let chart_series = [{ data: props.series }];

   return chart_series;
});

const chartClicked = (selected_x) => {
   if ((selected_x ?? false) === false) {
      return;
   }

   let selectedData = { x: selected_x };

   if (selectedData === false) {
      return;
   }

   emits("dataSelected", selectedData);
};
</script>

<template>
   <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <table
         class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
      >
         <thead class="text-gray-700 uppercase dark:text-gray-400">
            <tr>
               <th
                  v-for="(header, index) in headers"
                  scope="col"
                  class="font-bold px-6 py-3"
                  :class="{
                     'bg-gray-50': index % 2 !== 1,
                     'bg-white': index % 2 === 1,
                  }"
               >
                  {{ header }}
               </th>
            </tr>
         </thead>
         <tbody>
            <tr v-for="row in rows" class="border-b border-gray-200">
               <th
                  v-for="(col, index) in row"
                  scope="row"
                  class="px-6 py-4 font-medium text-gray-600 whitespace-nowrap"
                  :class="{
                     'bg-gray-50': index % 2 !== 1,
                     'bg-white': index % 2 === 1,
                  }"
               >
                  <div
                     class="inline-flex flex-row gap-x-2 items-center justify-center"
                  >
                     <span>
                        {{ col }}
                     </span>
                     <span v-if="index === 0">
                        <div
                           @click="chartClicked(col)"
                           class="p-1 rounded-lg border-2 shadow-md hover:shadow-sm hover:cursor-pointer"
                        >
                           <ExpandIcon />
                        </div>
                     </span>
                  </div>
               </th>
               <!-- <td class="px-6 py-4">Silver</td> -->
            </tr>
         </tbody>
      </table>
   </div>
</template>
