{{-- This partial provides JavaScript functions to format money values according to user's currency preferences --}}
<script>
/**
 * Format a money value according to the user's currency settings
 * @param {number|string} amount - The amount to format
 * @param {boolean} includeSymbol - Whether to include the currency symbol
 * @returns {Promise<string>} - The formatted money value
 */
async function formatMoney(amount, includeSymbol = true) {
    // Ensure amount is a valid number
    let numericAmount;
    try {
        numericAmount = parseFloat(amount);
        if (isNaN(numericAmount)) {
            numericAmount = 0;
        }
    } catch (e) {
        numericAmount = 0;
    }
    
    try {
        const response = await fetch('{{ route("landlord.getFormattedMoney") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                amounts: [numericAmount]
            })
        });
        
        const data = await response.json();
        if (data.success && data.formatted && data.formatted.length > 0) {
            return data.formatted[0];
        }
        
        throw new Error('Failed to format money value');
    } catch (error) {
        console.error('Error formatting money:', error);
        // Fallback to client-side formatting
        const currencySymbol = '{{ auth()->user()->currency_code ?: "$" }}';
        return includeSymbol ? `${currencySymbol}${numericAmount.toFixed(2)}` : numericAmount.toFixed(2);
    }
}

/**
 * Format multiple money values according to the user's currency settings
 * @param {Array<number|string>} amounts - The amounts to format
 * @returns {Promise<Array<string>>} - The formatted money values
 */
async function formatMoneyBatch(amounts) {
    // Ensure all amounts are valid numbers
    const numericAmounts = amounts.map(amount => {
        try {
            const numericAmount = parseFloat(amount);
            return isNaN(numericAmount) ? 0 : numericAmount;
        } catch (e) {
            return 0;
        }
    });
    
    try {
        const response = await fetch('{{ route("landlord.getFormattedMoney") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ amounts: numericAmounts })
        });
        
        const data = await response.json();
        if (data.success && data.formatted) {
            return data.formatted;
        }
        
        throw new Error('Failed to format money values');
    } catch (error) {
        console.error('Error formatting money batch:', error);
        // Fallback to client-side formatting
        const currencySymbol = '{{ auth()->user()->currency_code ?: "$" }}';
        return numericAmounts.map(amount => `${currencySymbol}${amount.toFixed(2)}`);
    }
}
</script>
