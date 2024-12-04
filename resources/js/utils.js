// Utility function to format the date and time
export const formatDate = (dateTime) => {
    // Check if the input contains time (T or space)
    const includesTime = dateTime.includes('T') || dateTime.includes(' ');

    // If the input contains time, split the date and time components
    const [datePart, timePart] = includesTime ? dateTime.split(/[\sT]/) : [dateTime, ''];

    // Split the date string (e.g., "1967-08-05") into components
    const [year, month, day] = datePart.split('-').map(Number);

    // Create a date using the components (month is zero-based)
    const date = new Date(year, month - 1, day);

    // Get the month, day, and year without padding
    const formattedMonth = date.getMonth() + 1; // Get month (1-12)
    const formattedDay = date.getDate(); // Get day (1-31)
    const formattedYear = date.getFullYear(); // Get year (e.g., 2024)

    // If the input does not include a time, return only the date
    if (!includesTime || !timePart) {
        return `${formattedMonth}/${formattedDay}/${formattedYear}`;
    }

    // If time is included, process the time part
    const [hours, minutes, seconds] = timePart.split(':').map(Number);
    let formattedHours = hours % 12;
    formattedHours = formattedHours ? formattedHours : 12; // Convert hour '0' to '12'
    const formattedMinutes = minutes.toString().padStart(2, '0');
    const formattedSeconds = seconds.toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';

    // Format the time as hh:mm:ss AM/PM
    const timeString = `${formattedHours}:${formattedMinutes}:${formattedSeconds} ${ampm}`;

    // Return formatted date and time: "mm/dd/yyyy at hh:mm:ss AM/PM"
    return `${formattedMonth}/${formattedDay}/${formattedYear} at ${timeString}`;
};
