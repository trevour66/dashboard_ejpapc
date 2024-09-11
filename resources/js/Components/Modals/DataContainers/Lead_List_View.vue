<script setup>
import { goto_matter_actionStep } from "@/Services/caseService";
const props = defineProps({
	lead_data: {
		required: true,
		type: Object,
	},
});

const parse_date = (date) => {
	if ((date ?? "") == "") return;

	const dateObj = new Date(date);

	return dateObj.toLocaleDateString();
};

</script>

<template>
	<div
		class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 dark:bg-gray-800 dark:border-gray-700"
	>
		<div class="w-full">
			<div class="mb-2 text-xs float-right">
				<div v-if="(lead_data?.lead_date_created ?? '') != ''">
					<span class="mr-2 inline-flex justify-center items-center">
						<svg
							class="w-4 h-4"
							viewBox="0 0 24 24"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
							<g
								id="SVGRepo_tracerCarrier"
								stroke-linecap="round"
								stroke-linejoin="round"
							></g>
							<g id="SVGRepo_iconCarrier">
								<path
									d="M14 22H10C6.22876 22 4.34315 22 3.17157 20.8284C2 19.6569 2 17.7712 2 14V12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12V14C22 17.7712 22 19.6569 20.8284 20.8284C20.1752 21.4816 19.3001 21.7706 18 21.8985"
									stroke="#333"
									stroke-width="1.5"
									stroke-linecap="round"
								></path>
								<path
									d="M7 4V2.5"
									stroke="#333"
									stroke-width="1.5"
									stroke-linecap="round"
								></path>
								<path
									d="M17 4V2.5"
									stroke="#333"
									stroke-width="1.5"
									stroke-linecap="round"
								></path>
								<path
									d="M21.5 9H16.625H10.75M2 9H5.875"
									stroke="#333"
									stroke-width="1.5"
									stroke-linecap="round"
								></path>
								<path
									d="M18 17C18 17.5523 17.5523 18 17 18C16.4477 18 16 17.5523 16 17C16 16.4477 16.4477 16 17 16C17.5523 16 18 16.4477 18 17Z"
									fill="#333"
								></path>
								<path
									d="M18 13C18 13.5523 17.5523 14 17 14C16.4477 14 16 13.5523 16 13C16 12.4477 16.4477 12 17 12C17.5523 12 18 12.4477 18 13Z"
									fill="#333"
								></path>
								<path
									d="M13 17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17C11 16.4477 11.4477 16 12 16C12.5523 16 13 16.4477 13 17Z"
									fill="#333"
								></path>
								<path
									d="M13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12C12.5523 12 13 12.4477 13 13Z"
									fill="#333"
								></path>
								<path
									d="M8 17C8 17.5523 7.55228 18 7 18C6.44772 18 6 17.5523 6 17C6 16.4477 6.44772 16 7 16C7.55228 16 8 16.4477 8 17Z"
									fill="#333"
								></path>
								<path
									d="M8 13C8 13.5523 7.55228 14 7 14C6.44772 14 6 13.5523 6 13C6 12.4477 6.44772 12 7 12C7.55228 12 8 12.4477 8 13Z"
									fill="#333"
								></path>
							</g>
						</svg>
						<span class="mx-1">
							{{ parse_date(lead_data?.lead_date_created ?? "") }}
						</span>
					</span>
				</div>

			</div>
			<div class="clear-both"></div>
		</div>
		<h5 class="text-base font-semibold text-gray-900 text-lg">
			{{ lead_data?.lead_name ?? "" }}
		</h5>

		<ul class="my-6 space-y-6">
			<li class="mb-8">
				<div
					v-if="lead_data?.LS_source ?? '' != ''"
					class="flex flex-col gap-2 text-base font-bold text-gray-700 p-2 border rounded-lg"
				>
					<div>
						<span
							class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-medium text-gray-500 bg-gray-200 rounded float-right"
							>Lead Source</span
						>
						<span class="clear-both"></span>
					</div>
					<div class="flex-1 ms-3 break-all">
						{{ lead_data?.LS_source ?? "" }}
					</div>
				</div>
			</li>

			<li class="mb-8" v-if="(lead_data?.matters ?? []).length >= 0">
				<div
					class="flex flex-col gap-2 text-base font-bold text-gray-700 p-0"
				>
					<div class="p-2">
						<span
							class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-medium text-gray-500 bg-gray-200 rounded float-right"
							>Matters</span
						>
						<span class="clear-both"></span>
					</div>
					<div class="flex-1 break-all h-[120px] overflow-y-scroll">

						<ul class=" text-sm text-gray-900 bg-white">
							
							<template v-for="matter, index in lead_data?.matters" :key="index">
								<li class="w-full font-normal px-2 py-3 border border-gray-200 rounded-lg mb-3">									
									<div>
										<h6 class="text-sm font-semibold text-gray-900 mb-2">
											{{ matter?.matter_current_name ?? "" }}

											<button
												@click="
													goto_matter_actionStep(matter?.matter_actionstep_id ?? false)
												"
												type="button"
												class="text-white bg-gray-50 hover:bg-gray-300 focus:ring-2 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-1 text-center inline-flex items-center me-2 ml-2"
											>
												<svg
													class="w-5 h-5"
													viewBox="0 0 24 24"
													fill="none"
													xmlns="http://www.w3.org/2000/svg"
												>
													<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
													<g
														id="SVGRepo_tracerCarrier"
														stroke-linecap="round"
														stroke-linejoin="round"
													></g>
													<g id="SVGRepo_iconCarrier">
														<path
															d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z"
															stroke="#333"
															stroke-width="1.5"
														></path>
														<path
															opacity="0.5"
															d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5"
															stroke="#333"
															stroke-width="1.5"
														></path>
													</g>
												</svg>
											</button>
										</h6>

										<div class="w-full">
											<div class="1 text-xs float-right">
												<div v-if="(matter?.matter_date_created ?? '') != ''">
													<span class="mr-2 inline-flex justify-center items-center">
														<span class="text-xs font-semibold">created :</span>
														<span class="mx-1">
															{{ parse_date(matter?.matter_date_created ?? "") }}
														</span>
													</span>
												</div>
											</div>
											<div class="clear-both"></div>
										</div>					

										<div class="w-full">
											<div class="1 text-xs float-right">
												<div v-if="(matter?.matter_last_activity ?? '') != ''">
													<span class="mr-2 inline-flex justify-center items-center">
														<span class="text-xs font-semibold">last activity :</span>
														<span class="mx-1">
															{{ parse_date(matter?.matter_last_activity ?? "") }}
														</span>
													</span>
												</div>
											</div>
											<div class="clear-both"></div>
										</div>
										
										<ul class="my-3 space-y-3">
											<li class="mb-4" v-if="matter?.current_step?.step_name ?? '' != ''">
												<div
													class="flex flex-col gap-2 text-base font-bold text-gray-700"
												>
													<div>
														<span
															class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium text-gray-500 bg-gray-200 rounded float-left"
															>Current Step</span
														>
														<span class="clear-both"></span>
													</div>
													<div class="flex-1 break-all">
														{{ matter?.current_step?.step_name ?? "" }}
													</div>
												</div>
											</li>

											<li class="mb-4" v-if=" matter?.current_matter_attorney?.ASALA_name  ?? '' != ''">
												<div
													class="flex flex-col gap-2 text-base font-bold text-gray-700 "
												>
													<div>
														<span
															class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium text-gray-500 bg-gray-200 rounded float-left"
															>Attorney</span
														>
														<span class="clear-both"></span>
													</div>
													<div class="flex-1 break-all">
														{{ matter?.current_matter_attorney?.ASALA_name ?? "" }}
													</div>
												</div>
											</li>
										</ul>
									</div>
								</li>
							</template>
							
						</ul>
					</div>
				</div>
			</li>

			
		</ul>
	</div>
</template>
