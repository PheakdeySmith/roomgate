document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar');
    // Stop if the calendar element doesn't exist on the page
    if (!calendarEl) {
        return;
    }

    class CalendarSchedule {
        constructor(el) {
            // Read data from the element's data-* attributes
            this.calendarEl = el;
            this.events = JSON.parse(el.dataset.events || '[]');
            this.storeUrl = el.dataset.storeUrl;
            this.updateUrlTemplate = el.dataset.updateUrlTemplate;
            this.deleteUrlTemplate = el.dataset.deleteUrlTemplate;
            this.csrfToken = el.dataset.csrfToken;
            this.swalImageSuccess = el.dataset.swalImageSuccess;
            this.swalImageError = el.dataset.swalImageError;


            // DOM elements
            this.modalEl = document.getElementById("override-modal");
            this.formEventEl = document.getElementById("override-event");
            this.btnNewEventEl = document.getElementById("btn-new-event");
            this.btnDeleteEventEl = document.getElementById("btn-delete-event");

            // State
            this.modal = new bootstrap.Modal(this.modalEl);
            this.calendar = null;
            this.selectedEvent = null;
        }

        _formatDateForInput(date) {
            if (!date) return "";
            const d = new Date(date);
            const pad = (num) => num.toString().padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        _onEventClick(clickInfo) {
            this.formEventEl.reset();
            this.formEventEl.classList.remove("was-validated");
            this.selectedEvent = clickInfo.event;
            document.getElementById("modal-title").textContent = "Edit Price Override";
            this.btnDeleteEventEl.style.display = "block";
            document.getElementById("title").value = this.selectedEvent.title;
            document.getElementById("price").value = this.selectedEvent.extendedProps.price || '';
            document.getElementById("start_date").value = this._formatDateForInput(this.selectedEvent.start);

            let endDate = this.selectedEvent.end ? new Date(this.selectedEvent.end) : new Date(this.selectedEvent.start);
            if (this.selectedEvent.allDay && this.selectedEvent.end) {
                endDate.setDate(endDate.getDate() - 1);
            }
            document.getElementById("end_date").value = this._formatDateForInput(endDate);

            document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
            const eventColorClass = this.selectedEvent.classNames[0] || this.selectedEvent.extendedProps.color || 'bg-info-subtle';
            const activeSwatch = document.querySelector(`.color-swatch[data-color="${eventColorClass}"]`);
            if (activeSwatch) {
                activeSwatch.classList.add('active');
            }
            this.modal.show();
        }

        _onSelect(selectionInfo) {
            this.formEventEl.reset();
            this.formEventEl.classList.remove("was-validated");
            this.selectedEvent = null;
            document.getElementById("modal-title").textContent = "Add Price Override";
            this.btnDeleteEventEl.style.display = "none";
            document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
            document.querySelector('.color-swatch[data-color="bg-info-subtle"]').classList.add('active');

            let startDate = selectionInfo.start;
            let endDate = selectionInfo.end ? new Date(selectionInfo.end.getTime() - (24 * 60 * 60 * 1000)) : startDate;

            document.getElementById("start_date").value = this._formatDateForInput(startDate);
            document.getElementById("end_date").value = this._formatDateForInput(endDate);

            this.modal.show();
            if (this.calendar) {
                this.calendar.unselect();
            }
        }

        async _saveEventUpdate(updateInfo) {
            const event = updateInfo.event;
            const url = this.updateUrlTemplate.replace('OVERRIDE_ID', event.id);

            let inclusiveEndDate = event.end ? new Date(event.end.getTime()) : new Date(event.start.getTime());
            if (event.allDay && event.end) {
                inclusiveEndDate.setDate(inclusiveEndDate.getDate() - 1);
            }

            const eventData = {
                title: event.title,
                price: event.extendedProps.price,
                start_date: this._formatDateForInput(event.start),
                end_date: this._formatDateForInput(inclusiveEndDate),
                color: event.classNames[0] || 'bg-info-subtle',
            };

            try {
                const response = await fetch(url, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                    body: JSON.stringify(eventData)
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to save event update.');
                }
                Swal.fire({
                    position: "top-end",
                    title: "Event Updated!",
                    width: 500,
                    padding: 30,
                    background: `var(--bs-secondary-bg) url(${this.swalImageSuccess}) no-repeat center`,
                    showConfirmButton: false,
                    timer: 4000,
                    customClass: { title: 'swal-title-success' }
                });
            } catch (error) {
                console.error('Error updating event:', error);
                updateInfo.revert();
                Swal.fire({
                    position: "top-end",
                    title: "Could not save change",
                    text: error.message,
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 4000
                });
            }
        }

        _handleEventUpdate(updateInfo) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to save the new date for this event?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, save it!",
                cancelButtonText: "No, cancel",
                confirmButtonColor: "#70bb63",
                cancelButtonColor: "#d33",
                customClass: {
                    confirmButton: "swal2-confirm btn btn-success me-2 mt-2",
                    cancelButton: "swal2-cancel btn btn-danger mt-2",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    this._saveEventUpdate(updateInfo);
                } else {
                    updateInfo.revert();
                }
            });
        }

        init() {
            const externalEventsContainer = document.getElementById('external-events');
            if (externalEventsContainer) {
                new FullCalendar.Draggable(externalEventsContainer, {
                    itemSelector: '.external-event',
                    eventData: (eventEl) => ({
                        title: eventEl.innerText.trim(),
                        className: eventEl.getAttribute('data-class') || 'bg-info-subtle',
                        extendedProps: { price: '' }
                    })
                });
            }

            this.calendar = new FullCalendar.Calendar(this.calendarEl, {
                initialEvents: this.events,
                themeSystem: "bootstrap",
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth"
                },
                editable: true,
                selectable: true,
                droppable: true,
                height: window.innerHeight - 200,
                eventReceive: (info) => {
                    info.event.remove(); // Remove the temporary event
                    this.formEventEl.reset();
                    this.selectedEvent = null;
                    document.getElementById("modal-title").textContent = "Add Dropped Event";
                    this.btnDeleteEventEl.style.display = "none";
                    document.getElementById("title").value = info.event.title;
                    document.getElementById("start_date").value = this._formatDateForInput(info.event.start);
                    document.getElementById("end_date").value = this._formatDateForInput(info.event.start);
                    document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
                    const activeSwatch = document.querySelector(`.color-swatch[data-color="${info.event.classNames[0]}"]`);
                    if (activeSwatch) activeSwatch.classList.add('active');
                    this.modal.show();
                },
                eventDrop: (info) => this._handleEventUpdate(info),
                eventResize: (info) => this._handleEventUpdate(info),
                eventClick: (info) => this._onEventClick(info),
                select: (info) => this._onSelect(info)
            });

            this.calendar.render();
            this.addEventListeners();
        }

        addEventListeners() {
            this.btnNewEventEl.addEventListener("click", () => {
                const today = new Date();
                this._onSelect({ start: today, end: new Date(today.getTime() + (24 * 60 * 60 * 1000)) });
            });

            this.btnDeleteEventEl.addEventListener("click", () => {
                if (!this.selectedEvent) return;
                Swal.fire({
                    title: "Are you sure?",
                    text: `Event "${this.selectedEvent.title}" will be permanently deleted!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel",
                    confirmButtonColor: "#d33",
                    customClass: {
                        confirmButton: "swal2-confirm btn btn-danger me-2 mt-2",
                        cancelButton: "swal2-cancel btn btn-secondary mt-2"
                    },
                    buttonsStyling: false
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const url = this.deleteUrlTemplate.replace('OVERRIDE_ID', this.selectedEvent.id);
                        try {
                            const response = await fetch(url, {
                                method: 'DELETE',
                                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken }
                            });
                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.message || 'Failed to delete');
                            }
                            this.selectedEvent.remove();
                            this.modal.hide();
                            Swal.fire({
                                position: "top-end",
                                title: "Event Deleted!",
                                width: 500,
                                padding: 30,
                                background: `var(--bs-secondary-bg) url(${this.swalImageSuccess}) no-repeat center`,
                                showConfirmButton: false,
                                timer: 4000,
                                customClass: { title: 'swal-title-success' }
                            });
                        } catch (error) {
                            Swal.fire({ title: "Error", text: error.message, icon: "error" });
                        }
                    }
                });
            });

            this.formEventEl.addEventListener("submit", async (e) => {
                e.preventDefault();
                const saveButton = document.getElementById('btn-save-event');
                if (!this.formEventEl.checkValidity()) {
                    e.stopPropagation();
                    this.formEventEl.classList.add("was-validated");
                    return;
                }

                saveButton.disabled = true;
                saveButton.innerHTML = 'Saving...';

                const activeSwatch = document.querySelector('.color-swatch.active');
                const eventData = {
                    title: document.getElementById("title").value,
                    price: document.getElementById("price").value,
                    start_date: document.getElementById("start_date").value,
                    end_date: document.getElementById("end_date").value,
                    color: activeSwatch ? activeSwatch.getAttribute('data-color') : 'bg-info-subtle',
                };

                let url = this.storeUrl;
                let method = 'POST';
                if (this.selectedEvent) {
                    url = this.updateUrlTemplate.replace('OVERRIDE_ID', this.selectedEvent.id);
                    method = 'PUT';
                }

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                        body: JSON.stringify(eventData)
                    });

                    const resultData = await response.json();
                    if (!response.ok) {
                        let errorText = resultData.message || "An unknown error occurred.";
                        if (resultData.errors) {
                            errorText = Object.values(resultData.errors).flat().join(' ');
                        }
                        throw new Error(errorText);
                    }

                    let successMessage = '';
                    if (method === 'POST') {
                        this.calendar.addEvent(resultData);
                        successMessage = 'Event created successfully!';
                    } else {
                        this.selectedEvent.remove();
                        this.calendar.addEvent(resultData);
                        successMessage = 'Event updated successfully!';
                    }

                    this.modal.hide();
                    Swal.fire({
                        position: "top-end",
                        title: successMessage,
                        width: 500,
                        padding: 30,
                        background: `var(--bs-secondary-bg) url(${this.swalImageSuccess}) no-repeat center`,
                        showConfirmButton: false,
                        timer: 4000,
                        customClass: { title: 'swal-title-success' }
                    });
                } catch (error) {
                    Swal.fire({
                        position: "top-end",
                        title: "Error!",
                        text: error.message,
                        icon: "error",
                        showConfirmButton: false,
                        timer: 6000,
                        background: `var(--bs-secondary-bg) url(${this.swalImageError}) no-repeat center`,
                        customClass: { title: 'swal-title-error', htmlContainer: 'swal-text-error' }
                    });
                } finally {
                    saveButton.disabled = false;
                    saveButton.innerHTML = 'Save';
                }
            });
        }
    }

    // Initialize the calendar
    new CalendarSchedule(calendarEl).init();

    // Handle color swatch clicks
    const colorSwatches = document.querySelectorAll('.color-swatch');
    colorSwatches.forEach(swatchToActivate => {
        swatchToActivate.addEventListener('click', () => {
            colorSwatches.forEach(s => s.classList.remove('active'));
            swatchToActivate.classList.add('active');
        });
    });
});