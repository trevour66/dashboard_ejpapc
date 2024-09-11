import axios from "axios";

const getLeadByStepAndSource_dashboard = async (to, from, dataType) => {
	const res = {
		step: null,
		source: null,
	};

	if (!(to ?? false) || !(from ?? false) || !(dataType ?? false)) {
		return res;
	}

	const form = {
		to,
		from,
		dataType,
	};

	return await axios
		.post("/get-leads-by-step-and-source", form)
		.then((response) => {
			return response.data?.data ?? false;
		})
		.catch((error) => {
			return false;
		});
};

const getAverageData = async (to, from, dataType) => {
	const res = {
		step: null,
		source: null,
	};

	if (!(to ?? false) || !(from ?? false) || !(dataType ?? false)) {
		return res;
	}

	const form = {
		to,
		from,
		dataType,
	};

	return await axios
		.post("/get-leads-averages", form)
		.then((response) => {
			return response.data?.data ?? false;
		})
		.catch((error) => {
			return false;
		});
};

const getOpenLeads = (url, to, from) => {
	if (!(to ?? false) || !(from ?? false)) {
		return false;
	}

	const form = {
		to,
		from,
	};

	return axios
		.post(url, form)
		.then((res) => {
			// console.log(res);
			if (res.status === 200) {
				const { data } = res;

				const fetchedData = data?.data?.data ?? [];
				const next_page_url = data?.data?.next_page_url ?? null;

				return {
					success: true,
					fetchedData: fetchedData,
					next_page_url: next_page_url,
				};
			} else {
				return {
					success: false,
					fetchedData: null,
					next_page_url: null,
				};
			}
		})
		.catch((err) => {
			return {
				success: false,
				fetchedData: null,
				next_page_url: null,
			};
		});
};

const getLeadsByStepOrSource = (url, to, from, filter) => {
	if (!(to ?? false) || !(from ?? false) || !(filter ?? false)) {
		return false;
	}

	const form = {
		to,
		from,
		filter,
	};

	return axios
		.post(url, form)
		.then((res) => {
			// console.log(res);
			if (res.status === 200) {
				const { data } = res;

				const fetchedData = data?.data?.data ?? [];
				const next_page_url = data?.data?.next_page_url ?? null;

				return {
					success: true,
					fetchedData: fetchedData,
					next_page_url: next_page_url,
				};
			} else {
				return {
					success: false,
					fetchedData: null,
					next_page_url: null,
				};
			}
		})
		.catch((err) => {
			return {
				success: false,
				fetchedData: null,
				next_page_url: null,
			};
		});
};

const getRetainedLeads = (url, to, from) => {
	if (!(to ?? false) || !(from ?? false)) {
		return false;
	}

	const form = {
		to,
		from,
	};

	return axios
		.post(url, form)
		.then((res) => {
			// console.log(res);
			if (res.status === 200) {
				const { data } = res;

				const fetchedData = data?.data?.data ?? [];
				const next_page_url = data?.data?.next_page_url ?? null;

				return {
					success: true,
					fetchedData: fetchedData,
					next_page_url: next_page_url,
				};
			} else {
				return {
					success: false,
					fetchedData: null,
					next_page_url: null,
				};
			}
		})
		.catch((err) => {
			return {
				success: false,
				fetchedData: null,
				next_page_url: null,
			};
		});
};
const getNewLeads = (url, to, from) => {
	if (!(to ?? false) || !(from ?? false)) {
		return false;
	}

	const form = {
		to,
		from,
	};

	return axios
		.post(url, form)
		.then((res) => {
			// console.log(res);
			if (res.status === 200) {
				const { data } = res;

				const fetchedData = data?.data?.data ?? [];
				const next_page_url = data?.data?.next_page_url ?? null;

				return {
					success: true,
					fetchedData: fetchedData,
					next_page_url: next_page_url,
				};
			} else {
				return {
					success: false,
					fetchedData: null,
					next_page_url: null,
				};
			}
		})
		.catch((err) => {
			return {
				success: false,
				fetchedData: null,
				next_page_url: null,
			};
		});
};

export { getLeadByStepAndSource_dashboard, getAverageData, getOpenLeads, getLeadsByStepOrSource, getRetainedLeads, getNewLeads };
