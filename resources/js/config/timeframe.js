const Timeframe = {
	this_week: "this_week",
	this_month: "this_month",
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
		data: Timeframe.custom,
		name: "Custom",
	},
	{
		data: Timeframe.all,
		name: "All",
	},
];

export { Timeframe, timeframeButtons };
