

function formatNumber(number, decimals = 0) {


    // Parse the number to ensure it's treated as a number
    number = parseFloat(number);
    // Parse the int to ensure it's treated as a int
    decimals = parseInt(decimals);

    // Validate inputs
    if (isNaN(number)) {
        number = 0;
    }


    // Use Intl.NumberFormat for advanced formatting options
    const formatter = new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });

    // Format the number with the specified decimals
    const formattedNumber = formatter.format(number);

    return formattedNumber;
}




