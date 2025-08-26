class CalendarSchedule {
    constructor() {
        // DOM Element References
        this.modalEl = document.getElementById("override-modal");
        this.calendarEl = document.getElementById("calendar");
        this.formEventEl = document.getElementById("override-event");
        this.btnNewEventEl = document.getElementById("btn-new-event");
        this.btnDeleteEventEl = document.getElementById("btn-delete-event");

        // Instances and State
        this.modal = new bootstrap.Modal(this.modalEl);
        this.calendar = null;
        this.selectedEvent = null;
        this.newEventData = null;
    }

    // Formats a Date object into "YYYY-MM-DD"
    _formatDateForInput(date) {
        if (!date) return "";
        const d = new Date(date);
        const pad = (num) => num.toString().padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    }

    // Handler for clicking an existing event on the calendar
    _onEventClick(clickInfo) {
        this.formEventEl.reset();
        this.formEventEl.classList.remove("was-validated");
        this.selectedEvent = clickInfo.event;

        document.getElementById("modal-title").textContent = "Edit Price Override";
        this.btnDeleteEventEl.style.display = "block";

        // Populate form fields
        document.getElementById("title").value = this.selectedEvent.title;
        document.getElementById("price").value = this.selectedEvent.extendedProps.price || '';
        document.getElementById("start_date").value = this._formatDateForInput(this.selectedEvent.start);

        let endDate = this.selectedEvent.end ? new Date(this.selectedEvent.end) : new Date(this.selectedEvent.start);
        if (this.selectedEvent.allDay) {
            endDate.setDate(endDate.getDate() - 1);
        }
        document.getElementById("end_date").value = this._formatDateForInput(endDate);

        // Set the active color swatch based on the event's data
        const eventColorClass = this.selectedEvent.extendedProps.color || 'bg-primary-subtle';
        document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
        const activeSwatch = document.querySelector(`.color-swatch[data-color="${eventColorClass}"]`);
        if (activeSwatch) {
            activeSwatch.classList.add('active');
        }

        this.modal.show();
    }

    // Handler for selecting a new date or range on the calendar
    _onSelect(selectionInfo) {
        this.formEventEl.reset();
        this.formEventEl.classList.remove("was-validated");
        this.selectedEvent = null;
        this.newEventData = selectionInfo;

        document.getElementById("modal-title").textContent = "Add Price Override";
        this.btnDeleteEventEl.style.display = "none";

        // Set default active color
        document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
        document.querySelector('.color-swatch[data-color="bg-primary-subtle"]').classList.add('active');

        // Populate date fields from selection
        const startDate = selectionInfo.start;
        const endDate = new Date(selectionInfo.end.getTime() - (24 * 60 * 60 * 1000));
        document.getElementById("start_date").value = this._formatDateForInput(startDate);
        document.getElementById("end_date").value = this._formatDateForInput(endDate);

        this.modal.show();
        this.calendar.unselect();
    }

    // Main initialization method
    init() {
        // --- 1. Set up color swatch click handlers ---
        const colorSwatches = document.querySelectorAll('.color-swatch');
        colorSwatches.forEach(swatchToActivate => {
            swatchToActivate.addEventListener('click', () => {
                colorSwatches.forEach(s => s.classList.remove('active'));
                swatchToActivate.classList.add('active');
            });
        });

        // --- 2. Set up main form submission (save) handler ---
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

            // This is the correct way to get the selected color
            const activeSwatch = document.querySelector('.color-swatch.active');
            const colorClassNameToSave = activeSwatch ? activeSwatch.getAttribute('data-color') : 'bg-primary-subtle';

            const eventData = {
                title: document.getElementById("title").value,
                price: document.getElementById("price").value,
                start_date: document.getElementById("start_date").value,
                end_date: document.getElementById("end_date").value,
                color: colorClassNameToSave,
            };

            // Logic for creating a new event
            if (!this.selectedEvent) {
                try {
                    const response = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(eventData)
                    });

                    if (response.ok) {
                        window.location.reload(); // Success! Reload the page via PRG.
                    } else {
                        const errorData = await response.json();
                        let errorMessage = 'Failed to save event:\n\n' + (errorData.message || '');
                        if (errorData.errors) {
                            Object.values(errorData.errors).forEach(errs => {
                                errorMessage += `\n- ${errs.join('\n- ')}`;
                            });
                        }
                        alert(errorMessage);
                    }
                } catch (error) {
                    alert('An unexpected network error occurred.');
                } finally {
                    saveButton.disabled = false;
                    saveButton.innerHTML = 'Save';
                }
            } else {
                // Logic for updating an existing event would go here
                alert('Update functionality is not yet implemented.');
                saveButton.disabled = false;
                saveButton.innerHTML = 'Save';
            }
        });

        // --- 3. Initialize and render the FullCalendar instance ---
        this.calendar = new FullCalendar.Calendar(this.calendarEl, {
            initialEvents: calendarEvents,
            themeSystem: "bootstrap",
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth"
            },
            editable: true,
            selectable: true,
            height: window.innerHeight - 200,
            eventClick: (info) => this._onEventClick(info),
            select: (info) => this._onSelect(info),
            dateClick: (info) => this._onSelect(info)
        });

        this.calendar.render();
    }
}
