@extends('backends.layouts.app')

@section('title', 'Create Invoice | RoomGate')

@push('style')
    <style>
        .invoice-type-card {
            cursor: pointer;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .invoice-type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .form-section {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl">
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0" id="page-title">Select Invoice Type</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Boron</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Invoices</a></li>
                    <li class="breadcrumb-item active" id="breadcrumb-active">Select Type</li>
                </ol>
            </div>
        </div>

        <div id="invoice-type-selection" class="row mt-4">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card invoice-type-card h-100" data-form="full-invoice-form">
                    <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                        <i class="ti ti-file-invoice fs-1 text-primary mb-3"></i>
                        <h5 class="card-title">Create Full Invoice</h5>
                        <p class="card-text text-muted">Includes both room rent and all utility charges.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card invoice-type-card h-100" data-form="rent-invoice-form">
                    <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                        <i class="ti ti-home-dollar fs-1 text-success mb-3"></i>
                        <h5 class="card-title">Create Rent Invoice</h5>
                        <p class="card-text text-muted">An invoice that only includes the monthly room rent.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card invoice-type-card h-100" data-form="utility-invoice-form">
                    <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                        <i class="ti ti-bolt fs-1 text-info mb-3"></i>
                        <h5 class="card-title">Create Utility Invoice</h5>
                        <p class="card-text text-muted">An invoice for charges like electricity, water, etc.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="invoice-form-container">
            <div id="full-invoice-form" class="form-section">
                @include('backends.dashboard.payments.partials.invoice-form', ['type' => 'full'])
            </div>
            <div id="rent-invoice-form" class="form-section">
                @include('backends.dashboard.payments.partials.invoice-form', ['type' => 'rent'])
            </div>
            <div id="utility-invoice-form" class="form-section">
                @include('backends.dashboard.payments.partials.invoice-form', ['type' => 'utility'])
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('backends.dashboard.payments.partials.format-money-js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | PART 1: VIEW MANAGEMENT
            |--------------------------------------------------------------------------
            */
            const cardSelectionView = document.getElementById('invoice-type-selection');
            const formContainerView = document.getElementById('invoice-form-container');
            const pageTitle = document.getElementById('page-title');
            const breadcrumbActive = document.getElementById('breadcrumb-active');
            const allForms = document.querySelectorAll('.form-section');
            const selectionCards = document.querySelectorAll('.invoice-type-card');
            const backButtons = document.querySelectorAll('.back-to-selection-btn');

            const showForm = (formId) => {
                cardSelectionView.style.display = 'none';
                allForms.forEach(form => form.style.display = 'none');
                const formToShow = document.getElementById(formId);
                if (formToShow) {
                    formToShow.style.display = 'block';
                    formContainerView.style.display = 'block';
                    let title = 'Create Invoice';
                    if (formId.includes('rent')) title = 'Create Rent Invoice';
                    if (formId.includes('utility')) title = 'Create Utility Invoice';
                    if (formId.includes('full')) title = 'Create Full Invoice';
                    pageTitle.textContent = title;
                    breadcrumbActive.textContent = title;
                }
            };

            const showCardSelection = () => {
                formContainerView.style.display = 'none';
                cardSelectionView.style.display = 'flex';
                pageTitle.textContent = 'Select Invoice Type';
                breadcrumbActive.textContent = 'Select Type';
            };

            selectionCards.forEach(card => card.addEventListener('click', function () { showForm(this.getAttribute('data-form')); }));
            backButtons.forEach(button => button.addEventListener('click', showCardSelection));

            /*
            |--------------------------------------------------------------------------
            | PART 2: DYNAMIC FORM POPULATION (AJAX)
            |--------------------------------------------------------------------------
            */
            const allContractSelects = document.querySelectorAll('.contract-select');
            allContractSelects.forEach(select => {
                select.addEventListener('change', function () {
                    const contractId = this.value;
                    // Find the parent form for the currently visible select menu
                    const parentForm = this.closest('form');
                    if (!contractId) return;
                    fetchContractDetails(`/landlord/payments/get-contract-details/${contractId}`, parentForm);
                });
            });

            function populateMobileList(listContainer, data) {
                listContainer.innerHTML = ''; // Clear previous items
                const listGroup = document.createElement('ul');
                listGroup.className = 'list-group list-group-flush';

                let itemCounter = 1;
                
                // Get the currency symbol from user preferences
                const currencySymbol = '{{ auth()->user()->currency_code ?: "$" }}';

                // Add Room Rent Item
                if (data.base_price) {
                    const basePrice = parseFloat(data.base_price) || 0;
                    const amenitiesPrice = (data.amenities || []).reduce((total, amenity) => total + (parseFloat(amenity.amenity_price) || 0), 0);
                    const finalRent = basePrice + amenitiesPrice;

                    const rentItem = document.createElement('li');
                    rentItem.className = 'list-group-item px-0';
                    
                    // Format the money using our helper function
                    formatMoney(finalRent).then(formattedAmount => {
                        rentItem.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Room Rent</h6>
                                <strong class="text-nowrap ms-3">${formattedAmount}</strong>
                            </div>
                            <small class="text-muted">Base price + amenities</small>
                        `;
                    }).catch(err => {
                        rentItem.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Room Rent</h6>
                                <strong class="text-nowrap ms-3">${currencySymbol}${finalRent.toFixed(2)}</strong>
                            </div>
                            <small class="text-muted">Base price + amenities</small>
                        `;
                    });
                    
                    listGroup.appendChild(rentItem);
                }

                // Add Utility Items
                (data.utility_data || []).forEach(utility => {
                    const rate = parseFloat(utility.rate) || 0;
                    const consumption = parseFloat(utility.consumption) || 0;
                    const amount = rate * consumption;

                    const utilityItem = document.createElement('li');
                    utilityItem.className = 'list-group-item px-0';
                    
                    // Format the money using our helper function
                    formatMoney(amount).then(formattedAmount => {
                        utilityItem.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${utility.utility_type.name}</h6>
                                <strong class="text-nowrap ms-3">${formattedAmount}</strong>
                            </div>
                            <small class="text-muted">
                                Consumption: ${consumption} at ${currencySymbol}${rate.toFixed(2)} each
                            </small>
                        `;
                    }).catch(err => {
                        utilityItem.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${utility.utility_type.name}</h6>
                                <strong class="text-nowrap ms-3">${currencySymbol}${amount.toFixed(2)}</strong>
                            </div>
                            <small class="text-muted">
                                Consumption: ${consumption} at ${currencySymbol}${rate.toFixed(2)} each
                            </small>
                        `;
                    });
                    
                    listGroup.appendChild(utilityItem);
                });

                if (listGroup.children.length === 0) {
                    listContainer.innerHTML = '<p class="text-muted text-center m-0">No invoice items for this selection.</p>';
                } else {
                    listContainer.appendChild(listGroup);
                }
            }


            // 🔄 2. UPDATE THE fetchContractDetails FUNCTION
            function fetchContractDetails(url, parentForm) {
                const formType = parentForm.closest('.form-section').id.split('-')[0];

                // --- Select BOTH the desktop table and the new mobile list container ---
                const desktopTableBody = parentForm.querySelector(`.invoice-items-body[data-type="${formType}"]`);
                const mobileListContainer = parentForm.querySelector('.invoice-items-list-mobile');

                // Show loading states
                if (desktopTableBody) desktopTableBody.innerHTML = `<tr><td colspan="5" class="text-center p-4"><div class="spinner-border spinner-border-sm"></div></td></tr>`;
                if (mobileListContainer) mobileListContainer.innerHTML = `<p class="text-muted text-center m-0"><div class="spinner-border spinner-border-sm"></div></p>`;
                // ... other loading state code ...

                fetch(url)
                    .then(response => response.ok ? response.json() : Promise.reject('Network error'))
                    .then(data => {
                        // --- Update shared inputs (room number, amenities, etc.) ---
                        parentForm.querySelectorAll('.room-number-input').forEach(input => input.value = data.room_number);
                        parentForm.querySelectorAll('.amenities-display').forEach(display => populateAmenities(display, data.amenities));

                        // --- Populate the correct view ---
                        // If the desktop table exists, populate it
                        if (desktopTableBody) {
                            populateTable(desktopTableBody, data, formType);
                        }
                        // If the mobile list container exists, populate it
                        if (mobileListContainer) {
                            // Decide what data to show based on form type for mobile
                            let mobileData = {};
                            if (formType === 'full') mobileData = data;
                            if (formType === 'rent') mobileData = { base_price: data.base_price, amenities: data.amenities };
                            if (formType === 'utility') mobileData = { utility_data: data.utility_data };

                            populateMobileList(mobileListContainer, mobileData);
                        }

                        // --- Final calculations ---
                        parentForm.querySelectorAll('.utility-row').forEach(updateUtilityRowAmount);
                        updateInvoiceSummary(parentForm);
                    })
                    .catch(error => {
                        console.error('Error fetching contract details:', error);
                        // Update all views with the error message
                        tableBodies.forEach(body => body.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Failed to load details.</td></tr>`);
                        amenitiesDisplays.forEach(display => display.innerHTML = '<small class="text-danger">Failed to load amenities.</small>');
                    });
            }
            function populateAmenities(displayElement, amenities) {
                displayElement.innerHTML = '';
                // Get the currency code and exchange rate from user preferences
                const currencyCode = '{{ auth()->user()->currency_code ?: "USD" }}';
                const exchangeRate = {{ auth()->user()->exchange_rate ?: 1 }};
                
                if (amenities && amenities.length > 0) {
                    amenities.forEach(amenity => {
                        const amenityEl = document.createElement('div');
                        amenityEl.className = 'd-flex justify-content-between align-items-center mb-1';
                        
                        // Convert the price from USD to the user's currency
                        const basePrice = parseFloat(amenity.amenity_price) || 0;
                        const convertedPrice = basePrice * exchangeRate;
                        
                        // Format the price appropriately based on currency
                        let formattedPrice;
                        if (currencyCode === 'KHR') {
                            // For KHR, show whole numbers without decimals
                            formattedPrice = `${currencyCode}${Math.round(convertedPrice)}`;
                        } else {
                            // For other currencies, show with 2 decimal places
                            formattedPrice = `${currencyCode}${convertedPrice.toFixed(2)}`;
                        }
                        
                        amenityEl.innerHTML = `<span><i class="ti ti-check text-success me-2"></i>${amenity.name}</span><span class="fw-bold">${formattedPrice}</span>`;
                        displayElement.appendChild(amenityEl);
                    });
                } else {
                    displayElement.innerHTML = '<small class="text-muted">No extra amenities for this room.</small>';
                }
            }

            function populateTable(tableBody, data, formType) {
                tableBody.innerHTML = '';
                let itemCounter = 1;

                if (formType === 'full' || formType === 'rent') {
                    // Get base values in USD
                    const basePrice = parseFloat(data.base_price) || 0;
                    const amenitiesPrice = (data.amenities || []).reduce((total, amenity) => total + (parseFloat(amenity.amenity_price) || 0), 0);
                    const finalRent = basePrice + amenitiesPrice;

                    // Get the currency code and exchange rate from user preferences
                    const currencyCode = '{{ auth()->user()->currency_code ?: "USD" }}';
                    const exchangeRate = {{ auth()->user()->exchange_rate ?: 1 }};
                    
                    // Convert prices to user's currency
                    const convertedBasePrice = basePrice * exchangeRate;
                    const convertedFinalRent = finalRent * exchangeRate;
                    
                    // Format values based on currency
                    let formattedBasePrice, formattedFinalRent;
                    if (currencyCode === 'KHR') {
                        formattedBasePrice = Math.round(convertedBasePrice);
                        formattedFinalRent = Math.round(convertedFinalRent);
                    } else {
                        formattedBasePrice = convertedBasePrice.toFixed(2);
                        formattedFinalRent = convertedFinalRent.toFixed(2);
                    }

                    const rentRow = tableBody.insertRow();
                    rentRow.className = 'rent-row invoice-line-item';
                    // ✨ Set description and sub-description separately
                    rentRow.dataset.type = 'rent';
                    rentRow.dataset.description = 'Room Rent';
                    rentRow.dataset.subDescription = '(Base + amenities)'; // New attribute
                    rentRow.dataset.amount = finalRent.toFixed(2); // Keep original USD amount for calculations
                    rentRow.dataset.convertedAmount = formattedFinalRent; // Store converted amount

                    rentRow.innerHTML = `
                <th>${String(itemCounter++).padStart(2, '0')}</th>
                <td class="text-start">
                    <h6 class="mb-0">Room Rent</h6>
                    <p class="text-muted mb-0 small">(Base + amenities)</p>
                </td>
                <td><input type="text" class="form-control text-center" value="1" readonly></td>
                <td><div class="input-group">
                    <input type="text" class="form-control room-unit-price-input" value="${formattedBasePrice}" readonly>
                    <span class="input-group-text">${currencyCode}</span>
                </div></td>
                <td class="text-end"><div class="input-group">
                    <input type="text" class="form-control room-amount-input text-end" value="${formattedFinalRent}" readonly>
                    <span class="input-group-text">${currencyCode}</span>
                </div></td>`;
                }

                if (formType === 'full' || formType === 'utility') {
                    // Get the currency code and exchange rate from user preferences
                    const currencyCode = '{{ auth()->user()->currency_code ?: "USD" }}';
                    const exchangeRate = {{ auth()->user()->exchange_rate ?: 1 }};
                    
                    (data.utility_data || []).forEach(utility => {
                        // Calculate base values in USD
                        const baseRate = parseFloat(utility.rate) || 0;
                        const consumption = parseFloat(utility.consumption) || 0;
                        const amount = consumption * baseRate;
                        
                        // Convert to user's currency
                        const convertedRate = baseRate * exchangeRate;
                        const convertedAmount = amount * exchangeRate;
                        
                        // Format based on currency
                        let formattedRate, formattedAmount;
                        if (currencyCode === 'KHR') {
                            formattedRate = Math.round(convertedRate);
                            formattedAmount = Math.round(convertedAmount);
                        } else {
                            formattedRate = convertedRate.toFixed(2);
                            formattedAmount = convertedAmount.toFixed(2);
                        }
                        
                        const utilityRow = tableBody.insertRow();
                        utilityRow.className = 'utility-row invoice-line-item';
                        utilityRow.dataset.type = 'utility';
                        utilityRow.dataset.description = utility.utility_type.name;
                        utilityRow.dataset.utilityTypeId = utility.utility_type.id;
                        utilityRow.dataset.startReading = utility.start_reading;
                        utilityRow.dataset.endReading = utility.end_reading;
                        utilityRow.dataset.consumption = utility.consumption;
                        utilityRow.dataset.rate = utility.rate; // Keep original USD rate
                        utilityRow.dataset.amount = amount.toFixed(2); // Keep original USD amount
                        utilityRow.dataset.convertedRate = formattedRate; // Store converted rate
                        utilityRow.dataset.convertedAmount = formattedAmount; // Store converted amount
                        
                        utilityRow.innerHTML = `
                    <th>${String(itemCounter++).padStart(2, '0')}</th>
                    <td class="text-start"><input type="text" class="form-control utility-detail-input" value="${utility.utility_type.name}" readonly></td>
                    <td><input type="number" class="form-control utility-qty-input text-center" value="${utility.consumption}" min="0" readonly></td>
                    <td><div class="input-group">
                        <input type="text" class="form-control utility-price-input" value="${formattedRate}" readonly>
                        <span class="input-group-text">${currencyCode}</span>
                    </div></td>
                    <td class="text-end"><div class="input-group">
                        <input type="text" class="form-control utility-amount-input text-end" value="${formattedAmount}" placeholder="0.00" readonly>
                        <span class="input-group-text">${currencyCode}</span>
                    </div></td>`;
                    });
                }
            }

            /*
            |--------------------------------------------------------------------------
            | PART 3: REAL-TIME CALCULATIONS & SUMMARY UPDATES
            |--------------------------------------------------------------------------
            */
            document.addEventListener('input', function (e) {
                if (e.target.matches('.utility-qty-input, .discount-input')) {
                    const form = e.target.closest('form');
                    if (e.target.matches('.utility-qty-input')) {
                        updateUtilityRowAmount(e.target.closest('tr'));
                    }
                    updateInvoiceSummary(form);
                }
            });

            function updateUtilityRowAmount(row) {
                const qty = parseFloat(row.querySelector('.utility-qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.utility-price-input').value) || 0;
                const amountInput = row.querySelector('.utility-amount-input');
                
                // Get currency information
                const currencyCode = '{{ auth()->user()->currency_code ?: "USD" }}';
                
                // Calculate amount and format based on currency
                let formattedAmount;
                const amount = qty * price;
                
                if (currencyCode === 'KHR') {
                    formattedAmount = Math.round(amount);
                } else {
                    formattedAmount = amount.toFixed(2);
                }
                
                if (amountInput) amountInput.value = formattedAmount;
                
                // Update dataset values
                row.dataset.consumption = qty;
                row.dataset.convertedRate = price;
                row.dataset.convertedAmount = formattedAmount;
            }

            function updateInvoiceSummary(form) {
                if (!form) return;
                let subtotal = 0;

                // Get currency info
                const currencyCode = '{{ auth()->user()->currency_code ?: "USD" }}';
                const exchangeRate = {{ auth()->user()->exchange_rate ?: 1 }};

                // Calculate subtotal using DATASET values (original USD amounts)
                // The dataset.amount contains the original USD value, not the converted display value
                const rentRow = form.querySelector('.rent-row');
                if (rentRow) {
                    subtotal += parseFloat(rentRow.dataset.amount) || 0;
                }

                form.querySelectorAll('.utility-row').forEach(row => {
                    subtotal += parseFloat(row.dataset.amount) || 0;
                });

                const summaryWrapper = form.querySelector('.invoice-summary-wrapper');
                if (!summaryWrapper) return;

                const discountInput = summaryWrapper.querySelector('.discount-input');
                const discountAmountEl = summaryWrapper.querySelector('.discount-display');
                const subtotalEl = summaryWrapper.querySelector('.subtotal-display');
                const totalEl = summaryWrapper.querySelector('.total-display');

                const discountPercent = parseFloat(discountInput.value) || 0;
                const discountAmount = subtotal * (discountPercent / 100);
                const total = subtotal - discountAmount;
                
                // Format numbers appropriately for the current currency
                let formattedSubtotal, formattedDiscount, formattedTotal;
                
                if (currencyCode === 'KHR') {
                    // For KHR, convert and round to whole numbers
                    formattedSubtotal = Math.round(subtotal * exchangeRate) + '៛ ' + currencyCode;
                    formattedDiscount = Math.round(discountAmount * exchangeRate) + '៛ ' + currencyCode;
                    formattedTotal = Math.round(total * exchangeRate) + '៛ ' + currencyCode;
                } else {
                    // For other currencies, use 2 decimal places
                    formattedSubtotal = currencyCode + ' ' + (subtotal * exchangeRate).toFixed(2);
                    formattedDiscount = currencyCode + ' ' + (discountAmount * exchangeRate).toFixed(2);
                    formattedTotal = currencyCode + ' ' + (total * exchangeRate).toFixed(2);
                }
                
                // Update the display
                if (subtotalEl) subtotalEl.textContent = formattedSubtotal;
                if (discountAmountEl) discountAmountEl.textContent = '-' + formattedDiscount;
                if (totalEl) totalEl.textContent = formattedTotal;
            }

            /*
            |--------------------------------------------------------------------------
            | PART 4: INVOICE PREVIEW GENERATION
            |--------------------------------------------------------------------------
            */
            const logoUrl = "{{ asset('assets/images/logo-dark.png') }}";
            // Use the landlord's custom QR codes if available
            const hasQrCode1 = {{ isset($qrCode1) ? 'true' : 'false' }};
            const hasQrCode2 = {{ isset($qrCode2) ? 'true' : 'false' }};
            const qrCode1Url = "{{ $qrCode1 ?? asset('assets/images/qr1.jpg') }}";
            const qrCode2Url = "{{ $qrCode2 ?? asset('assets/images/qr2.jpg') }}";

            // --- THIS IS THE FIX ---
            // The 'previewButtons' variable must be declared before it is used.
            const previewButtons = document.querySelectorAll('.preview-btn');

            previewButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    generateInvoicePreview(this);
                });
            });

            function generateInvoicePreview(clickedButton) {
                const activeForm = clickedButton.closest('form');
                if (!activeForm) return;

                const invoiceNumber = activeForm.querySelector('.invoice-no-input')?.value || 'N/A';
                const roomNumber = activeForm.querySelector('.room-number-input')?.value;
                const issueDate = activeForm.querySelector('.issue-date-input')?.value;
                const dueDate = activeForm.querySelector('.due-date-input')?.value;
                const selectedContractOption = activeForm.querySelector('.contract-select')?.options[activeForm.querySelector('.contract-select').selectedIndex];
                const tenantName = selectedContractOption ? selectedContractOption.text.split(' - ')[1] : 'Tenant';

                // Get currency information for formatting
                const currencyCode = '{{ auth()->user()->currency_code ?: "USD" }}';
                const exchangeRate = {{ auth()->user()->exchange_rate ?: 1 }};
                
                let itemsHtml = '';
                let itemCounter = 1;
                let subtotalUSD = 0;

                activeForm.querySelectorAll('.invoice-line-item').forEach(itemElement => {
                    // ✨ Check for sub-description and build the two-line detail
                    let detailHtml = itemElement.dataset.description;
                    if (itemElement.dataset.subDescription) {
                        detailHtml += `<br><small class="text-muted">${itemElement.dataset.subDescription}</small>`;
                    }

                    const qty = itemElement.dataset.type === 'rent' ? 1 : itemElement.dataset.consumption;
                    
                    // Get the original USD values
                    const originalPrice = itemElement.dataset.type === 'rent' ? 
                        itemElement.dataset.amount : 
                        itemElement.dataset.rate;
                    const originalAmount = parseFloat(itemElement.dataset.amount) || 0;
                    
                    // Add to subtotal in USD
                    subtotalUSD += originalAmount;
                    
                    // Convert values based on currency
                    let displayPrice, displayAmount;
                    if (currencyCode === 'KHR') {
                        displayPrice = Math.round(parseFloat(originalPrice) * exchangeRate);
                        displayAmount = Math.round(originalAmount * exchangeRate);
                    } else {
                        displayPrice = (parseFloat(originalPrice) * exchangeRate).toFixed(2);
                        displayAmount = (originalAmount * exchangeRate).toFixed(2);
                    }
                    
                    itemsHtml += `
                <tr>
                    <th scope="row">${String(itemCounter++).padStart(2, '0')}</th>
                    <td class="text-start">${detailHtml}</td>
                    <td>${qty}</td>
                    <td>${displayPrice} ${currencyCode}</td>
                    <td class="text-end">${displayAmount} ${currencyCode}</td>
                </tr>`;
                });

                // Calculate discount and total
                const discountPercent = parseFloat(activeForm.querySelector('.discount-input')?.value) || 0;
                const discountAmountUSD = subtotalUSD * (discountPercent / 100);
                const totalUSD = subtotalUSD - discountAmountUSD;
                
                // Format totals based on currency
                let formattedSubtotal, formattedDiscount, formattedTotal;
                if (currencyCode === 'KHR') {
                    formattedSubtotal = Math.round(subtotalUSD * exchangeRate) + ' ' + currencyCode;
                    formattedDiscount = Math.round(discountAmountUSD * exchangeRate) + ' ' + currencyCode;
                    formattedTotal = Math.round(totalUSD * exchangeRate) + ' ' + currencyCode;
                } else {
                    formattedSubtotal = (subtotalUSD * exchangeRate).toFixed(2) + ' ' + currencyCode;
                    formattedDiscount = (discountAmountUSD * exchangeRate).toFixed(2) + ' ' + currencyCode;
                    formattedTotal = (totalUSD * exchangeRate).toFixed(2) + ' ' + currencyCode;
                }
                
                const subtotalText = formattedSubtotal;
                const discountText = formattedDiscount;
                const totalText = formattedTotal;

                // ✨ This is the original compact HTML template with the updated final total row
                const invoiceHtml = `
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Invoice #${invoiceNumber}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: sans-serif; -webkit-print-color-adjust: exact; background-color: #fff !important; }
                    .invoice-items-table .totals-row td, .invoice-items-table .final-total-row th, .invoice-items-table .final-total-row td { border: none; padding-top: 0.5rem; padding-bottom: 0.5rem; }
                    .invoice-items-table .totals-row td:nth-last-child(-n+2) { border-top: 1px solid #e9ecef; }
                    .invoice-items-table .final-total-row th, .invoice-items-table .final-total-row td { border-top: 2px solid #212529; border-bottom: 2px solid #212529; }
                </style>
            </head>
            <body>
                <div class="container py-4">
                    <div class="row align-items-center mb-4"><div class="col-6"><img src="${logoUrl}" height="60" alt="Logo"></div><div class="col-6 text-end"><h6>Invoice #${invoiceNumber}</h6></div></div><hr>
                    <div class="row mb-4"><div class="col-6"><p class="fw-bold">Bill To:</p><p>${tenantName}<br>Room ${roomNumber}<br>Phnom Penh</p></div><div class="col-6 text-end"><p><span class="fw-bold">Invoice Date:</span> ${issueDate}</p><p><span class="fw-bold">Due Date:</span> ${dueDate}</p></div></div>
                    <table class="table invoice-items-table text-center table-nowrap align-middle mb-0">
                        <thead class="bg-light bg-opacity-50"><tr><th style="width:50px;">#</th><th class="text-start">Item Details</th><th>Quantity</th><th>Unit Price</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            ${itemsHtml}
                            <tr class="totals-row"><td colspan="3"></td><td class="text-end">Subtotal</td><td class="text-end">${subtotalText}</td></tr>
                            <tr class="totals-row"><td colspan="3"></td><td class="text-end">Discount</td><td class="text-end">${discountText}</td></tr>
                            <tr class="final-total-row fs-4 fw-bold"><th colspan="3"></th><th class="text-end">Total Amount</th><th class="text-end">${totalText}</th></tr>
                        </tbody>
                    </table>
                    <div class="text-center mt-5">
                        ${hasQrCode1 ? `<img src="${qrCode1Url}" height="100" class="mx-2" alt="Payment QR Code">` : ''}
                        ${hasQrCode2 ? `<img src="${qrCode2Url}" height="100" class="mx-2" alt="Payment QR Code">` : ''}
                        ${(!hasQrCode1 && !hasQrCode2) ? '<p class="text-muted">No payment QR codes available</p>' : ''}
                    </div>
                </div>
            </body>
            </html>`;

                // (The iframe logic remains the same)
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                document.body.appendChild(iframe);
                iframe.contentDocument.write(invoiceHtml);
                iframe.contentDocument.close();
                iframe.onload = function () {
                    iframe.contentWindow.print();
                    document.body.removeChild(iframe);
                };
            }

            // Initialize the page
            showCardSelection();
        });


        /*
    |--------------------------------------------------------------------------
    | PART 5: FORM SUBMISSION
    |--------------------------------------------------------------------------
    */
        const allForms = document.querySelectorAll('form[id^="invoice-form-"]');

        allForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent the browser's default submission
                submitInvoiceData(form);
            });
        });

        function prepareFormData(form) {
            const formData = {
                items: []
            };

            // 1. Get static invoice data
            formData.contract_id = form.querySelector('.contract-select').value;
            formData.invoice_number = form.querySelector('.invoice-no-input').value;
            formData.issue_date = form.querySelector('.issue-date-input').value;
            formData.due_date = form.querySelector('.due-date-input').value;
            formData.discount = form.querySelector('.discount-input').value || 0;

            // 2. Get line items from the data attributes we added
            form.querySelectorAll('.invoice-line-item').forEach(itemElement => {
                const itemData = {
                    type: itemElement.dataset.type,
                    description: itemElement.dataset.description,
                    amount: itemElement.dataset.amount,
                };

                // If it's a utility, add the extra details
                if (itemData.type === 'utility') {
                    itemData.utility_type_id = itemElement.dataset.utilityTypeId;
                    itemData.consumption = itemElement.dataset.consumption;
                    itemData.rate = itemElement.dataset.rate;
                    itemData.start_reading = itemElement.dataset.startReading;
                    itemData.end_reading = itemElement.dataset.endReading;
                }

                formData.items.push(itemData);
            });

            return formData;
        }

        async function submitInvoiceData(form) {
            const formData = prepareFormData(form);
            console.log('Data to be sent:', formData);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonHtml = submitButton.innerHTML;

            // Enhanced validation
            if (!formData.contract_id) {
                Swal.fire({
                    position: "top-end",
                    title: 'Missing Information',
                    text: 'Please select a contract.',
                    width: 500,
                    padding: 30,
                    background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-4.jpg') }}) no-repeat center",
                    showConfirmButton: true,
                    customClass: {
                        title: 'swal-title-error'
                    }
                });
                return;
            }
            
            if (!formData.items || formData.items.length === 0) {
                Swal.fire({
                    position: "top-end",
                    title: 'No Invoice Items',
                    text: 'No invoice items found. Please select a contract to load items.',
                    width: 500,
                    padding: 30,
                    background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-4.jpg') }}) no-repeat center",
                    showConfirmButton: true,
                    customClass: {
                        title: 'swal-title-error'
                    }
                });
                return;
            }
            
            // Validate each item has required fields
            const missingFields = [];
            formData.items.forEach((item, index) => {
                if (!item.type) missingFields.push(`Item #${index+1} is missing type`);
                if (!item.description) missingFields.push(`Item #${index+1} is missing description`);
                if (!item.amount) missingFields.push(`Item #${index+1} is missing amount`);
                
                // For utility items, check additional required fields
                if (item.type === 'utility') {
                    if (!item.utility_type_id) missingFields.push(`Utility item #${index+1} is missing utility type`);
                    if (!item.consumption) missingFields.push(`Utility item #${index+1} is missing consumption`);
                }
            });
            
            if (missingFields.length > 0) {
                Swal.fire({
                    position: "top-end",
                    title: 'Validation Failed',
                    html: 'Please fix the following issues:<br>' + missingFields.join('<br>'),
                    width: 500,
                    padding: 30,
                    background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-4.jpg') }}) no-repeat center",
                    showConfirmButton: true,
                    customClass: {
                        title: 'swal-title-error'
                    }
                });
                return;
            }

            // Disable button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...`;

            try {
                const response = await fetch("{{ route('landlord.payments.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                let result;
                try {
                    // Try to parse the response as JSON
                    result = await response.json();
                } catch (parseError) {
                    console.error('JSON parsing error:', parseError);
                    
                    // Get the text response instead
                    const textResponse = await response.text();
                    console.error('Raw response:', textResponse);
                    
                    if (textResponse.includes('invoice number') || textResponse.includes('invoice_number')) {
                        // This is likely an invoice number uniqueness error
                        Swal.fire({
                            position: "top-end",
                            title: 'Invoice Number Error',
                            text: 'This invoice number has already been used. Please refresh the page to get a new invoice number.',
                            width: 500,
                            padding: 30,
                            background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-4.jpg') }}) no-repeat center",
                            showConfirmButton: true,
                            customClass: {
                                title: 'swal-title-error'
                            }
                        });
                    } else {
                        Swal.fire({
                            position: "top-end",
                            title: 'Server Error',
                            text: 'The server returned an unexpected response. Please try again or contact support.',
                            width: 500,
                            padding: 30,
                            background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-4.jpg') }}) no-repeat center",
                            showConfirmButton: true,
                            customClass: {
                                title: 'swal-title-error'
                            }
                        });
                    }
                    return;
                }

                if (!response.ok) {
                    // Handle validation errors from the server
                    console.error('Server response:', result);
                    let errorMessage = result.message || 'An unknown error occurred.';
                    
                    if (result.errors) {
                        const errorDetails = [];
                        
                        // Check for specific error types
                        if (result.errors.invoice_number) {
                            errorDetails.push('Invoice number error: ' + result.errors.invoice_number[0]);
                        }
                        
                        if (result.errors.items) {
                            errorDetails.push('Items error: ' + result.errors.items[0]);
                        }
                        
                        // Check for nested item errors
                        Object.keys(result.errors).forEach(key => {
                            if (key.startsWith('items.')) {
                                const match = key.match(/items\.(\d+)\.(\w+)/);
                                if (match) {
                                    const [, index, field] = match;
                                    errorDetails.push(`Item #${parseInt(index)+1} ${field} error: ${result.errors[key][0]}`);
                                }
                            }
                        });
                        
                        // If we found specific errors, use those, otherwise use generic error list
                        if (errorDetails.length > 0) {
                            errorMessage = 'Please fix the following issues:<br>' + errorDetails.join('<br>');
                        } else {
                            // Fall back to generic error message
                            errorMessage += '<br>' + Object.entries(result.errors)
                                .map(([key, messages]) => `${key}: ${messages[0]}`)
                                .join('<br>');
                        }
                    }
                    
                    // Use SweetAlert for better error presentation
                    Swal.fire({
                        position: "top-end",
                        title: 'Invoice Creation Failed',
                        html: errorMessage,
                        width: 500,
                        padding: 30,
                        background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-4.jpg') }}) no-repeat center",
                        showConfirmButton: true,
                        customClass: {
                            title: 'swal-title-error'
                        }
                    });
                } else {
                    // Success! Show a nicer message with SweetAlert instead of basic alert
                    Swal.fire({
                        position: "top-end",
                        title: result.message || "Invoice created successfully!",
                        width: 500,
                        padding: 30,
                        background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-5.jpg') }}) no-repeat center",
                        showConfirmButton: false,
                        timer: 4000,
                        customClass: {
                            title: 'swal-title-success'
                        }
                    });
                    
                    // Add a small delay before redirecting to make sure the message is seen
                    setTimeout(() => {
                        window.location.href = result.redirect_url || "{{ route('landlord.payments.index') }}";
                    }, 1000);
                }

            } catch (error) {
                console.error('Submission error:', error);
                alert('A network error occurred. Please try again or refresh the page.');
            } finally {
                // Re-enable the button
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonHtml;
            }
        }
    </script>
@endpush