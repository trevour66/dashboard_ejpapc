<script setup>
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { computed, ref, watchEffect, watch, reactive } from "vue";

const prop = defineProps({
	submission: {
		required: true,
		type: Object,
	},
	show_proof_of_employment: Boolean,
	show_admin_rems: Boolean,
	timeline: Boolean,
});

const form = useForm({
	poe: null,
	admin_rems: null,
	submission: JSON.stringify(prop.submission),
});

const tempFileHolder = reactive({
	poe: null,
	admin_rems: null,
});

const submitForm = () => {
	if (formDisabled.value) {
		return;
	}

	if (prop.show_proof_of_employment ?? false) {
		form.poe = tempFileHolder.poe ?? null;
	}
	if (prop.show_admin_rems ?? false) {
		form.admin_rems = tempFileHolder.admin_rems ?? null;
	}

	form.post(route("requirements.store"), {
		onSuccess: () => form.reset(),
	});
};

const formDisabled = ref(true);

watch(tempFileHolder, (newValue, oldValue) => {
	if (prop.show_proof_of_employment && newValue.poe) {
		formDisabled.value = false;
	}

	if (prop.show_admin_rems && newValue.admin_rems !== null) {
		console.log("here");
		formDisabled.value = false;
	}
});
</script>

<template>
	<Head title="Question Requirement" />

	<div
		class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
	>
		<div class="flex flex-col justify-center items-center">
			<div class="mb-4">
				<Link href="/">
					<img class="h-24" src="/images/ejp.png" alt="" />
				</Link>
			</div>
			<!-- {{ form.errors }}
			{{ submission }} -->
			<div
				class="max-w-sm md:max-w-[460px] p-12 bg-white mx-auto border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
			>
				<div v-if="!show_proof_of_employment && !show_admin_rems">
					<p class="my-8 text-md font-bold text-gray-900 text-center">
						Hy there!
					</p>

					<p class="my-8 text-md text-gray-700 text-center">
						It seems you have uploaded all the required files and must have
						gotten a link to book your consultation. If this did not happen,
						Please contact EJPAPC.
					</p>
				</div>
				<div v-else>
					<h2
						class="mb-8 text-lg font-bold text-gray-900 text-center uppercase"
					>
						Upload Required Files
					</h2>
					<div class="text-red-800 text-center my-4 text-sm font-semibold">
						<div v-if="form.errors.submission" class="mb-2">
							We experienced a crucial error while satisfying your request.
							Please contact support
						</div>

						<div v-if="form.errors.general" class="mb-2">
							{{ form.errors?.general ? form.errors?.general : "" }}
						</div>

						<div>
							{{ form.errors?.poe ? form.errors?.poe : "" }}
							{{ form.errors?.admin_rems ? form.errors?.admin_rems : "" }}
						</div>
					</div>
					<form>
						<div class="w-full mb-6" v-if="show_proof_of_employment">
							<label class="block mb-2" for="poe">
								<span class="font-medium text-[#982069] dark:text-white">
									Proof of Employment
								</span>
								<br />
								<span class="text-sm">
									Please upload a paystub, W2, offer letter or other document
									that allows us to verify your employer's name
								</span>
							</label>
							<input
								class="block w-full text-md text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
								id="poe"
								name="poe"
								type="file"
								@input="tempFileHolder.poe = $event.target.files[0]"
							/>
						</div>

						<div class="w-full mb-6" v-if="show_admin_rems">
							<label class="block mb-2" for="admin_rems">
								<span class="font-medium text-[#982069] dark:text-white">
									Admin Remedy
								</span>
								<br />
								<span class="text-sm">
									You indicated that you had submitted your claim
									<span v-if="submission?.agency_names ?? false">
										to the agency(s)
										<span class="uppercase font-semibold text-gray-500">
											: {{ submission?.agency_names }}
										</span> </span
									>. Please upload a document for the agency that shows your
									case number.
								</span>
							</label>
							<input
								class="block w-full text-md text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
								id="admin_rems"
								name="admin_rems"
								type="file"
								@input="tempFileHolder.admin_rems = $event.target.files[0]"
							/>
						</div>

						<div class="w-full mb-6">
							<progress
								v-if="form.progress"
								:value="form.progress.percentage"
								max="100"
							>
								{{ form.progress.percentage }}%
							</progress>
						</div>

						<PrimaryButton
							:disabled="formDisabled"
							type="submit"
							class="float-right"
							:class="{
								'cursor-not-allowed': formDisabled,
							}"
							@click.prevent="submitForm"
						>
							Submit
						</PrimaryButton>
						<span class="clear-both"></span>
					</form>
				</div>
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
