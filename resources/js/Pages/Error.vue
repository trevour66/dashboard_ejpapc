<script setup>
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
	status: {
		required: true,
		type: String,
	},
	message: String,
});
</script>

<template>
	<Head title="Error" />

	<div
		class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
	>
		<div class="flex flex-col justify-center items-center">
			<div class="mb-4">
				<Link href="/">
					<img class="h-24" src="/images/ejp.png" alt="" />
				</Link>
			</div>

			<div
				class="sm:max-w-sm md:max-w-[450px] rounded-lg bg-white p-8 text-center shadow-xl"
			>
				<h1 class="mb-4 text-4xl font-bold uppercase">{{ status }}</h1>
				<p class="text-gray-600 my-8">Whoops, something went wrong.</p>

				<p class="text-gray-500 mt-8 py-2 border-y-2 text-center">
					{{ message }}
				</p>
			</div>
		</div>
	</div>
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
