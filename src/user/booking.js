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

                            // Show SweetAlert notification
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Date Unavailable',
                                    text: 'This date is already booked. Please select another date.',
                                    confirmButtonColor: '#d4af37',
                                    background: '#1a1a1a',
                                    color: '#ffffff'
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
            const start = document.querySelector('input[name="startTime"]')?.value;
            const end = document.querySelector('input[name="endTime"]')?.value;

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
                    // Use innerHTML to render the span tag
                    summaryPackageName.innerHTML = `<span class="text-muted" style="font-size: 0.875rem; font-weight: 400;">${sanitizeInput(packageName)} - ₱${packagePrice.toLocaleString()}</span>`;
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
                    addonItem.innerHTML = `<span class="text-muted" style="font-size: 0.875rem; font-weight: 400;">${sanitizeInput(addonName)}</span><span>₱${addonPrice.toLocaleString()}</span>`;
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
            const startTimeInput = document.querySelector('input[name="startTime"]');
            const endTimeInput = document.querySelector('input[name="endTime"]');

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
        const downpayment = total * 0.25;

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
            if (currentDown !== downpayment) {
                downpaymentEl.style.transform = 'scale(1.1)';
                downpaymentEl.textContent = `₱${downpayment.toLocaleString()}`;
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
    const allInputs = document.querySelectorAll('input[name="fname"], input[name="lname"], input[name="email"], input[name="phone"], input[name="eventDate"], input[name="startTime"], input[name="endTime"], input[name="location"], textarea[name="specialRequests"]');
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
    const startTimeInput = document.querySelector('input[name="startTime"]');
    const endTimeInput = document.querySelector('input[name="endTime"]');

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

            // Check minimum 5 days advance booking
            const minDate = new Date();
            minDate.setDate(minDate.getDate() + 5);
            minDate.setHours(0, 0, 0, 0);

            if (selected < minDate) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
                feedbackDiv.classList.remove('valid-feedback');
                feedbackDiv.classList.add('invalid-feedback');
                feedbackDiv.textContent = 'Bookings must be made at least 5 days in advance';
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
    function validateTime(timeInput, feedbackId, label) {
        const value = timeInput.value;
        if (!value) return true;

        const [hours, minutes] = value.split(':').map(Number);
        const timeInMinutes = hours * 60 + minutes;
        const minTime = 7 * 60; // 7:00 AM
        const maxTime = 22 * 60; // 10:00 PM

        let feedbackEl = document.getElementById(feedbackId);
        if (!feedbackEl) {
            feedbackEl = document.createElement('div');
            feedbackEl.id = feedbackId;
            feedbackEl.className = 'invalid-feedback';
            timeInput.parentElement.appendChild(feedbackEl);
        }

        if (timeInMinutes < minTime || timeInMinutes > maxTime) {
            timeInput.classList.add('is-invalid');
            timeInput.classList.remove('is-valid');
            feedbackEl.textContent = `${label} must be between 7:00 AM and 10:00 PM`;
            feedbackEl.style.display = 'block';
            return false;
        } else {
            timeInput.classList.remove('is-invalid');
            timeInput.classList.add('is-valid');
            feedbackEl.textContent = '';
            feedbackEl.style.display = 'none';
            return true;
        }
    }

    function validateTimeRange() {
        if (!startTimeInput.value || !endTimeInput.value) return;

        const startValid = validateTime(startTimeInput, 'start-time-feedback', 'Start time');
        const endValid = validateTime(endTimeInput, 'end-time-feedback', 'End time');

        if (!startValid || !endValid) return;

        const [startHours, startMinutes] = startTimeInput.value.split(':').map(Number);
        const [endHours, endMinutes] = endTimeInput.value.split(':').map(Number);

        const startInMinutes = startHours * 60 + startMinutes;
        const endInMinutes = endHours * 60 + endMinutes;

        let feedbackEl = document.getElementById('time-range-feedback');
        if (!feedbackEl) {
            feedbackEl = document.createElement('div');
            feedbackEl.id = 'time-range-feedback';
            feedbackEl.className = 'invalid-feedback';
            endTimeInput.parentElement.appendChild(feedbackEl);
        }

        if (endInMinutes <= startInMinutes) {
            endTimeInput.classList.add('is-invalid');
            endTimeInput.classList.remove('is-valid');
            feedbackEl.textContent = 'End time must be after start time';
            feedbackEl.style.display = 'block';
        } else {
            endTimeInput.classList.remove('is-invalid');
            endTimeInput.classList.add('is-valid');
            feedbackEl.textContent = '';
            feedbackEl.style.display = 'none';
        }
    }

    if (startTimeInput) {
        startTimeInput.addEventListener('change', function () {
            validateTime(this, 'start-time-feedback', 'Start time');
            validateTimeRange();
        });
        startTimeInput.addEventListener('blur', function () {
            validateTime(this, 'start-time-feedback', 'Start time');
            validateTimeRange();
        });
    }

    if (endTimeInput) {
        endTimeInput.addEventListener('change', function () {
            validateTime(this, 'end-time-feedback', 'End time');
            validateTimeRange();
        });
        endTimeInput.addEventListener('blur', function () {
            validateTime(this, 'end-time-feedback', 'End time');
            validateTimeRange();
        });
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
                detailsHtml += '<div class="col-12"><h6>What\'s Included</h6><ul>';
                data.inclusions.forEach(item => {
                    detailsHtml += `<li>${sanitizeInput(item)}</li>`;
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
                        <div class="addon-card-luxury">
                            <input class="form-check-input" type="checkbox" name="addons[]" value="${addonId}" id="addon-${addonId}" data-price="${addonPrice}" data-name="${addonDesc}">
                            <label class="form-check-label ms-2" for="addon-${addonId}" style="color: var(--text-secondary); cursor: pointer;">
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
            document.querySelectorAll('.package-details-luxury').forEach(c => c.style.display = 'none');

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
                { label: 'Office Address', value: '123 Photography Street, Manila City' },
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
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'File size must be less than 5MB',
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#ffffff'
                });
                this.value = '';
                proofPreview.innerHTML = '';
                return;
            }

            // File type validation
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please upload a valid image (JPG, PNG) or PDF file',
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#ffffff'
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Package Required',
                    text: 'Please select a package before submitting your booking.',
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#ffffff'
                });
                return;
            }

            // Validate payment method
            const selectedPaymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
            if (!selectedPaymentMethod) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Payment Method Required',
                    text: 'Please select a payment method.',
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#ffffff'
                });
                return;
            }

            // Validate proof of payment for online methods
            const paymentProof = document.getElementById('paymentProof');
            if (selectedPaymentMethod.value !== 'Cash' && !paymentProof.files.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Payment Proof Required',
                    text: 'Please upload proof of payment for online payment methods.',
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#ffffff'
                });
                return;
            }

            // Validate terms acceptance
            const termsCheckbox = document.getElementById('termsConfirm');
            if (!termsCheckbox.checked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Terms & Conditions',
                    text: 'Please accept the terms and conditions to proceed.',
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#ffffff'
                });
                return;
            }

            // Calculate total and confirm
            const total = updateBookingSummary();
            const downpayment = total * 0.25;

            Swal.fire({
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
            }).then((result) => {
                // Only submit if user explicitly clicked "Confirm Booking"
                if (result.isConfirmed) {
                    // Show loading state
                    const submitBtn = this.querySelector('.luxury-submit-btn');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    submitBtn.disabled = true;

                    // Submit form
                    this.submit();
                }
                // If user clicked "Review Again" (isDismissed or isDenied), do nothing
                // Form will NOT submit
            });
        });
    }

    // Initialize summary
    updateBookingSummary();
});
