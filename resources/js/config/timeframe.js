const Timeframe = {
	this_week: "this_week",
	this_month: "this_month",
	last_month: "last_month",
	this_quarter: "this_quarter",
	this_year: "this_year",
	custom: "custom",
	all: "all",
};

const timeframeButtons = [
	{
		data: Timeframe.this_week,
		name: "This Week",
	},
	{
		data: Timeframe.this_month,
		name: "This Month",
	},

	{
		data: Timeframe.last_month,
		name: "Last Month",
	},
	{
		data: Timeframe.this_quarter,
		name: "This Quarter",
	},
	{
		data: Timeframe.this_year,
		name: "This Year",
	},

	// Last month, This Year, and this quarter.
	{
		data: Timeframe.custom,
		name: "Custom",
	},
	{
		data: Timeframe.all,
		name: "All",
	},
];

export { Timeframe, timeframeButtons };
