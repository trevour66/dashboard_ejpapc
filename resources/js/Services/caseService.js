import axios from "axios";

const goto_matter_actionStep = async (matter_actionStepID) => {
	if (!(matter_actionStepID ?? false) || !(window ?? false)) return;
	const url = `https://us-west-2.actionstep.com/mym/asfw/workflow/action/overview/action_id/${matter_actionStepID}`;

	// window.open(url, "_blank");
	// window.location.href = url;

	await navigator.clipboard.writeText(url);
	alert("Link copied to clipboard!");
};

const getCasesStartupData_dashboard = async (to, from, dataType) => {
	if (!(to ?? false) || !(from ?? false) || !(dataType ?? false)) {
		return false;
	}

	const form = {
		to,
		from,
		dataType,
	};

	return await axios
		.post("/get-cases-startup-data", form)
		.then((response) => {
			// console.log(response);
			return response.data?.data ?? false;
		})
		.catch((error) => {
			return false;
		});
};

const getOpenCases = (url, to, from) => {
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

const getCases_lastAccessedWithin10Day = (url, to, from) => {
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

const getNextStepOverduedCases = (url, to, from) => {
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

const getCasesByStep = (url, to, from, filter) => {
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

const getCasesByAtty = (url, to, from, filter) => {
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

const getAnticipatedFunds_getAttyFeesCollected = (url, to, from) => {
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

export {
	getCasesStartupData_dashboard,
	getOpenCases,
	getCasesByStep,
	getCasesByAtty,
	goto_matter_actionStep,
	getNextStepOverduedCases,
	getCases_lastAccessedWithin10Day,
	getAnticipatedFunds_getAttyFeesCollected,
};
