import axios from "axios";

const getGeneralFinanceData = async (to, from, dataType) => {
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
		.post("/get-finance-count", form)
		.then((response) => {
			return response.data?.data ?? false;
		})
		.catch((error) => {
			return false;
		});
};

const getFeesByAtty = (url, to, from, filter) => {
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

export { getGeneralFinanceData, getFeesByAtty };
