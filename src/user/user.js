document.addEventListener("DOMContentLoaded", function () {
    // ===================================
    // SIDEBAR TOGGLE LOGIC
    // ===================================

    const sidebarToggle = document.getElementById('sidebar-toggle');
    const pageWrapper = document.getElementById('page-wrapper');
    const body = document.body;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                body.classList.toggle('sidebar-mobile-active');
            } else {
                body.classList.toggle('sidebar-mini');
            }
        });
    }

    if (pageWrapper) {
        pageWrapper.addEventListener('click', () => {
            if (window.innerWidth <= 768 && body.classList.contains('sidebar-mobile-active')) {
                body.classList.remove('sidebar-mobile-active');
            }
        });
    }

    body.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 &&
            body.classList.contains('sidebar-mobile-active') &&
            !document.getElementById('sidebar').contains(e.target) &&
            !sidebarToggle.contains(e.target)) {
            body.classList.remove('sidebar-mobile-active');
        }
    });

    window.addEventListener('resize', () => {
        const isMobile = window.innerWidth <= 768;
        if (!isMobile) {
            body.classList.remove('sidebar-mobile-active');
        } else {
            body.classList.remove('sidebar-mini');
        }
    });

    // ===================================
    // SECURITY & VALIDATION HELPERS
    // ===================================

    function sanitizeInput(input) {
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML;
    }

    // ===================================
    // REAL-TIME BOOKING SUMMARY LOGIC
    // ===================================

    const summaryPackageName = document.getElementById('summary-package-name');
    const summaryAddonsList = document.getElementById('summary-addons-list');
    const summaryAddonsContainer = document.getElementById('summary-addons-container');
    const totalPriceEl = document.getElementById('totalPrice');
    const downpaymentEl = document.getElementById('downpaymentAmount');

    // New Summary Elements
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
            const packagePrice = parseFloat(selectedPackageRadio.nextElementSibling.dataset.price);
            const packageName = selectedPackageRadio.nextElementSibling.dataset.name;
            total += packagePrice;

            if (summaryPackageName) {
                summaryPackageName.textContent = sanitizeInput(packageName);
            }
        } else {
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
                    addonItem.innerHTML = `<span>${sanitizeInput(addonName)}</span><span>₱${addonPrice.toLocaleString()}</span>`;
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

        // Calculate downpayment
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

    // ===================================
    // DATE AVAILABILITY CHECK
    // ===================================

    const eventDateInput = document.getElementById('eventDate');
    const feedbackDiv = document.getElementById('date-availability-feedback');

    if (eventDateInput) {
        eventDateInput.addEventListener('change', function () {
            const selectedDate = this.value;
            if (!selectedDate) return;

            // Validate date is in the future
            const today = new Date();
            const selected = new Date(selectedDate);
            today.setHours(0, 0, 0, 0);

            if (selected < today) {
                eventDateInput.classList.add('is-invalid');
                feedbackDiv.textContent = 'Please select a future date';
                return;
            }

            // Check availability via API
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
                        feedbackDiv.textContent = '';
                    } else {
                        eventDateInput.classList.remove('is-valid');
                        eventDateInput.classList.add('is-invalid');
                        feedbackDiv.textContent = sanitizeInput(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error checking availability:', error);
                    eventDateInput.classList.add('is-invalid');
                    feedbackDiv.textContent = 'Unable to check availability. Please try again.';
                });
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
                        <h6 class="mb-3"><i class="bi ${details.icon} me-2"></i>${sanitizeInput(details.title)}</h6>
                    `;

                    details.details.forEach(item => {
                        infoHTML += `<div class="mb-2">
                            <small class="text-muted-luxury d-block">${sanitizeInput(item.label)}</small>
                            <span>${sanitizeInput(item.value)}</span>
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
                alert('File size must be less than 5MB');
                this.value = '';
                proofPreview.innerHTML = '';
                return;
            }

            // File type validation
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload a valid image (JPG, PNG) or PDF file');
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
                alert('Please select a package');
                return;
            }

            // Validate payment method
            const selectedPaymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
            if (!selectedPaymentMethod) {
                alert('Please select a payment method');
                return;
            }

            // Validate proof of payment for online methods
            const paymentProof = document.getElementById('paymentProof');
            if (selectedPaymentMethod.value !== 'Cash' && !paymentProof.files.length) {
                alert('Please upload proof of payment for online payment methods.');
                return;
            }

            // Validate terms acceptance
            const termsCheckbox = document.getElementById('termsConfirm');
            if (!termsCheckbox.checked) {
                alert('Please accept the terms and conditions');
                return;
            }

            // Calculate total and confirm
            const total = updateBookingSummary();
            const downpayment = total * 0.25;

            if (confirm(`Confirm booking with downpayment of ₱${downpayment.toLocaleString()}?`)) {
                // Show loading state
                const submitBtn = this.querySelector('.luxury-submit-btn');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                submitBtn.disabled = true;

                // Submit form
                this.submit();
            }
        });
    }

    // Initialize summary
    updateBookingSummary();
});