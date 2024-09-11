const Timeframe = {
	this_week: "this_week",
	this_month: "this_month",
	two_months: "two_months",
	three_months: "three_months",
	six_months: "six_months",
	one_year: "one_year",
	all: "all",
};

const timeframeButtons = [
	{
		data: Timeframe.this_week,
		name: "Last 7 days",
	},
	{
		data: Timeframe.this_month,
		name: "Last 30 days",
	},
	{
		data: Timeframe.two_months,
		name: "2M",
	},
	{
		data: Timeframe.three_months,
		name: "3M",
	},
	{
		data: Timeframe.six_months,
		name: "6M",
	},
	{
		data: Timeframe.one_year,
		name: "1Y",
	},
	{
		data: Timeframe.all,
		name: "All",
	},
];

export { Timeframe, timeframeButtons };
