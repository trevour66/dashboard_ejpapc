const modalViewTypes = {
	matters_list_view: "matters_list_view",
	leads_list_view: "leads_list_view",
	custom_date_range_form_view: "custom_date_range_form_view",
};

const casesModals = {
	new_leads_modal: {
		label: "New Leads",
		name: "new_leads",
		category: "lead",
		view_type: modalViewTypes.leads_list_view,
		has_filter: false,
	},
	
	retained_leads_modal: {
		label: "Retained Leads",
		name: "retained_leads",
		category: "lead",
		view_type: modalViewTypes.leads_list_view,
		has_filter: false,
	},
	
	leads_by_step_modal: {
		label: "Leads by Step",
		name: "leads_by_step",
		category: "lead",
		view_type: modalViewTypes.leads_list_view,
		has_filter: true,
	},

	leads_by_source_modal: {
		label: "Leads by Source",
		name: "leads_by_source",
		category: "lead",
		view_type: modalViewTypes.leads_list_view,
		has_filter: true,
	},

	open_cases_modal: {
		label: "Open Cases",
		name: "open_case",
		category: "matter",
		view_type: modalViewTypes.matters_list_view,
		has_filter: false,
	},

	cases_with_next_step_overdue_modal: {
		label: "NextStep Overdue",
		name: "cases_with_next_step_overdue_modal",
		category: "matter",
		view_type: modalViewTypes.matters_list_view,
		has_filter: false,
	},

	last_accessed_within_ten_days_modal: {
		label: "Last Accessed within 10 Days",
		name: "last_accessed_within_ten_days_modal",
		category: "matter",
		view_type: modalViewTypes.matters_list_view,
		has_filter: false,
	},

	cases_by_step_modal: {
		label: "Cases by Step",
		name: "cases_by_step",
		category: "matter",
		view_type: modalViewTypes.matters_list_view,
		has_filter: true,
	},

	cases_by_atty_modal: {
		label: "Cases by Attorney",
		name: "cases_by_attorney",
		category: "matter",
		view_type: modalViewTypes.matters_list_view,
		has_filter: true,
	},

	fees_by_responsible_atty_modal: {
		label: "Fees by Attorney",
		name: "fees_by_attorney",
		category: "matter",
		view_type: modalViewTypes.matters_list_view,
		has_filter: true,
	},

	anticipated_funds_modal: {
		label: "Anticipated Funds",
		name: "anticipated_funds",
		category: "matter",
		view_type: modalViewTypes.matters_list_view,
		has_filter: false,
	},

	atty_fees_collected_modal: {
		label: "Atty Fees Collected",
		name: "atty_fees_collected",
		category: "matter",
		view_type: modalViewTypes.matters_list_view,
		has_filter: false,
	},

	average_atty_fees_collected_modal: {
		label: "Atty Fees Collected(average)",
		name: "average_atty_fees_collected",
		category: "matter",
		view_type: modalViewTypes.matters_list_view,
		has_filter: false,
	},
};

const ModalTypes = {
	...casesModals,
	custom_date_range_form_view: {
		name: 'custom_date',
		view_type: modalViewTypes.custom_date_range_form_view
	},
	none: {
		name: "none",
		view_type: null,
	},
};

export { ModalTypes, modalViewTypes };
