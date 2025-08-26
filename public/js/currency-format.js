/**
 * Format a currency amount for display in client-side code
 * This is a JavaScript implementation similar to the server-side format_money helper
 * 
 * @param {number} amount - The amount to format
 * @param {string} currencyCode - The currency code (default: 'USD')
 * @param {number} exchangeRate - The exchange rate from USD (default: 1)
 * @param {boolean} includeCode - Whether to include the currency code (default: true)
 * @returns {string} The formatted currency amount
 */
function formatCurrency(amount, currencyCode = 'USD', exchangeRate = 1, includeCode = true) {
    try {
        // Convert amount to specified currency
        const convertedAmount = amount * exchangeRate;
        
        // Format based on currency
        let formatted;
        switch (currencyCode) {
            case 'USD':
                formatted = '$' + convertedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                break;
            case 'KHR':
                formatted = Math.round(convertedAmount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '៛';
                break;
            case 'EUR':
                formatted = '€' + convertedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                break;
            case 'GBP':
                formatted = '£' + convertedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                break;
            case 'JPY':
                formatted = '¥' + Math.round(convertedAmount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                break;
            case 'CNY':
                formatted = '¥' + convertedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                break;
            case 'THB':
                formatted = '฿' + convertedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                break;
            case 'SGD':
                formatted = 'S$' + convertedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                break;
            case 'MYR':
                formatted = 'RM' + convertedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                break;
            case 'VND':
                formatted = Math.round(convertedAmount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '₫';
                break;
            default:
                formatted = convertedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
        
        // Include currency code if requested
        if (includeCode && currencyCode !== 'USD') {
            formatted += ' ' + currencyCode;
        }
        
        return formatted;
    } catch (e) {
        // In case of any error, return a simple formatted USD amount
        console.error('Currency formatter error:', e);
        return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
}

// Global settings - These will be set from the user's profile
let CURRENT_CURRENCY_CODE = 'USD';
let CURRENT_EXCHANGE_RATE = 1;

/**
 * Set the current currency settings for the page
 * 
 * @param {string} currencyCode - The currency code
 * @param {number} exchangeRate - The exchange rate from USD
 */
function setCurrentCurrency(currencyCode, exchangeRate) {
    CURRENT_CURRENCY_CODE = currencyCode || 'USD';
    CURRENT_EXCHANGE_RATE = exchangeRate || 1;
}

/**
 * Format a currency amount using the current global currency settings
 * 
 * @param {number} amount - The amount to format (in USD)
 * @param {boolean} includeCode - Whether to include the currency code
 * @returns {string} The formatted currency amount
 */
function formatMoney(amount, includeCode = true) {
    return formatCurrency(amount, CURRENT_CURRENCY_CODE, CURRENT_EXCHANGE_RATE, includeCode);
}
