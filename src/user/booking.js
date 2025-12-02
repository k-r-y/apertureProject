// booking.js - Dedicated script for booking form functionality
document.addEventListener("DOMContentLoaded", function () {
    // ===================================
    // SECURITY & VALIDATION HELPERS
    // ===================================

    function sanitizeInput(input) {
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML;
    }

    // ===================================
    // FETCH AND DISABLE BOOKED DATES
    // ===================================

    let bookedDates = [];

    // Fetch all booked dates from the server
    fetch('../includes/api/get_booked_dates.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.bookedDates) {
                bookedDates = data.bookedDates;

                // Apply disabled dates to the date input
                const eventDateInput = document.getElementById('eventDate');
                if (eventDateInput) {
                    // Add event listener to prevent selection of booked dates
                    eventDateInput.addEventListener('input', function (e) {
                        const selectedDate = this.value;
                        if (bookedDates.includes(selectedDate)) {
                            this.value = '';
                            const feedbackDiv = document.getElementById('date-availability-feedback');
                            if (feedbackDiv) {
                                this.classList.add('is-invalid');
                                feedbackDiv.classList.add('invalid-feedback');
                                feedbackDiv.textContent = 'This date is already booked. Please select another date.';
                                feedbackDiv.style.display = 'block';
                            }

                            // Show LuxuryModal notification
                            if (typeof LuxuryModal !== 'undefined') {
                                LuxuryModal.show({
                                    title: 'Date Unavailable',
                                    message: 'This date is already booked. Please select another date.',
                                    icon: 'warning',
                                    confirmText: 'OK'
                                });
                            }
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error fetching booked dates:', error);
        });

    // Validate Date Range (5 days min, 3 years max)
    const eventDateRangeInput = document.getElementById('eventDate');
    if (eventDateRangeInput) {
        eventDateRangeInput.addEventListener('change', function () {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const minDate = new Date(today);
            minDate.setDate(today.getDate() + 3);

            const maxDate = new Date(today);
            maxDate.setFullYear(today.getFullYear() + 3);

            if (selectedDate < minDate) {
                LuxuryModal.show({
                    title: 'Invalid Date',
                    message: 'Bookings must be made at least 3 days in advance.',
                    icon: 'warning'
                });
                this.value = '';
                return;
            }

            if (selectedDate > maxDate) {
                LuxuryModal.show({
                    title: 'Invalid Date',
                    message: 'Bookings cannot be made more than 3 years in advance.',
                    icon: 'warning'
                });
                this.value = '';
                return;
            }
        });
    }

    // ===================================
    // EVENT TYPE CUSTOM INPUT HANDLER
    // ===================================

    const eventTypeSelect = document.getElementById('eventType');
    const customEventTypeContainer = document.getElementById('customEventTypeContainer');
    const customEventTypeInput = document.getElementById('customEventType');

    if (eventTypeSelect && customEventTypeContainer && customEventTypeInput) {
        // Check initial state on page load
        if (eventTypeSelect.value === 'Other') {
            customEventTypeContainer.style.display = 'block';
            customEventTypeInput.setAttribute('required', 'required');
        }

        eventTypeSelect.addEventListener('change', function () {
            if (this.value === 'Other') {
                customEventTypeContainer.style.display = 'block';
                customEventTypeInput.setAttribute('required', 'required');
                customEventTypeInput.focus();
            } else {
                customEventTypeContainer.style.display = 'none';
                customEventTypeInput.removeAttribute('required');
                customEventTypeInput.value = '';
            }
        });
    }

    // ===================================
    // REAL-TIME BOOKING SUMMARY LOGIC
    // ===================================

    const summaryPackageName = document.getElementById('summary-package-name');
    const summaryAddonsList = document.getElementById('summary-addons-list');
    const summaryAddonsContainer = document.getElementById('summary-addons-container');
    const totalPriceEl = document.getElementById('totalPrice');
    const downpaymentEl = document.getElementById('downpaymentAmount');

    // Summary Elements
    const summaryClientName = document.getElementById('summary-client-name');
    const summaryClientEmail = document.getElementById('summary-client-email');
    const summaryClientPhone = document.getElementById('summary-client-phone');
    const summaryEventDate = document.getElementById('summary-event-date');
    const summaryEventTime = document.getElementById('summary-event-time');
    const summaryEventVenue = document.getElementById('summary-event-venue');
    const summaryPaymentMethod = document.getElementById('summary-payment-method');
    const summaryPaymentContainer = document.getElementById('summary-payment-container');
    const summarySpecialRequests = document.getElementById('summary-special-requests');
    const summaryRequestsContainer = document.getElementById('summary-requests-container');

    function updateBookingSummary() {
        let total = 0;
        const selectedPackageRadio = document.querySelector('input[name="packageID"]:checked');
        const selectedAddons = document.querySelectorAll('input[name="addons[]"]:checked');

        // Update Client Info
        if (summaryClientName) {
            const fname = document.querySelector('input[name="fname"]')?.value || '';
            const lname = document.querySelector('input[name="lname"]')?.value || '';
            summaryClientName.textContent = sanitizeInput(`${fname} ${lname}`);
        }
        if (summaryClientEmail) {
            summaryClientEmail.textContent = sanitizeInput(document.querySelector('input[name="email"]')?.value || '');
        }
        if (summaryClientPhone) {
            summaryClientPhone.textContent = sanitizeInput(document.querySelector('input[name="phone"]')?.value || '');
        }

        // Update Event Details
        if (summaryEventDate) {
            const dateVal = document.querySelector('input[name="eventDate"]')?.value;
            summaryEventDate.textContent = dateVal ? new Date(dateVal).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : '-';
        }
        if (summaryEventTime) {
            const start = document.getElementById('startTime')?.value;
            const end = document.getElementById('endTime')?.value;

            const formatTime = (timeStr) => {
                if (!timeStr) return '';
                const [hours, minutes] = timeStr.split(':');
                const h = parseInt(hours);
                const ampm = h >= 12 ? 'PM' : 'AM';
                const h12 = h % 12 || 12;
                return `${h12}:${minutes} ${ampm}`;
            };

            summaryEventTime.textContent = (start && end) ? `${formatTime(start)} - ${formatTime(end)}` : '-';
        }
        if (summaryEventVenue) {
            const location = document.querySelector('input[name="location"]')?.value;
            summaryEventVenue.textContent = location ? sanitizeInput(location) : '-';
        }

        // Update package in summary
        if (selectedPackageRadio) {
            const packageLabel = selectedPackageRadio.nextElementSibling;
            console.log('Package selected:', selectedPackageRadio.value);
            console.log('Package label:', packageLabel);

            if (packageLabel && packageLabel.dataset) {
                const packagePrice = parseFloat(packageLabel.dataset.price) || 0;
                const packageName = packageLabel.dataset.name || 'Unknown Package';
                total += packagePrice;

                console.log('Package price:', packagePrice);
                console.log('Package name:', packageName);
                console.log('Summary element:', summaryPackageName);

                if (summaryPackageName) {
                    // Use innerHTML to render the span tag (packageName comes from data attributes which are already escaped by PHP)
                    summaryPackageName.innerHTML = `<div class="d-flex justify-content-between align-items-center">
                   <span class="text-muted" style="font-size: 0.875rem; font-weight: 400;">${packageName}</span>
                   <span class="text-muted" style="font-size: 0.875rem; font-weight: 400;"> ₱${packagePrice.toLocaleString()}</span>
                </div>`;
                    console.log('Package summary updated:', summaryPackageName.innerHTML);
                } else {
                    console.error('summaryPackageName element not found!');
                }
            } else {
                console.error('Package label or dataset not found', packageLabel);
                if (summaryPackageName) {
                    summaryPackageName.textContent = 'Package selected (price unavailable)';
                }
            }
        } else {
            console.log('No package selected');
            if (summaryPackageName) {
                summaryPackageName.textContent = 'Not selected';
            }
        }

        // Update add-ons in summary
        if (summaryAddonsList) {
            summaryAddonsList.innerHTML = '';
            if (selectedAddons.length > 0) {
                selectedAddons.forEach(checkbox => {
                    const addonPrice = parseFloat(checkbox.dataset.price);
                    const addonName = checkbox.dataset.name;
                    total += addonPrice;

                    const addonItem = document.createElement('div');
                    // addonName comes from data attributes which are already escaped by PHP
                    addonItem.innerHTML = `
                    
                    <div class="d-flex justify-content-between align-items-center">
                   <span class="text-muted" style="font-size: 0.875rem; font-weight: 400;">${addonName}</span>
                   <span class="text-muted" style="font-size: 0.875rem; font-weight: 400;"> ₱${addonPrice.toLocaleString()}</span>
                </div>`;
                    summaryAddonsList.appendChild(addonItem);
                });
                if (summaryAddonsContainer) summaryAddonsContainer.style.display = 'block';
            } else {
                if (summaryAddonsContainer) summaryAddonsContainer.style.display = 'none';
            }
        }

        // Update Payment Method
        const selectedPayment = document.querySelector('input[name="paymentMethod"]:checked');
        if (summaryPaymentMethod && selectedPayment) {
            summaryPaymentMethod.textContent = sanitizeInput(selectedPayment.value);
            if (summaryPaymentContainer) summaryPaymentContainer.style.display = 'block';
        } else {
            if (summaryPaymentContainer) summaryPaymentContainer.style.display = 'none';
        }

        // Update Special Requests
        const requests = document.querySelector('textarea[name="specialRequests"]')?.value;
        if (summarySpecialRequests && requests) {
            summarySpecialRequests.textContent = sanitizeInput(requests);
            if (summaryRequestsContainer) summaryRequestsContainer.style.display = 'block';
        } else {
            if (summaryRequestsContainer) summaryRequestsContainer.style.display = 'none';
        }

        // Calculate extra hours if applicable
        let extraHoursCost = 0;
        const extraHoursRow = document.getElementById('extra-hours-row');
        const extraHoursPrice = document.getElementById('extraHoursPrice');
        const extraHoursDetail = document.getElementById('extra-hours-detail');

        if (selectedPackageRadio) {
            const packageLabel = selectedPackageRadio.nextElementSibling;
            const startTimeInput = document.getElementById('startTime');
            const endTimeInput = document.getElementById('endTime');

            if (packageLabel && startTimeInput && endTimeInput && startTimeInput.value && endTimeInput.value) {
                const coverageHours = parseFloat(packageLabel.dataset.coverageHours) || 4;
                const hourlyRate = parseFloat(packageLabel.dataset.hourlyRate) || 1000;

                // Calculate event duration
                const startTime = startTimeInput.value.split(':');
                const endTime = endTimeInput.value.split(':');
                const startMinutes = parseInt(startTime[0]) * 60 + parseInt(startTime[1]);
                const endMinutes = parseInt(endTime[0]) * 60 + parseInt(endTime[1]);
                const durationMinutes = endMinutes - startMinutes;
                const durationHours = durationMinutes / 60;

                console.log('Coverage calculation:', {
                    coverageHours,
                    durationHours,
                    hourlyRate
                });

                if (durationHours > coverageHours) {
                    const extraHours = durationHours - coverageHours;
                    extraHoursCost = Math.ceil(extraHours) * hourlyRate; // Round up to nearest hour
                    total += extraHoursCost;

                    if (extraHoursRow && extraHoursPrice && extraHoursDetail) {
                        extraHoursRow.style.display = 'flex';
                        extraHoursPrice.textContent = `₱${extraHoursCost.toLocaleString()}`;
                        extraHoursDetail.textContent = `(${Math.ceil(extraHours)}h @ ₱${hourlyRate.toLocaleString()}/h)`;
                    }

                    console.log('Extra hours charged:', {
                        extraHours: Math.ceil(extraHours),
                        extraHoursCost
                    });
                } else {
                    if (extraHoursRow) {
                        extraHoursRow.style.display = 'none';
                    }
                }
            } else {
                if (extraHoursRow) {
                    extraHoursRow.style.display = 'none';
                }
            }
        } else {
            if (extraHoursRow) {
                extraHoursRow.style.display = 'none';
            }
        }

        // Calculate downpayment based on FINAL total (including extra hours)
        const minDownpayment = total * 0.25;
        const downpaymentInput = document.getElementById('downpaymentInput');
        const downpaymentHint = document.getElementById('downpaymentHint');

        let finalDownpayment = minDownpayment;

        if (downpaymentInput) {
            // Update constraints
            downpaymentInput.min = minDownpayment.toFixed(2);
            downpaymentInput.max = total.toFixed(2);

            // Update hint text
            if (downpaymentHint) {
                downpaymentHint.textContent = `Minimum required: ₱${minDownpayment.toLocaleString()} (25% of ₱${total.toLocaleString()})`;
            }

            // Get user value or default to min
            const userValue = parseFloat(downpaymentInput.value);

            // If user hasn't touched it yet or it's invalid (less than min), set to min
            // We check if it's the active element to avoid overwriting while typing
            if (document.activeElement !== downpaymentInput) {
                if (isNaN(userValue) || userValue < minDownpayment) {
                    downpaymentInput.value = minDownpayment.toFixed(2);
                    finalDownpayment = minDownpayment;
                } else if (userValue > total) {
                    downpaymentInput.value = total.toFixed(2);
                    finalDownpayment = total;
                } else {
                    finalDownpayment = userValue;
                }
            } else {
                // If typing, just use the value for summary if valid, otherwise min for summary
                finalDownpayment = (!isNaN(userValue) && userValue >= minDownpayment) ? userValue : minDownpayment;

                // Real-time validation feedback
                if (userValue > total) {
                    downpaymentInput.classList.add('is-invalid');
                    if (downpaymentHint) {
                        downpaymentHint.textContent = `Amount cannot exceed total price (₱${total.toLocaleString()})`;
                        downpaymentHint.classList.remove('text-muted');
                        downpaymentHint.classList.add('text-danger');
                    }
                } else if (userValue < minDownpayment && !isNaN(userValue) && downpaymentInput.value !== '') {
                    // Only show error if they typed something and it's too low
                    // We don't auto-correct while typing to allow them to finish typing
                    // But we can show visual feedback
                    // downpaymentInput.classList.add('is-invalid'); // Optional: might be annoying while typing "1000"
                } else {
                    downpaymentInput.classList.remove('is-invalid');
                    if (downpaymentHint) {
                        downpaymentHint.textContent = `Minimum required: ₱${minDownpayment.toLocaleString()} (25% of ₱${total.toLocaleString()})`;
                        downpaymentHint.classList.add('text-muted');
                        downpaymentHint.classList.remove('text-danger');
                    }
                }
            }
        }

        // Update prices with animation
        if (totalPriceEl) {
            const currentTotal = parseFloat(totalPriceEl.textContent.replace(/[^0-9.]/g, '')) || 0;
            if (currentTotal !== total) {
                totalPriceEl.style.transform = 'scale(1.1)';
                totalPriceEl.textContent = `₱${total.toLocaleString()}`;
                setTimeout(() => totalPriceEl.style.transform = 'scale(1)', 200);
            }
        }

        if (downpaymentEl) {
            const currentDown = parseFloat(downpaymentEl.textContent.replace(/[^0-9.]/g, '')) || 0;
            if (currentDown !== finalDownpayment) {
                downpaymentEl.style.transform = 'scale(1.1)';
                downpaymentEl.textContent = `₱${finalDownpayment.toLocaleString()}`;
                setTimeout(() => downpaymentEl.style.transform = 'scale(1)', 200);
            }
        }

        return total;
    }

    function updateAddonListeners() {
        const addonCheckboxes = document.querySelectorAll('input[name="addons[]"]');
        addonCheckboxes.forEach(checkbox => {
            checkbox.removeEventListener('change', updateBookingSummary);
            checkbox.addEventListener('change', updateBookingSummary);
        });
    }

    // Add listeners to all inputs for real-time updates
    const allInputs = document.querySelectorAll('input[name="fname"], input[name="lname"], input[name="email"], input[name="phone"], input[name="eventDate"], #startTime, #endTime, input[name="location"], textarea[name="specialRequests"], input[name="downpayment"]');
    allInputs.forEach(input => {
        input.addEventListener('input', updateBookingSummary);
        input.addEventListener('change', updateBookingSummary);
    });

    document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
        radio.addEventListener('change', updateBookingSummary);
    });

    // DATE AVAILABILITY CHECK WITH REAL-TIME VALIDATION
    // ===================================

    const eventDateInput = document.getElementById('eventDate');
    const feedbackDiv = document.getElementById('date-availability-feedback');
    const startTimeInput = document.getElementById('startTime');
    const endTimeInput = document.getElementById('endTime');

    // Real-time date validation
    if (eventDateInput) {
        eventDateInput.addEventListener('change', function () {
            const selectedDate = this.value;
            if (!selectedDate) {
                this.classList.remove('is-valid', 'is-invalid');
                feedbackDiv.textContent = '';
                feedbackDiv.classList.remove('valid-feedback', 'invalid-feedback');
                return;
            }

            const today = new Date();
            const selected = new Date(selectedDate);
            today.setHours(0, 0, 0, 0);
            selected.setHours(0, 0, 0, 0);

            // Check if date is in the past
            if (selected < today) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
                feedbackDiv.classList.remove('valid-feedback');
                feedbackDiv.classList.add('invalid-feedback');
                feedbackDiv.textContent = 'Please select a future date';
                feedbackDiv.style.display = 'block';
                return;
            }

            // Check minimum 3 days advance booking
            const minDate = new Date();
            minDate.setDate(minDate.getDate() + 3);
            minDate.setHours(0, 0, 0, 0);

            if (selected < minDate) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
                feedbackDiv.classList.remove('valid-feedback');
                feedbackDiv.classList.add('invalid-feedback');
                feedbackDiv.textContent = 'Bookings must be made at least 3 days in advance';
                feedbackDiv.style.display = 'block';
                return;
            }

            // Check availability via API
            feedbackDiv.textContent = 'Checking availability...';
            feedbackDiv.classList.remove('invalid-feedback', 'valid-feedback');
            feedbackDiv.classList.add('text-muted');
            feedbackDiv.style.display = 'block';

            fetch('../includes/api/check_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ date: selectedDate })
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.available) {
                        eventDateInput.classList.remove('is-invalid');
                        eventDateInput.classList.add('is-valid');
                        feedbackDiv.classList.remove('invalid-feedback', 'text-muted');
                        feedbackDiv.classList.add('valid-feedback');
                        feedbackDiv.textContent = data.message || 'Date is available';
                        feedbackDiv.style.display = 'block';
                    } else {
                        eventDateInput.classList.remove('is-valid');
                        eventDateInput.classList.add('is-invalid');
                        feedbackDiv.classList.remove('valid-feedback', 'text-muted');
                        feedbackDiv.classList.add('invalid-feedback');
                        feedbackDiv.textContent = sanitizeInput(data.message);
                        feedbackDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error checking availability:', error);
                    eventDateInput.classList.add('is-invalid');
                    feedbackDiv.classList.remove('valid-feedback', 'text-muted');
                    feedbackDiv.classList.add('invalid-feedback');
                    feedbackDiv.textContent = 'Unable to check availability. Please try again.';
                    feedbackDiv.style.display = 'block';
                });
        });
    }

    // Real-time time validation
    function validateTimeRange() {
        const startTimeSelect = document.getElementById('startTime');
        const endTimeSelect = document.getElementById('endTime');

        if (!startTimeSelect || !endTimeSelect) return;

        const startValue = startTimeSelect.value;
        const endValue = endTimeSelect.value;

        let feedbackEl = document.getElementById('time-range-feedback');
        if (!feedbackEl) {
            feedbackEl = document.createElement('div');
            feedbackEl.id = 'time-range-feedback';
            feedbackEl.className = 'invalid-feedback';
            endTimeSelect.parentElement.appendChild(feedbackEl);
        }

        // Reset states
        startTimeSelect.classList.remove('is-invalid');
        endTimeSelect.classList.remove('is-invalid');
        feedbackEl.style.display = 'none';

        if (!startValue || !endValue) return;

        const [startH, startM] = startValue.split(':').map(Number);
        const [endH, endM] = endValue.split(':').map(Number);

        const startMinutes = startH * 60 + startM;
        const endMinutes = endH * 60 + endM;

        // Enforce 2-hour minimum duration (120 minutes)
        if (endMinutes - startMinutes < 120) {
            endTimeSelect.classList.add('is-invalid');
            feedbackEl.textContent = 'Minimum booking duration is 2 hours';
            feedbackEl.style.display = 'block';
        } else {
            endTimeSelect.classList.remove('is-invalid');
            endTimeSelect.classList.add('is-valid');
        }
    }

    if (startTimeInput) {
        startTimeInput.addEventListener('change', validateTimeRange);
    }

    if (endTimeInput) {
        endTimeInput.addEventListener('change', validateTimeRange);
    }

    // ===================================
    // PACKAGE SELECTION & DETAILS
    // ===================================

    const packageDetailsCache = {};

    async function fetchAndDisplayPackageDetails(packageId, container) {
        // Check cache first
        if (packageDetailsCache[packageId]) {
            container.innerHTML = packageDetailsCache[packageId];
            container.style.display = 'block';
            updateAddonListeners();
            return;
        }

        container.innerHTML = '<div class="text-center text-muted-luxury p-3"><div class="spinner-border spinner-border-sm text-gold" role="status"></div><span class="ms-2">Loading details...</span></div>';
        container.style.display = 'block';

        try {
            const response = await fetch(`getPackageDetails.php?packageId=${encodeURIComponent(packageId)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Failed to fetch package details');
            const data = await response.json();

            let detailsHtml = '<div class="row g-3">';

            // Inclusions
            if (data.inclusions && data.inclusions.length > 0) {
                detailsHtml += '<div class="col-12"><h6 class="text-gold text-uppercase small letter-spacing-1 mb-2">What\'s Included</h6><ul class="list-unstyled mb-0">';
                data.inclusions.forEach(item => {
                    detailsHtml += `<li class="text-muted small mb-1"><i class="bi bi-check2 text-gold me-2"></i>${sanitizeInput(item)}</li>`;
                });
                detailsHtml += '</ul></div>';
            }

            // Add-ons
            detailsHtml += '<div class="col-12"><h6>Available Add-ons</h6>';
            if (data.addons && data.addons.length > 0) {
                data.addons.forEach(addon => {
                    const addonId = sanitizeInput(addon.addonID);
                    const addonDesc = sanitizeInput(addon.Description);
                    const addonPrice = parseFloat(addon.Price);

                    detailsHtml += `
                        <div class="addon-card-luxury position-relative">
                            <input class="form-check-input" type="checkbox" name="addons[]" value="${addonId}" id="addon-${addonId}" data-price="${addonPrice}" data-name="${addonDesc}">
                            <label class="form-check-label ms-2 stretched-link" for="addon-${addonId}" style="color: var(--text-secondary); cursor: pointer;">
                                <strong>${addonDesc}</strong> - <span class="text-gold">₱${addonPrice.toLocaleString()}</span>
                            </label>
                        </div>`;
                });
            } else {
                detailsHtml += '<p class="text-muted-luxury small mb-0">No add-ons available</p>';
            }
            detailsHtml += '</div></div>';

            packageDetailsCache[packageId] = detailsHtml;
            container.innerHTML = detailsHtml;
            updateAddonListeners();

        } catch (error) {
            console.error('Failed to fetch package details:', error);
            container.innerHTML = '<p class="text-danger small">Could not load details. Please try again.</p>';
        }
    }

    // Package selection event listeners
    document.querySelectorAll('.luxury-radio[name="packageID"]').forEach(radio => {
        radio.addEventListener('change', function () {
            // Clear previous add-ons
            document.querySelectorAll('input[name="addons[]"]').forEach(cb => cb.checked = false);

            // Hide ALL package details containers
            document.querySelectorAll('[id^="details-"]').forEach(container => {
                container.style.display = 'none';
                container.innerHTML = ''; // Clear content for performance
            });

            const packageId = this.value;
            const detailsContainer = document.getElementById(`details-${packageId}`);

            if (detailsContainer) {
                fetchAndDisplayPackageDetails(packageId, detailsContainer);
            }

            updateBookingSummary();
        });
    });

    // ===================================
    // PAYMENT METHOD HANDLING
    // ===================================

    const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
    const paymentDetails = document.getElementById('paymentDetails');
    const paymentInfo = document.getElementById('paymentInfo');
    const proofUploadSection = document.getElementById('proofUploadSection');
    const paymentProofInput = document.getElementById('paymentProof');
    const proofPreview = document.getElementById('proofPreview');

    const paymentDetailsData = {
        'GCash': {
            icon: 'bi-phone',
            title: 'GCash Payment',
            details: [
                { label: 'Account Name', value: 'Aperture Photography' },
                { label: 'GCash Number', value: '0917-123-4567' },
                { label: 'Instructions', value: 'Send the downpayment amount and upload the receipt below.' }
            ]
        },
        'PayMaya': {
            icon: 'bi-wallet2',
            title: 'PayMaya Payment',
            details: [
                { label: 'Account Name', value: 'Aperture Photography' },
                { label: 'PayMaya Number', value: '0917-123-4567' },
                { label: 'Instructions', value: 'Send the downpayment amount and upload the receipt below.' }
            ]
        },
        'Bank Transfer': {
            icon: 'bi-bank',
            title: 'Bank Transfer',
            details: [
                { label: 'Bank Name', value: 'BDO Unibank' },
                { label: 'Account Name', value: 'Aperture Photography Services' },
                { label: 'Account Number', value: '1234-5678-9012' },
                { label: 'Instructions', value: 'Transfer the downpayment amount and upload the receipt below.' }
            ]
        },
        'Cash': {
            icon: 'bi-cash',
            title: 'Cash Payment',
            details: [
                { label: 'Office Address', value: 'Dasmariñas City, Cavite, Philippines' },
                { label: 'Office Hours', value: 'Mon-Fri: 9AM-6PM, Sat: 9AM-3PM' },
                { label: 'Instructions', value: 'Please pay the downpayment at our office. Bring a valid ID.' }
            ]
        }
    };

    if (paymentMethods) {
        paymentMethods.forEach(method => {
            method.addEventListener('change', function () {
                const selectedMethod = this.value;
                const details = paymentDetailsData[selectedMethod];

                if (details) {
                    let infoHTML = `<div class="mb-3">
                        <h6 class="mb-3 text-light"><i class="bi ${details.icon} me-2"></i>${sanitizeInput(details.title)}</h6>
                    `;

                    details.details.forEach(item => {
                        infoHTML += `<div class="mb-3">
                            <small class="text-light fw-bold d-block">${sanitizeInput(item.label)} : </small>
                            <span class="text-light fw-light">${sanitizeInput(item.value)}</span>
                        </div>`;
                    });

                    infoHTML += '</div>';
                    paymentInfo.innerHTML = infoHTML;
                    paymentDetails.style.display = 'block';

                    // Show/hide proof upload based on payment method
                    if (selectedMethod === 'Cash') {
                        proofUploadSection.style.display = 'none';
                        paymentProofInput.removeAttribute('required');
                    } else {
                        proofUploadSection.style.display = 'block';
                        paymentProofInput.setAttribute('required', 'required');
                    }
                }
            });
        });
    }

    // ===================================
    // PAYMENT PROOF UPLOAD WITH SECURITY
    // ===================================

    if (paymentProofInput) {
        paymentProofInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) {
                proofPreview.innerHTML = '';
                return;
            }

            // File size validation (5MB max)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                LuxuryModal.show({
                    title: 'File Too Large',
                    message: 'File size must be less than 5MB',
                    icon: 'error',
                    confirmText: 'OK'
                });
                this.value = '';
                proofPreview.innerHTML = '';
                return;
            }

            // File type validation
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            if (!validTypes.includes(file.type)) {
                LuxuryModal.show({
                    title: 'Invalid File Type',
                    message: 'Please upload a valid image (JPG, PNG) or PDF file',
                    icon: 'error',
                    confirmText: 'OK'
                });
                this.value = '';
                proofPreview.innerHTML = '';
                return;
            }

            // Display preview
            const reader = new FileReader();
            reader.onload = function (event) {
                let previewHTML = '';
                if (file.type === 'application/pdf') {
                    previewHTML = `
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="bi bi-file-pdf text-gold me-3" style="font-size: 2.5rem;"></i>
                            <div>
                                <strong>PDF Uploaded</strong><br>
                                <small>${sanitizeInput(file.name)} (${(file.size / 1024).toFixed(2)} KB)</small>
                            </div>
                        </div>
                    `;
                } else {
                    previewHTML = `
                        <div class="text-center">
                            <img src="${event.target.result}" class="img-fluid" alt="Payment Proof Preview">
                            <small class="text-muted-luxury d-block mt-2">${sanitizeInput(file.name)} (${(file.size / 1024).toFixed(2)} KB)</small>
                        </div>
                    `;
                }
                proofPreview.innerHTML = previewHTML;
            };
            reader.readAsDataURL(file);
        });
    }

    // ===================================
    // FORM SUBMISSION WITH VALIDATION
    // ===================================

    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate package selection
            const selectedPackage = document.querySelector('input[name="packageID"]:checked');
            if (!selectedPackage) {
                LuxuryModal.show({
                    title: 'Package Required',
                    message: 'Please select a package before submitting your booking.',
                    icon: 'warning',
                    confirmText: 'OK'
                });
                return;
            }

            // Validate payment method
            const selectedPaymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
            if (!selectedPaymentMethod) {
                LuxuryModal.show({
                    title: 'Payment Method Required',
                    message: 'Please select a payment method.',
                    icon: 'warning',
                    confirmText: 'OK'
                });
                return;
            }

            // Validate proof of payment for online methods
            const paymentProof = document.getElementById('paymentProof');
            if (selectedPaymentMethod.value !== 'Cash' && !paymentProof.files.length) {
                LuxuryModal.show({
                    title: 'Payment Proof Required',
                    message: 'Please upload proof of payment for online payment methods.',
                    icon: 'warning',
                    confirmText: 'OK'
                });
                return;
            }

            // Validate terms acceptance
            const termsCheckbox = document.getElementById('termsConfirm');
            if (!termsCheckbox.checked) {
                LuxuryModal.show({
                    title: 'Terms & Conditions',
                    message: 'Please accept the terms and conditions to proceed.',
                    icon: 'warning',
                    confirmText: 'OK'
                });
                return;
            }

            // Calculate total and confirm
            const total = updateBookingSummary();
            const downpayment = total * 0.25;

            LuxuryModal.show({
                icon: 'question',
                title: 'Confirm Your Booking',
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <p style="margin-bottom: 15px;">Please review your booking details:</p>
                        <div style="background: rgba(212, 175, 55, 0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <p style="margin: 5px 0;"><strong>Total Amount:</strong> ₱${total.toLocaleString()}</p>
                            <p style="margin: 5px 0;"><strong>Downpayment (25%):</strong> ₱${downpayment.toLocaleString()}</p>
                            <p style="margin: 5px 0; color: #d4af37;"><strong>Balance:</strong> ₱${(total - downpayment).toLocaleString()}</p>
                        </div>
                        <p style="font-size: 0.9em; color: #aaa;">By confirming, you agree to pay the downpayment to secure your booking.</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirm Booking',
                cancelButtonText: 'Review Again',
                confirmButtonColor: '#d4af37',
                cancelButtonColor: '#6c757d',
                background: '#1a1a1a',
                color: '#ffffff',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                timer: false,
                timerProgressBar: false,
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: false,
                focusConfirm: false,
                focusCancel: false,
                customClass: {
                    popup: 'luxury-swal-popup',
                    confirmButton: 'luxury-swal-confirm',
                    cancelButton: 'luxury-swal-cancel'
                }
            }).then(async (result) => {
                // Only submit if user explicitly clicked "Confirm Booking"
                if (result.isConfirmed) {
                    // Show loading state
                    // Show loading state
                    const submitBtn = this.querySelector('.luxury-submit-btn') || this.querySelector('input[type="submit"]');
                    let originalText = '';

                    if (submitBtn) {
                        originalText = submitBtn.value || submitBtn.innerHTML;
                        if (submitBtn.tagName === 'INPUT') {
                            submitBtn.value = 'Processing...';
                        } else {
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                        }
                        submitBtn.disabled = true;
                    }

                    // Submit form via AJAX
                    try {
                        const formData = new FormData(this);
                        const response = await fetch('processBooking.php', {
                            method: 'POST',
                            body: formData
                        });

                        const contentType = response.headers.get('content-type');
                        let data;

                        if (contentType && contentType.includes('application/json')) {
                            data = await response.json();
                        } else {
                            // If not JSON, it might be a redirect or HTML error
                            const text = await response.text();
                            console.error('Non-JSON response:', text);
                            throw new Error('Server returned an unexpected response');
                        }

                        if (data.success) {
                            // Show success modal with redirect button
                            LuxuryModal.show({
                                icon: 'success',
                                title: 'Booking Confirmed!',
                                html: `
                                    <div style="text-align: center; padding: 20px;">
                                        <p style="margin-bottom: 15px; font-size: 1.1em;">${data.message || 'Your booking has been submitted successfully!'}</p>
                                        <div style="background: rgba(212, 175, 55, 0.1); padding: 15px; border-radius: 8px; margin: 20px 0;">
                                            <p style="margin: 5px 0;"><strong>Booking Reference:</strong> #${data.bookingRef || 'N/A'}</p>
                                            <p style="margin: 5px 0; color: #aaa; font-size: 0.9em;">We will review your booking and contact you shortly.</p>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: 'View My Appointments',
                                confirmColor: '#d4af37'
                            }).then(() => {
                                // Redirect to appointments page
                                window.location.href = 'appointments.php';
                            });
                        } else {
                            // Show error modal
                            LuxuryModal.show({
                                icon: 'error',
                                title: 'Booking Failed',
                                message: data.message || 'An error occurred while processing your booking. Please try again.',
                                confirmButtonText: 'OK'
                            });

                            // Re-enable submit button
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                if (submitBtn.tagName === 'INPUT') {
                                    submitBtn.value = originalText;
                                } else {
                                    submitBtn.innerHTML = originalText;
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Booking submission error:', error);

                        // Show error modal
                        LuxuryModal.show({
                            icon: 'error',
                            title: 'Network Error',
                            message: 'Could not connect to the server. Please check your internet connection and try again.',
                            confirmButtonText: 'OK'
                        });

                        // Re-enable submit button
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            if (submitBtn.tagName === 'INPUT') {
                                submitBtn.value = originalText;
                            } else {
                                submitBtn.innerHTML = originalText;
                            }
                        }
                    }
                }
                // If user clicked "Review Again" (isDismissed or isDenied), do nothing
                // Form will NOT submit
            });
        });
    }

    // Initialize summary
    updateBookingSummary();
});
