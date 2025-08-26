// Currency Exchange Rate Handling
document.addEventListener('DOMContentLoaded', function() {
    const currencySelect = document.getElementById('currency_code');
    const exchangeRateInput = document.getElementById('exchange_rate');
    const currencyCodeSpans = document.querySelectorAll('.currency-code');
    const exchangeRateWrapper = exchangeRateInput ? exchangeRateInput.closest('.form-group') : null;
    
    let userEditedRate = false;
    let lastCurrency = currencySelect ? currencySelect.value : null;
    let fetchingRate = false;
    
    if (currencySelect && exchangeRateInput) {
        // Add a small info message below the exchange rate input
        const infoDiv = document.createElement('div');
        infoDiv.className = 'text-muted small mt-1 rate-status';
        infoDiv.style.fontSize = '0.8rem';
        infoDiv.style.display = 'none';
        exchangeRateWrapper.appendChild(infoDiv);
        
        // Keep track of user edits
        exchangeRateInput.addEventListener('input', function() {
            // Mark that the user has manually edited the rate
            userEditedRate = true;
            infoDiv.textContent = 'You have manually edited this rate. It will be saved as your preference.';
            infoDiv.style.display = 'block';
        });
        
        // Update all currency code spans when currency changes and automatically fetch the exchange rate
        currencySelect.addEventListener('change', function() {
            const selectedCurrency = currencySelect.value;
            
            // Update currency code display
            currencyCodeSpans.forEach(span => {
                span.textContent = selectedCurrency;
            });
            
            // Don't reset userEditedRate flag when switching back to the same currency
            if (lastCurrency !== selectedCurrency) {
                userEditedRate = false;
            }
            
            // Automatically fetch the exchange rate when currency changes
            if (selectedCurrency === 'USD') {
                // For USD, set rate to 1 directly
                exchangeRateInput.value = 1;
                infoDiv.textContent = 'USD is the base currency with exchange rate 1.0';
                infoDiv.style.display = 'block';
            } else {
                // For other currencies, fetch the rate
                fetchExchangeRate();
            }
            
            // Remember the last selected currency
            lastCurrency = selectedCurrency;
        });
        
        // Function to fetch the current exchange rate
        function fetchExchangeRate(forceRefresh = false) {
            const selectedCurrency = currencySelect.value;
            
            // If it's USD, just set to 1 and return
            if (selectedCurrency === 'USD') {
                exchangeRateInput.value = 1;
                return;
            }
            
            // Skip fetch if user edited the rate (unless force refresh is requested)
            if (userEditedRate && !forceRefresh) {
                return;
            }
            
            // Prevent multiple simultaneous fetches
            if (fetchingRate) return;
            fetchingRate = true;
            
            // Add a subtle loading indicator to the exchange rate input
            const originalValue = exchangeRateInput.value;
            exchangeRateInput.disabled = true;
            infoDiv.textContent = 'Fetching current exchange rate...';
            infoDiv.style.display = 'block';
            
            // Include force_refresh parameter if requested
            const url = forceRefresh 
                ? `/landlord/profile/currency/fetch-rate?currency_code=${selectedCurrency}&force_refresh=1` 
                : `/landlord/profile/currency/fetch-rate?currency_code=${selectedCurrency}`;
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Network response was not ok: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update the input field with the fetched rate (formatted to 2 decimal places)
                        exchangeRateInput.value = parseFloat(data.rate).toFixed(2);
                        
                        // Show source of exchange rate
                        if (data.is_stored_preference) {
                            infoDiv.textContent = 'Using your saved exchange rate preference';
                        } else {
                            infoDiv.textContent = 'Using current market exchange rate (you can edit if needed)';
                        }
                    } else {
                        // On error, revert to original value
                        exchangeRateInput.value = originalValue;
                        infoDiv.textContent = `Could not fetch rate for ${selectedCurrency}. Using previous value.`;
                        console.error(`Could not fetch rate for ${selectedCurrency}`);
                    }
                })
                .catch(error => {
                    console.error('Error fetching exchange rate:', error);
                    exchangeRateInput.value = originalValue;
                    infoDiv.textContent = 'Error fetching exchange rate. Using previous value.';
                })
                .finally(() => {
                    exchangeRateInput.disabled = false;
                    fetchingRate = false;
                });
        }
        
        // Add a utility function to refresh rate from API (for use with refresh button if needed)
        window.refreshExchangeRate = function() {
            userEditedRate = false; // Reset user edited flag
            fetchExchangeRate(true); // Force refresh from API
        };
        
        // Initial check to update status message
        if (currencySelect.value === 'USD') {
            infoDiv.textContent = 'USD is the base currency with exchange rate 1.0';
            infoDiv.style.display = 'block';
        }
    }
});
