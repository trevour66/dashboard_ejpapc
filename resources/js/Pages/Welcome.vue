<script setup>
import { Head, Link } from "@inertiajs/vue3";

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
		class="min-h-screen py-8 bg-dots-darker flex justify-center items-center bg-gray-100"
	>
		<div
			class="min-h-[4em] bg-white relative sm:flex sm:justify-center sm:items-center"
		>
			<div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
				<Link
					v-if="$page.props.auth.user"
					:href="route('dashboard')"
					class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-[#982069]"
					>Dashboard</Link
				>

				<template v-else>
					<Link
						:href="route('login')"
						class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-[#982069]"
						>Log in</Link
					>

					<Link
						v-if="canRegister"
						:href="route('register')"
						class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-[#982069]"
						>Register</Link
					>
				</template>
			</div>
		</div>
		<div class="h-full w-full max-w-full">
			<div
				class="relative sm:flex sm:justify-center sm:items-center bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-[#982069] selection:text-white h-full"
			>
				<div class="max-w-7xl mx-auto p-6 lg:p-8 py-28">
					<div class="mt-16">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
							<a
								href="https://www.ejusticeproject.com/"
								target="_blank"
								class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-[#982069]"
							>
								<div>
									<div
										class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full"
									>
										<svg
											fill="none"
											class="w-7 h-7 stroke-[#982069]"
											stroke-width="1.5"
											viewBox="0 0 24 24"
											xmlns="http://www.w3.org/2000/svg"
											aria-hidden="true"
										>
											<path
												stroke-linecap="round"
												stroke-linejoin="round"
												d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"
											></path>
										</svg>
									</div>

									<h2
										class="mt-6 text-xl font-semibold text-gray-900 dark:text-white"
									>
										E-Justice Project
									</h2>

									<p
										class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed"
									>
										E-Justice Project. A California law firm focused on
										empowering historically underserved communities by educating
										and advocating for their employment-related rights. They
										provide legal assistance, including negotiations and
										litigation, to ensure equitable access to justice for
										California workers.
									</p>
								</div>

								<svg
									xmlns="http://www.w3.org/2000/svg"
									fill="none"
									viewBox="0 0 24 24"
									stroke-width="2"
									class="self-center shrink-0 stroke-[#982069] w-6 h-6 mx-6"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
									/>
								</svg>
							</a>

							<a
								href="https://ejpapc.com"
								target="_blank"
								class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-[#982069]"
							>
								<div>
									<div
										class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full"
									>
										<svg
											class="w-7 h-7"
											version="1.1"
											id="Capa_1"
											xmlns="http://www.w3.org/2000/svg"
											xmlns:xlink="http://www.w3.org/1999/xlink"
											viewBox="0 0 24 24"
											xml:space="preserve"
											fill="#982069"
										>
											<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
											<g
												id="SVGRepo_tracerCarrier"
												stroke-linecap="round"
												stroke-linejoin="round"
											></g>
											<g id="SVGRepo_iconCarrier">
												<g>
													<path
														style="fill: #982069"
														d="M2,6.577v4.461h16.189l1.396-1.395c0.44-0.439,1.158-0.439,1.597,0l1.396,1.395h1.499v2h-1.5 l-1.394,1.394c-0.44,0.44-1.158,0.44-1.597,0l-1.395-1.394H2V17.5h1.42l1.397-1.396c0.44-0.439,1.158-0.439,1.597,0L7.811,17.5 h16.266v2H7.808l-1.395,1.394c-0.439,0.44-1.157,0.44-1.596,0L3.422,19.5H2v4.538H0.077H0v-24h0.077H2v4.539h6.958l1.396-1.396 c0.44-0.439,1.158-0.439,1.597,0l1.397,1.396h10.729v2h-10.73l-1.395,1.394c-0.439,0.44-1.157,0.44-1.596,0L8.96,6.577H2z"
													></path>
												</g>
											</g>
										</svg>
									</div>

									<h2
										class="mt-6 text-xl font-semibold text-gray-900 dark:text-white"
									>
										EJPAPC
									</h2>

									<p
										class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed"
									>
										A comprehensive timeline tool that methodically chronicles
										the sequence of events in your legal case. Effectively
										manage the evolution of your case, ensuring a thorough grasp
										of the proceedings and maintaining control over every
										significant development.
									</p>
								</div>

								<svg
									xmlns="http://www.w3.org/2000/svg"
									fill="none"
									viewBox="0 0 24 24"
									stroke-width="1.5"
									class="self-center shrink-0 stroke-[#982069] w-6 h-6 mx-6"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
									/>
								</svg>
							</a>
						</div>
					</div>

					<div class="mt-16 px-6">
						<div
							class="float-right ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0"
						>
							E-Justice Project Employee Incentive Program v{{ appVersion }}
						</div>
						<div class="clear-both"></div>
					</div>
				</div>
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
