// Utility function to format the date and time
export const formatDate = (dateTime) => {
    const date = new Date(dateTime);

    // Get the month, day, and year
    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Get month (1-12)
    const day = date.getDate().toString().padStart(2, '0'); // Get day (1-31)
    const year = date.getFullYear(); // Get year (e.g., 2024)

    // Get the hours, minutes, seconds, and AM/PM
    let hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    
    // Convert to 12-hour format
    hours = hours % 12;
    hours = hours ? hours : 12; // The hour '0' should be '12'
    
    // Format the time as hh:mm:ss AM/PM
    const timeString = `${hours}:${minutes}:${seconds} ${ampm}`;
    
    // Return formatted date and time: "mm/dd/yyyy at hh:mm:ss AM/PM"
    return `${month}/${day}/${year} at ${timeString}`;
};
