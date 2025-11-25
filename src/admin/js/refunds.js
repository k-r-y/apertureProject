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

    // Save refund changes
    saveRefundBtn.addEventListener('click', function () {
        if (!currentRefundId) return;

        const status = document.getElementById('modalStatus').value;
        const notes = document.getElementById('modalNotes').value;

        saveRefundBtn.disabled = true;
        saveRefundBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('api/refunds.php?action=update_status', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                refundId: currentRefundId,
                status: status,
                notes: notes
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    LuxuryToast.show('Success', 'Refund updated successfully', 'success');
                    refundModal.hide();
                    fetchRefunds(currentFilter);
                } else {
                    LuxuryToast.show('Error', data.message || 'Failed to update refund', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                LuxuryToast.show('Error', 'Failed to update refund', 'error');
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
            <tr>
                <td class="text-light">#${refund.refundID}</td>
                <td class="text-light">${refund.bookingRef}</td>
                <td class="text-light">${refund.FirstName} ${refund.LastName}</td>
                <td class="text-light small">${refund.event_type}</td>
                <td class="text-gold">₱${parseFloat(refund.amount).toLocaleString()}</td>
                <td class="text-light small">${formatDate(refund.requested_at)}</td>
                <td>${getStatusBadge(refund.status)}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-gold" onclick="viewRefund(${refund.refundID})">
                        <i class="bi bi-eye"></i>
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
            'pending': '<span class="badge bg-warning text-dark">Pending</span>',
            'approved': '<span class="badge bg-info text-dark">Approved</span>',
            'processed': '<span class="badge bg-success">Processed</span>',
            'rejected': '<span class="badge bg-danger">Rejected</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
    }
});
