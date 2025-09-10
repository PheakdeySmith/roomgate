@extends('backends.layouts.app')

@section('title', 'Property Manager | RoomGate')

@push('style')
    {{--
    <link rel="stylesheet" href="{{ asset('assets/css/your-custom-styles.css') }}"> --}}
@endpush

@section('content')
    <div class="page-container">

        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Property Manager</h4>
            </div>
            <div class="text-end">
                {{-- This is the NEW and improved breadcrumb --}}
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('landlord.properties.show', $property->id) }}">{{ $property->name }}</a></li>
                    <li class="breadcrumb-item active" id="breadcrumb-active-tab">Overviews</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="d-flex">
                {{-- Left Sidebar / Tab Navigation --}}
                <div class="offcanvas-xl offcanvas-start file-manager" tabindex="-1" id="fileManagerSidebar"
                    aria-labelledby="fileManagerSidebarLabel">
                    <div class="d-flex flex-column">
                        <div class="py-2 px-3 flex-shrink-0 d-flex align-items-center gap-2 border-bottom border-dashed">
                            <div class="avatar-md">
                                <img src="{{ Auth::user()->image ? asset(Auth::user()->image) : asset('assets/images/default_image.png') }}"
                                    alt="" class="img-fluid rounded-circle">
                            </div>
                            <div>
                                <h5 class="mb-1 fs-16 fw-semibold">{{ Auth::user()->name }} <i
                                        class="ti ti-rosette-discount-check-filled text-success" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-title="Pro User"></i></h5>
                                <p class="fs-12 mb-0">Welcome!</p>
                            </div>
                            <button type="button" class="btn btn-sm btn-icon btn-soft-danger ms-auto d-xl-none"
                                data-bs-dismiss="offcanvas" data-bs-target="#fileManagerSidebar" aria-label="Close">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <div class="p-3">
                            <div class="d-flex flex-column">
                                <button type="button"
                                    class="btn fw-medium btn-success drop-arrow-none dropdown-toggle w-100 mb-3">
                                    Create New <i class="ti ti-plus ms-1"></i>
                                </button>

                                <div class="nav flex-column nav-pills file-menu" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">

                                    <a class="list-group-item active" id="v-pills-overviews-tab" data-bs-toggle="pill"
                                        href="#v-pills-overviews" role="tab" aria-controls="v-pills-overviews"
                                        aria-selected="true">
                                        <i class="ti ti-building-community fs-18 align-middle me-2"></i>Overviews
                                    </a>

                                    <a class="list-group-item" id="v-pills-all-rooms-tab" data-bs-toggle="pill"
                                        href="#v-pills-all-rooms" role="tab" aria-controls="v-pills-all-rooms"
                                        aria-selected="true">
                                        <i class="ti ti-building-community fs-18 align-middle me-2"></i>Rooms
                                    </a>

                                    <a class="list-group-item" id="v-pills-utilities-tab" data-bs-toggle="pill"
                                        href="#v-pills-utilities" role="tab" aria-controls="v-pills-utilities"
                                        aria-selected="false">
                                        <i class="ti ti-bolt fs-18 align-middle me-2"></i>Utilities
                                    </a>

                                    <a class="list-group-item" id="v-pills-contracts-tab" data-bs-toggle="pill"
                                        href="#v-pills-contracts" role="tab" aria-controls="v-pills-contracts"
                                        aria-selected="false">
                                        <i class="ti ti-file-text fs-18 align-middle me-2"></i>Contracts
                                    </a>

                                    <a class="list-group-item" id="v-pills-deleted-tab" data-bs-toggle="pill"
                                        href="#v-pills-deleted" role="tab" aria-controls="v-pills-deleted"
                                        aria-selected="false">
                                        <i class="ti ti-trash fs-18 align-middle me-2"></i>Deleted Items
                                    </a>
                                </div>

                                {{-- <div class="mt-5 pt-5">
                                    <div class="alert alert-secondary p-3 pt-0 text-center mb-0" role="alert">
                                        <img src="{{ asset('assets') }}/images/panda.svg" alt="" class="img-fluid mt-n5"
                                            style="max-width: 135px;">
                                        <div>
                                            <h5 class="alert-heading fw-semibold fs-18 mt-2">Get more space for files</h5>
                                            <p>We offer you unlimited storage space for all you needs</p>
                                            <a href="!" class="btn btn-secondary">Upgrade to Pro</a>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side / Tab Content Panes --}}
                <div class="w-100 border-start">
                    <div class="tab-content p-3" id="v-pills-tabContent">

                        <div class="tab-pane fade show active" id="v-pills-overviews" role="tabpanel"
                            aria-labelledby="v-pills-overviews-tab">

                            @include('backends.dashboard.properties.partials._overview_tab')

                        </div>

                        <div class="tab-pane fade" id="v-pills-all-rooms" role="tabpanel"
                            aria-labelledby="v-pills-all-rooms-tab">
                            @include('backends.dashboard.properties.partials._all_rooms_tab')
                        </div>

                        <div class="tab-pane fade" id="v-pills-utilities" role="tabpanel"
                            aria-labelledby="v-pills-utilities-tab">
                            @include('backends.dashboard.properties.partials._utilities_tab')
                        </div>

                        <div class="tab-pane fade" id="v-pills-contracts" role="tabpanel"
                            aria-labelledby="v-pills-contracts-tab">
                            @include('backends.dashboard.properties.partials._contracts_tab')
                        </div>

                        <div class="tab-pane fade" id="v-pills-deleted" role="tabpanel"
                            aria-labelledby="v-pills-deleted-tab">
                            @include('backends.dashboard.properties.partials._deleted_fields_tab')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- Your existing code for Select2 ---
            $(function () {
                $('#createModal #room_type_id').select2({
                    dropdownParent: $('#createModal'),
                    placeholder: "Select an option",
                    allowClear: true
                });
            });


            // --- Your existing code for remembering the active tab ---
            const tabTriggers = document.querySelectorAll('#v-pills-tab a');
            tabTriggers.forEach(triggerEl => {
                triggerEl.addEventListener('shown.bs.tab', event => {
                    localStorage.setItem('activePropertyTab', event.target.getAttribute('href'));
                });
            });

            const lastActiveTab = localStorage.getItem('activePropertyTab');
            if (lastActiveTab) {
                const tabToActivate = document.querySelector(`#v-pills-tab a[href="${lastActiveTab}"]`);
                if (tabToActivate) {
                    const tab = new bootstrap.Tab(tabToActivate);
                    tab.show();
                }
            }

            // --- NEW CODE TO CLOSE OFFCANVAS ON CLICK ---
            const sidebar = document.getElementById('fileManagerSidebar');
            if (sidebar) {
                const sidebarLinks = sidebar.querySelectorAll('.nav-pills a.list-group-item');
                const offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(sidebar);

                sidebarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        // Check if the offcanvas is currently shown (on mobile view)
                        if (sidebar.classList.contains('show')) {
                            offcanvasInstance.hide();
                        }
                    });
                });
            }
            // --- END OF NEW CODE ---
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Select the modal element
            const assignMeterModal = document.getElementById('assignMeterModal');

            // ADD THIS CHECK
            if (assignMeterModal) {
                // 2. Listen for the 'show.bs.modal' event, which fires just before the modal opens
                assignMeterModal.addEventListener('show.bs.modal', function(event) {

                    // 3. Get the button that triggered the modal
                    const button = event.relatedTarget;

                    // 4. Extract the room ID and number from the button's data attributes
                    const roomId = button.getAttribute('data-room-id');
                    const roomNumber = button.getAttribute('data-room-number');

                    // 5. Find the input fields inside the modal
                    const modalRoomNumberInput = assignMeterModal.querySelector('#modalRoomNumber');
                    const modalRoomIdInput = assignMeterModal.querySelector('#modalRoomId');

                    // 6. Set the values of the input fields
                    modalRoomNumberInput.value = roomNumber;
                    modalRoomIdInput.value = roomId;
                });
            } // END CHECK
        });

        document.addEventListener('DOMContentLoaded', function () {
            const editMeterModal = document.getElementById('editMeterModal');

            // Make sure the modal exists on the page before adding an event listener
            if (editMeterModal) {
                editMeterModal.addEventListener('show.bs.modal', function (event) {
                    // Get the button that triggered the modal
                    const button = event.relatedTarget;

                    // Extract data from the button's data-* attributes
                    const meterNumber = button.getAttribute('data-meter-number');
                    const utilityTypeId = button.getAttribute('data-utility-type-id');
                    const initialReading = button.getAttribute('data-initial-reading');
                    const installedAt = button.getAttribute('data-installed-at');
                    const updateUrl = button.getAttribute('data-update-url');

                    // Find the form and its input fields within the modal
                    const form = editMeterModal.querySelector('#editMeterForm');
                    const meterNumberInput = form.querySelector('#edit_meter_number');
                    const utilityTypeSelect = form.querySelector('#edit_utility_type');
                    const initialReadingInput = form.querySelector('#edit_initial_reading');
                    const installedAtInput = form.querySelector('#edit_installed_at');

                    // Set the form's action attribute to the correct update URL
                    form.setAttribute('action', updateUrl);

                    // Populate the form fields with the meter's data
                    meterNumberInput.value = meterNumber;
                    utilityTypeSelect.value = utilityTypeId;
                    initialReadingInput.value = initialReading;
                    installedAtInput.value = installedAt;
                });
            }
        });

        document.addEventListener('submit', function (e) {
            // Check if the submitted form has our target class
            const form = e.target.closest('.deactivate-meter-form');
            if (form) {
                // Prevent the form from submitting immediately
                e.preventDefault();

                const meterNumber = form.dataset.meterNumber || 'this meter';

                Swal.fire({
                    title: "Are you sure?",
                    text: `Meter #${meterNumber} will be deactivated. You can reactivate it later.`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, deactivate it!",
                    cancelButtonText: "No, cancel",
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    customClass: {
                        confirmButton: "swal2-confirm btn btn-danger me-2 mt-2",
                        cancelButton: "swal2-cancel btn btn-secondary mt-2",
                    },
                    buttonsStyling: false,
                }).then((result) => {
                    // If the user confirmed, submit the original form
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });
    </script>

    <script>
        // Wait for the entire page to be loaded before running the script
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Select the search input field
            const searchInput = document.getElementById('roomSearchInput');

            // ADD THIS CHECK
            if (searchInput) {
                // 2. Add an event listener that fires every time a key is released
                searchInput.addEventListener('keyup', function(event) {

                    // 3. Get the search term and convert it to lowercase for case-insensitive matching
                    const searchTerm = event.target.value.toLowerCase();

                    // 4. IMPORTANT: Find which tab pane is currently active
                    const activePane = document.querySelector('#v-pills-all-rooms .tab-pane.active') || document.querySelector('#v-pills-utilities .tab-pane.active');


                    // If an active pane is found, proceed with filtering
                    if (activePane) {

                        // 5. Select all the accordion items *only within that active pane*
                        const accordionItems = activePane.querySelectorAll('.accordion-item');

                        // 6. Loop through each accordion item to decide whether to show or hide it
                        accordionItems.forEach(function(item) {

                            // Get the searchable text from the accordion's button
                            const itemText = item.querySelector('.accordion-button').textContent.toLowerCase();

                            // 7. Check if the room's text includes the search term
                            if (itemText.includes(searchTerm)) {
                                // If it matches, make sure the item is visible
                                item.style.display = 'block';
                            } else {
                                // If it doesn't match, hide the item
                                item.style.display = 'none';
                            }
                        });
                    }
                });
            } // END CHECK
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Listen for form submissions on any form with the .ajax-form class
            document.body.addEventListener('submit', function (event) {
                if (event.target.matches('.ajax-form')) {
                    event.preventDefault(); // Stop the page from reloading

                    const form = event.target;
                    const url = form.action;
                    const formData = new FormData(form);
                    const submitButton = form.querySelector('button[type="submit"]');

                    if (submitButton) {
                        // Disable button and show a loading spinner
                        submitButton.disabled = true;
                        const originalButtonHtml = submitButton.innerHTML;
                        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> Saving...`;
                    }

                    // Send the data to the server in the background
                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    })
                        .then(response => {
                            // Check if the response is not ok (e.g., validation error 422)
                            if (!response.ok) {
                                return response.json().then(errorData => Promise.reject(errorData));
                            }
                            return response.json();
                        })
                        .then(data => {
                            // If the server confirms success, update the UI
                            if (data.success) {
                                updateMeterCardUI(form, data.reading); // Call the UI update function
                                form.reset(); // Clear the input field
                                
                                // Show success feedback
                                const formContainer = form.closest('.mb-4');
                                if (formContainer) {
                                    const successAlert = document.createElement('div');
                                    successAlert.className = 'alert alert-success alert-dismissible fade show mt-2';
                                    successAlert.innerHTML = `
                                        <strong>Success!</strong> Reading of ${data.reading.reading_value} saved successfully.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    `;
                                    formContainer.appendChild(successAlert);
                                    
                                    // Auto-remove after 3 seconds
                                    setTimeout(() => {
                                        successAlert.classList.remove('show');
                                        setTimeout(() => successAlert.remove(), 150);
                                    }, 3000);
                                }
                            }
                        })
                        .catch(error => {
                            // Handle any errors, including validation errors
                            const errorMessage = error?.errors?.reading_value?.[0] || error.message || 'An unknown error occurred.';
                            
                            // Show error in a more user-friendly way
                            const formContainer = form.closest('.mb-4');
                            if (formContainer) {
                                const errorAlert = document.createElement('div');
                                errorAlert.className = 'alert alert-danger alert-dismissible fade show mt-2';
                                errorAlert.innerHTML = `
                                    <strong>Error:</strong> ${errorMessage}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                `;
                                formContainer.appendChild(errorAlert);
                            } else {
                                alert('Error: ' + errorMessage);
                            }
                            
                            console.error('Submission Error:', error);
                        })
                        .finally(() => {
                            // Always re-enable the button
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = '<i class="ti ti-device-floppy"></i><span class="d-none d-sm-inline-block ms-1">Save</span>';
                            }
                        });
                }
            });
        });

        /**
         * IMPROVED FUNCTION WITH SAFETY CHECKS
         * It finds the parent meter card and updates ALL relevant elements within it.
         */
        function updateMeterCardUI(form, newReading) {
            // Start from the form that was submitted and find its parent .card
            const meterCard = form.closest('.card');
            if (!meterCard) {
                console.error('Could not find parent .card element for the form.');
                return;
            }

            try {
                // --- Data Preparation ---
                const readingDate = new Date(newReading.reading_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const unit = newReading?.meter?.utility_type?.unit_of_measure || '';
                const newReadingValue = parseFloat(newReading.reading_value).toFixed(2);

                // --- UI Updates ---

                // 1. Update all summary value elements (for both desktop and mobile)
                const readingValueEls = meterCard.querySelectorAll('.last-reading-value');
                if (readingValueEls && readingValueEls.length > 0) {
                    readingValueEls.forEach(el => {
                        if (el) {
                            // Check if we need to append the unit (new design has separate unit element)
                            if (el.nextElementSibling && el.nextElementSibling.classList.contains('text-muted')) {
                                el.textContent = newReadingValue;
                            } else {
                                el.textContent = `${newReadingValue} ${unit}`;
                            }
                        }
                    });
                }

                // 2. Update all summary date elements
                const readingDateEls = meterCard.querySelectorAll('.last-reading-date');
                if (readingDateEls && readingDateEls.length > 0) {
                    readingDateEls.forEach(el => {
                        if (el) {
                            // For the new design with badge dates
                            if (el.classList.contains('badge')) {
                                el.textContent = readingDate;
                            } else {
                                el.textContent = `on ${readingDate}`;
                            }
                        }
                    });
                }
                
                // Update the meter card appearance - remove warning styling
                meterCard.classList.remove('border-warning');
                const cardHeader = meterCard.querySelector('.card-header');
                if (cardHeader) {
                    cardHeader.classList.remove('bg-warning-subtle');
                }
                
                // 3. Check if all meters in the room have current readings
                // Find the accordion item (room) containing this meter card
                const accordionItem = meterCard.closest('.accordion-item');
                if (accordionItem) {
                    // Count all meter cards in this room
                    const allMeters = accordionItem.querySelectorAll('.card');
                    let allMetersRead = true;
                    
                    if (allMeters && allMeters.length > 0) {
                        // Check each meter to see if it has a recent reading date
                        allMeters.forEach(meter => {
                            if (meter === meterCard) {
                                // This meter was just read, so it's current
                                return;
                            }
                            
                            // For other meters, check their last reading date
                            const dateEl = meter.querySelector('.last-reading-date');
                            if (dateEl) {
                                const dateText = dateEl.textContent;
                                // Parse the date from badge or "on Sep 06, 2025" format
                                const dateStr = dateText.startsWith('on ') ? dateText.replace('on ', '') : dateText;
                                const parsedDate = new Date(dateStr);
                                const currentMonth = new Date().getMonth();
                                const currentYear = new Date().getFullYear();
                                
                                // Check if this meter's reading is not from the current month
                                if (parsedDate.getMonth() !== currentMonth || parsedDate.getFullYear() !== currentYear) {
                                    allMetersRead = false;
                                }
                            }
                        });
                    }
                    
                    // Update the room's status badge if all meters are now read
                    const statusBadge = accordionItem.querySelector('.badge');
                    if (statusBadge && allMetersRead) {
                        statusBadge.className = 'badge bg-success-subtle text-success ms-auto';
                        statusBadge.innerHTML = '<i class="ti ti-check me-1"></i>Recorded';
                    }
                }

                // 3. Update all history tables - handles both table and div-based layouts
                const historyTbodies = meterCard.querySelectorAll('.reading-history-tbody');
                if (historyTbodies && historyTbodies.length > 0) {
                    historyTbodies.forEach(tbody => {
                        if (!tbody) return;
                        
                        try {
                            // Check if we're dealing with a table or div-based layout
                            if (tbody.tagName === 'TBODY') {
                                // Traditional table layout
                                const newRow = tbody.insertRow(0);
                                if (newRow) {
                                    newRow.innerHTML = `
                                        <td>${readingDate}</td>
                                        <td>${newReadingValue}</td>
                                        <td><span class="badge bg-info-subtle text-info">Just Added</span></td>
                                    `;
                                }
                            } else {
                                // Div-based layout for mobile
                                const newReadingDiv = document.createElement('div');
                                newReadingDiv.className = 'd-flex align-items-center py-2 px-3 border-bottom border-secondary-subtle';
                                newReadingDiv.innerHTML = `
                                    <div class="hide-on-xs fs-12" style="width: 35%">${readingDate}</div>
                                    <div class="full-width-on-xs text-center fw-medium" style="width: 30%">${newReadingValue}</div>
                                    <div class="full-width-on-xs text-end" style="width: 35%">
                                        <span class="badge bg-info">Just Added</span>
                                    </div>
                                `;
                                
                                // Insert at the beginning
                                if (tbody.firstChild) {
                                    tbody.insertBefore(newReadingDiv, tbody.firstChild);
                                } else {
                                    tbody.appendChild(newReadingDiv);
                                }
                            }
                        } catch (err) {
                            console.warn('Could not update history table:', err);
                        }
                    });
                }
                
                // Refresh history container if it exists
                const historyContainers = meterCard.querySelectorAll('.history-container');
                if (historyContainers && historyContainers.length > 0) {
                    historyContainers.forEach(container => {
                        if (!container) return;
                        const url = container.dataset.url;
                        if (url) {
                            // Add a small delay to allow the server to process the new reading
                            setTimeout(() => {
                                container.innerHTML = `<div class="text-center p-4"><div class="spinner-border spinner-border-sm" role="status"></div></div>`;
                                fetch(url)
                                    .then(response => response.text())
                                    .then(html => {
                                        container.innerHTML = html;
                                    })
                                    .catch(error => {
                                        container.innerHTML = '<p class="text-danger text-center">Could not load history.</p>';
                                    });
                            }, 500);
                        }
                    });
                }
            } catch (err) {
                console.error('Error updating meter card UI:', err);
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to load history content into a container
            const loadHistory = (container) => {
                if (!container) return;
                
                const url = container.dataset.url;
                if (!url) return;

                // Show a spinner while loading
                container.innerHTML = `<div class="text-center p-4"><div class="spinner-border spinner-border-sm" role="status"></div></div>`;

                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                    })
                    .catch(error => {
                        container.innerHTML = '<p class="text-danger text-center">Could not load history.</p>';
                        console.error('Error loading history:', error);
                    });
            };

            // Find all history containers and load their initial content
            const historyContainers = document.querySelectorAll('.history-container');
            if (historyContainers && historyContainers.length > 0) {
                historyContainers.forEach(container => {
                    if (container) loadHistory(container);
                });
            }

            // Use event delegation to handle clicks on pagination links
            document.body.addEventListener('click', function (event) {
                // Target only pagination links inside a history container
                if (event.target.matches('.pagination a')) {
                    event.preventDefault();

                    const link = event.target;
                    const container = link.closest('.history-container');
                    if (container) {
                        // Temporarily update the dataset url to the new page url
                        container.dataset.url = link.href;
                        // Reload the container with the new page
                        loadHistory(container);
                    }
                }
            });

            // Removed tab-specific logic since we're not using tabs in mobile view anymore
        });
    </script>

@endpush