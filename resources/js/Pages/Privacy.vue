<script setup>
import { Head, Link } from "@inertiajs/vue3";

import Privacy from "@/Components/Privacy.vue";

defineProps({
	canLogin: Boolean,
	canRegister: Boolean,
	laravelVersion: String,
	phpVersion: String,
});
</script>

<template>
	<Head title="Welcome" />

	<section
		class="min-h-screen p-20 bg-dots-darker flex justify-center items-center bg-gray-100"
	>
		<div class="h-full w-full max-w-full">
			<div
				class="relative sm:flex sm:justify-center sm:items-center bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-[#982069] selection:text-white h-full"
			>
				<Privacy />
			</div>
		</div>
	</section>
</template>

<style>
.bg-dots-darker {
	background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
	.dark\:bg-dots-lighter {
		background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
	}
}
</style>
