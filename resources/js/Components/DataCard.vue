<script setup>
import { initModals } from "flowbite";

const props = defineProps({
	meta: {
		type: Object,
		required: false,
		default: null,
	},
	avg: {
		type: Object,
		required: false,
		default: null,
	},
});

const hasData = () => {
	const count = props?.avg?.data ?? 0;

	if (count === 0 || count === "__") {
		return false;
	}

	return true;
	// console.log(props.avg);
};

const emittedEvents = defineEmits(["openModal"]);

const initModal = () => {
	emittedEvents("openModal");
};
</script>

<template>
	<div class="bg-white p-4 rounded-lg shadow-lg border-2 border-gray-100 min-w-[150px]">
		<div class="flex flex-row space-x-4 items-center">
			<div>
				<div class="flex justify-between items-center gap-4">
					<div class="flex items-center h-full w-full">
						<p class="text-gray-500 text-xs font-bold uppercase leading-4">
							<slot name="header"></slot>
						</p>
					</div>
					<div>
						<div v-if="meta != null && hasData()">
							<button
								@click="initModal(meta)"
								type="button"
								class="text-white bg-gray-200 hover:bg-gray-300 focus:ring-2 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center me-2"
							>
								<svg
									class="w-3 h-3"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512"
								>
									<path
										d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l82.7 0L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3l0 82.7c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160c0-17.7-14.3-32-32-32L320 0zM80 32C35.8 32 0 67.8 0 112L0 432c0 44.2 35.8 80 80 80l320 0c44.2 0 80-35.8 80-80l0-112c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 112c0 8.8-7.2 16-16 16L80 448c-8.8 0-16-7.2-16-16l0-320c0-8.8 7.2-16 16-16l112 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 32z"
									/>
								</svg>
							</button>
						</div>
					</div>
				</div>
				<p
					class="text-gray-700 font-bold text-md inline-flex items-center space-x-2"
				>
					<slot name="content"></slot>
				</p>
			</div>
		</div>
	</div>
</template>
