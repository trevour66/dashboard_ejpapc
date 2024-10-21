const getThisWeekDates = () => {
    // Ensure the current date is a valid Date object
    let currentDate = new Date();

    // Calculate the start (Monday) and end (Sunday) of "this week"
    let dayOfWeek = currentDate.getDay();
    let diffToMonday = (dayOfWeek === 0 ? 6 : dayOfWeek - 1); // If it's Sunday, treat it as the last day of the week
    let startOfWeek = new Date(currentDate);
    startOfWeek.setDate(currentDate.getDate() - diffToMonday); // Go back to Monday
    let endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6); // Go forward to Sunday

    return {
        from: startOfWeek, // Format as YYYY-MM-DD
        to: endOfWeek
    };
}	

const getThisMonthDates = () => {
    // Ensure the current date is a valid Date object
    let currentDate = new Date();

    // Calculate the start (1st) and end (last day) of "this month"
    let startOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1); // 1st day of current month
    let endOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0); // Last day of current month

    return {			
        from: startOfMonth,
        to: endOfMonth
    };
}	

const getLastMonthDates = () => {
    let currentDate = new Date();
    
    // Get the first day of the current month
    let firstDayOfCurrentMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

    // Get the last day of the previous month
    let lastDayOfLastMonth = new Date(firstDayOfCurrentMonth);
    lastDayOfLastMonth.setDate(0); // Going back 1 day to the last day of the previous month
    
    // Get the first day of the previous month
    let firstDayOfLastMonth = new Date(lastDayOfLastMonth.getFullYear(), lastDayOfLastMonth.getMonth(), 1);

    return {
        from: firstDayOfLastMonth,
        to: lastDayOfLastMonth
    };
};

const getThisQuarterDates = () => {
    let currentDate = new Date();
    let month = currentDate.getMonth();
    
    // Determine the start and end month of the current quarter
    let quarterStartMonth = month - (month % 3); // 0 for Jan-Mar, 3 for Apr-Jun, etc.
    let startOfQuarter = new Date(currentDate.getFullYear(), quarterStartMonth, 1); // First day of the quarter
    let endOfQuarter = new Date(currentDate.getFullYear(), quarterStartMonth + 3, 0); // Last day of the quarter

    return {
        from: startOfQuarter,
        to: endOfQuarter
    };
};


const getThisYearDates = () => {
    let currentDate = new Date();
    
    // Get the first and last days of the current year
    let startOfYear = new Date(currentDate.getFullYear(), 0, 1); // January 1st
    let endOfYear = new Date(currentDate.getFullYear(), 11, 31); // December 31st

    return {
        from: startOfYear,
        to: endOfYear
    };
};


export {
    getThisWeekDates,
    getThisMonthDates,
    getLastMonthDates,
    getThisQuarterDates,
    getThisYearDates
}