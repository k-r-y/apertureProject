document.addEventListener('DOMContentLoaded', function () {
    const refundsTableBody = document.getElementById('refundsTableBody');
    const filterButtons = document.querySelectorAll('[data-status]');
    const refundModal = new bootstrap.Modal(document.getElementById('refundModal'));
    const saveRefundBtn = document.getElementById('saveRefundBtn');

    let currentRefundId = null;
    let currentFilter = 'all';

    // Initial load
    fetchRefunds('all');

    // Filter button listeners
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const status = this.getAttribute('data-status');
            currentFilter = status;
            fetchRefunds(status);
        });
    });

    // Toggle proof upload visibility
    const modalStatus = document.getElementById('modalStatus');
    const proofContainer = document.getElementById('proofUploadContainer');

    modalStatus.addEventListener('change', function () {
        if (this.value === 'processed') {
            proofContainer.style.display = 'block';
        } else {
            proofContainer.style.display = 'none';
        }
    });

    // Save refund changes
    saveRefundBtn.addEventListener('click', function () {
        if (!currentRefundId) return;

        const status = modalStatus.value;
        const notes = document.getElementById('modalNotes').value;
        const fileInput = document.getElementById('refundProof');

        if (status === 'processed' && fileInput.files.length === 0) {
            LuxuryToast.show({ message: 'Please upload a proof of refund', type: 'error' });
            return;
        }

        saveRefundBtn.disabled = true;
        saveRefundBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        const formData = new FormData();
        formData.append('refundId', currentRefundId);
        formData.append('status', status);
        formData.append('notes', notes);
        if (fileInput.files.length > 0) {
            formData.append('refundProof', fileInput.files[0]);
        }

        fetch('api/refunds.php?action=update_status', {
            method: 'POST',
            credentials: 'include',
            body: formData // No Content-Type header needed for FormData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    LuxuryToast.show({ message: 'Refund updated successfully', type: 'success' });
                    refundModal.hide();
                    fetchRefunds(currentFilter);
                    // Reset file input
                    fileInput.value = '';
                    proofContainer.style.display = 'none';
                } else {
                    LuxuryToast.show({ message: data.message || 'Failed to update refund', type: 'error' });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                LuxuryToast.show({ message: 'Failed to update refund', type: 'error' });
            })
            .finally(() => {
                saveRefundBtn.disabled = false;
                saveRefundBtn.textContent = 'Save Changes';
            });
    });

    // Fetch refunds
    function fetchRefunds(status) {
        refundsTableBody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted">Loading...</td></tr>';

        fetch(`api/refunds.php?action=get_all&status=${status}`, {
            credentials: 'include'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderRefunds(data.refunds);
                } else {
                    refundsTableBody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-danger">Error: ${data.message}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                refundsTableBody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-danger">Failed to load refunds</td></tr>';
            });
    }

    // Render refunds table
    function renderRefunds(refunds) {
        if (refunds.length === 0) {
            refundsTableBody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted">No refunds found</td></tr>';
            return;
        }

        refundsTableBody.innerHTML = refunds.map(refund => `
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td class="text-gold fw-bold">#${refund.refundID}</td>
                <td class="text-light text-opacity-75">${refund.bookingRef}</td>
                <td class="text-light fw-medium">${refund.FirstName} ${refund.LastName}</td>
                <td class="text-muted small">${refund.event_type}</td>
                <td class="text-gold font-monospace">₱${parseFloat(refund.amount).toLocaleString()}</td>
                <td class="text-muted small">${formatDate(refund.requested_at)}</td>
                <td>${getStatusBadge(refund.status)}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-gold border-0" onclick="viewRefund(${refund.refundID})" title="View Details">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // View refund details
    window.viewRefund = function (refundId) {
        fetch(`api/refunds.php?action=get_all&status=all`, {
            credentials: 'include'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const refund = data.refunds.find(r => r.refundID == refundId);
                    if (refund) {
                        populateModal(refund);
                        refundModal.show();
                    }
                }
            });
    };

    // Populate modal
    function populateModal(refund) {
        currentRefundId = refund.refundID;
        document.getElementById('modalBookingRef').textContent = refund.bookingRef;
        document.getElementById('modalClientName').textContent = `${refund.FirstName} ${refund.LastName}`;
        document.getElementById('modalEventType').textContent = refund.event_type;
        document.getElementById('modalEventDate').textContent = formatDate(refund.event_date);
        document.getElementById('modalAmount').textContent = `₱${parseFloat(refund.amount).toLocaleString()}`;
        document.getElementById('modalRequested').textContent = formatDate(refund.requested_at);
        document.getElementById('modalReason').textContent = refund.reason || 'No reason provided';
        document.getElementById('modalStatus').value = refund.status;
        document.getElementById('modalNotes').value = refund.notes || '';
    }

    // Helper: Format date
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Helper: Get status badge
    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 fw-normal px-3">Pending</span>',
            'approved': '<span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 fw-normal px-3">Approved</span>',
            'processed': '<span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 fw-normal px-3">Processed</span>',
            'rejected': '<span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fw-normal px-3">Rejected</span>'
        };
        return badges[status] || '<span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 fw-normal px-3">Unknown</span>';
    }
});
