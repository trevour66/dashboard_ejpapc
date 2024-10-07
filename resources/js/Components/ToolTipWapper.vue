<script setup>
const dynamicIDGenerator = () => {
   const characters =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
   let randomString = "";

   for (let i = 0; i < 9; i++) {
      const randomIndex = Math.floor(Math.random() * characters.length);
      randomString += characters[randomIndex];
   }

   return randomString;
};

import { initTooltips } from "flowbite";
import { computed, onMounted } from "vue";

defineProps({
   tooltip_string: {
      type: String,
      required: false,
      default: "",
   },
});

let elemID = dynamicIDGenerator();

onMounted(() => {
   initTooltips();
});
</script>

<template>
   <template v-if="tooltip_string == ''">
      <div>
         <slot name="icon"></slot>
      </div>
   </template>
   <template v-else>
      <div :data-tooltip-target="elemID">
         <slot name="icon"></slot>
      </div>
   </template>

   <div
      v-if="tooltip_string !== ''"
      :id="elemID"
      role="tooltip"
      class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700"
   >
      {{ tooltip_string }}
   </div>
</template>
