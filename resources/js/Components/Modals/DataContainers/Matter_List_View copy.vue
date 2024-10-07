<script setup>
import { goto_matter_actionStep } from "@/Services/caseService";
const props = defineProps({
	matter_data: {
		required: true,
		type: Object,
	},
});

const parse_date = (date) => {
	if ((date ?? "") == "") return;

	const dateObj = new Date(date);

	return dateObj.toLocaleDateString();
};

const formatCurrency = (amount) => {
	if ((amount ?? "") == "") return;
	return amount.toLocaleString("en-US", { style: "currency", currency: "USD" });
};
</script>

<template>
	<div
		class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 dark:bg-gray-800 dark:border-gray-700"
	>
		<div class="w-full">
			<div class="mb-2 text-xs float-right">
				<div v-if="(matter_data?.matter_date_created ?? '') != ''">
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
							{{ parse_date(matter_data?.matter_date_created ?? "") }}
						</span>
					</span>
				</div>

				<div v-if="(matter_data?.matter_current_offer ?? '') != ''">
					<span class="mr-2 inline-flex justify-center items-center">
						<svg
							fill="#e8b445"
							viewBox="-1 0 19 19"
							xmlns="http://www.w3.org/2000/svg"
							class="cf-icon-svg w-4 h-4"
						>
							<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
							<g
								id="SVGRepo_tracerCarrier"
								stroke-linecap="round"
								stroke-linejoin="round"
							></g>
							<g id="SVGRepo_iconCarrier">
								<path
									d="M16.417 9.58A7.917 7.917 0 1 1 8.5 1.662a7.917 7.917 0 0 1 7.917 7.916zm-2.93.717L8.981 5.793A2.169 2.169 0 0 0 7.63 5.24l-2.635.026a.815.815 0 0 0-.801.8L4.163 8.71a2.166 2.166 0 0 0 .55 1.352l4.505 4.504a.794.794 0 0 0 1.12 0l3.148-3.15a.794.794 0 0 0 0-1.119zM5.724 6.25a.554.554 0 1 0 .554.554.554.554 0 0 0-.554-.554zm5.306 5.857a.396.396 0 0 1-.56 0l-.246-.246a1.853 1.853 0 0 1-.328.172 1.17 1.17 0 0 1-.465.091c-.034 0-.068 0-.103-.003a.396.396 0 0 1 .057-.79.428.428 0 0 0 .199-.025 1.125 1.125 0 0 0 .198-.102 1.683 1.683 0 0 0 .181-.142 1.004 1.004 0 0 0 .267-.47c.042-.199-.025-.266-.057-.298a.294.294 0 0 0-.186-.083.654.654 0 0 0-.221.024.875.875 0 0 0-.206.097.995.995 0 0 0-.164.134 2.094 2.094 0 0 1-.267.228 1.606 1.606 0 0 1-.422.216 1.305 1.305 0 0 1-.546.06 1.103 1.103 0 0 1-.669-.31 1.118 1.118 0 0 1-.275-1.063 1.688 1.688 0 0 1 .221-.522l-.24-.24a.396.396 0 1 1 .559-.56l.249.248a1.937 1.937 0 0 1 .343-.167 1.388 1.388 0 0 1 .485-.09.396.396 0 0 1 .001.792.595.595 0 0 0-.206.039 1.141 1.141 0 0 0-.208.1l-.02.012a1.122 1.122 0 0 0-.148.106 1.01 1.01 0 0 0-.264.457.334.334 0 0 0 .063.328.326.326 0 0 0 .19.082.528.528 0 0 0 .215-.023.837.837 0 0 0 .211-.109 1.324 1.324 0 0 0 .168-.144 1.793 1.793 0 0 1 .296-.24 1.679 1.679 0 0 1 .399-.187 1.454 1.454 0 0 1 .51-.058 1.082 1.082 0 0 1 .692.314 1.058 1.058 0 0 1 .27 1.022 1.703 1.703 0 0 1-.223.54l.25.251a.395.395 0 0 1 0 .56z"
								></path>
							</g>
						</svg>

						<span class="mx-1">
							{{ formatCurrency(matter_data?.matter_current_offer ?? "") }}
						</span>
					</span>
				</div>

				<div v-if="(matter_data?.matter_atty_fees ?? '') != ''">
					<span class="mr-2 inline-flex justify-center items-center">
						<svg
							class="w-4 h-4"
							viewBox="0 0 24 24"
							xmlns="http://www.w3.org/2000/svg"
							stroke="#e9b035"
						>
							<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
							<g
								id="SVGRepo_tracerCarrier"
								stroke-linecap="round"
								stroke-linejoin="round"
							></g>
							<g id="SVGRepo_iconCarrier">
								<path
									d="M8.5,23a9.069,9.069,0,0,0,3.5-.68,8.92,8.92,0,0,0,3.5.68c3.645,0,6.5-1.945,6.5-4.429V14.429C22,11.945,19.145,10,15.5,10c-.169,0-.335.008-.5.017V5.333C15,2.9,12.145,1,8.5,1S2,2.9,2,5.333V18.667C2,21.1,4.855,23,8.5,23ZM20,18.571C20,19.72,18.152,21,15.5,21S11,19.72,11,18.571v-.925a8.329,8.329,0,0,0,4.5,1.211A8.329,8.329,0,0,0,20,17.646ZM15.5,12c2.652,0,4.5,1.28,4.5,2.429s-1.848,2.428-4.5,2.428S11,15.577,11,14.429,12.848,12,15.5,12Zm-7-9C11.152,3,13,4.23,13,5.333S11.152,7.667,8.5,7.667,4,6.437,4,5.333,5.848,3,8.5,3ZM4,8.482A8.466,8.466,0,0,0,8.5,9.667,8.466,8.466,0,0,0,13,8.482V10.33a6.47,6.47,0,0,0-2.9,1.607,7.694,7.694,0,0,1-1.6.174c-2.652,0-4.5-1.23-4.5-2.333Zm0,4.445a8.475,8.475,0,0,0,4.5,1.184c.178,0,.35-.022.525-.031A3.1,3.1,0,0,0,9,14.429v2.085c-.168.01-.33.042-.5.042-2.652,0-4.5-1.23-4.5-2.334Zm0,4.444a8.466,8.466,0,0,0,4.5,1.185c.168,0,.333-.013.5-.021v.036a3.466,3.466,0,0,0,.919,2.293A7.839,7.839,0,0,1,8.5,21C5.848,21,4,19.77,4,18.667Z"
								></path>
							</g>
						</svg>

						<span class="mx-1">
							{{ formatCurrency(matter_data?.matter_atty_fees ?? "") }}
						</span>
					</span>
				</div>
			</div>
			<div class="clear-both"></div>
		</div>
		<h5 class="text-base font-semibold text-gray-900 text-lg">
			{{ matter_data?.matter_current_name ?? "" }}

			<button
				@click="
					goto_matter_actionStep(matter_data?.matter_actionstep_id ?? false)
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
		</h5>

		<ul class="my-6 space-y-6">
			<li class="mb-8" v-if="matter_data?.matter_last_filenote ?? '' != ''">
				<div
					class="flex flex-col gap-2 text-base font-bold text-gray-700 p-2 border rounded-lg"
				>
					<div>
						<span
							class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-medium text-gray-500 bg-gray-200 rounded float-right"
							>Last Filenote</span
						>
						<span class="clear-both"></span>
					</div>
					<div class="flex-1 ms-3 break-all">
						<p
							class="text-sm font-normal text-gray-500 dark:text-gray-400 my-6 h-[100px] overflow-y-auto break-all"
						>
							{{ matter_data?.matter_last_filenote ?? "" }}
						</p>
					</div>
				</div>
			</li>

			<li class="mb-8">
				<div
					v-if="matter_data?.step_name ?? '' != ''"
					class="flex flex-col gap-2 text-base font-bold text-gray-700 p-2 border rounded-lg"
				>
					<div>
						<span
							class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-medium text-gray-500 bg-gray-200 rounded float-right"
							>Current Step</span
						>
						<span class="clear-both"></span>
					</div>
					<div class="flex-1 ms-3 break-all">
						{{ matter_data?.step_name ?? "" }}
					</div>
				</div>
			</li>

			<li class="mb-8">
				<div
					class="flex flex-col gap-2 text-base font-bold text-gray-700 p-2 border rounded-lg"
				>
					<div>
						<span
							class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-medium text-gray-500 bg-gray-200 rounded float-right"
							>Attorney</span
						>
						<span class="clear-both"></span>
					</div>
					<div class="flex-1 ms-3 break-all">
						{{ matter_data?.ASALA_name ?? "" }}
					</div>
				</div>
			</li>
			<li class="mb-8">
				<div
					class="flex flex-col gap-2 text-base font-bold text-gray-700 p-2 border rounded-lg"
				>
					<div>
						<span
							class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-medium text-gray-500 bg-gray-200 rounded float-right"
							>Lead</span
						>
						<span class="clear-both"></span>
					</div>
					<div class="flex-1 ms-3 break-all">
						{{ matter_data?.lead_name ?? "" }}
					</div>
				</div>
			</li>
		</ul>
	</div>
</template>
