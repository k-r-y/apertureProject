<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../logIn.php");
    exit;
}

$clientId = $_GET['id'] ?? 0;

// Get client info
// Get client info
$stmt = $conn->prepare("
    SELECT u.*, 
    (SELECT COALESCE(SUM(CASE WHEN is_fully_paid = 1 THEN total_price ELSE downpayment_amount END), 0) 
     FROM bookings 
     WHERE userID = u.userID AND booking_status != 'cancelled') as total_spent
    FROM users u 
    WHERE u.userID = ?
");
$stmt->bind_param("i", $clientId);
$stmt->execute();
$client = $stmt->get_result()->fetch_assoc();

if (!$client) {
    header("Location: client.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client: <?= htmlspecialchars($client['FirstName'] . ' ' . $client['LastName']) ?> - Aperture</title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
</head>
<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>
    
    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid">
                <!-- Client Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="header-title m-0"><?= htmlspecialchars($client['FirstName'] . ' ' . $client['LastName']) ?></h1>
                        <p class="text-muted"><?= htmlspecialchars($client['Email']) ?></p>
                    </div>
                    <a href="client.php" class="btn btn-ghost"><i class="bi bi-arrow-left me-2"></i>Back to Clients</a>
                </div>

                <!-- Tags Row -->
                <div class="mb-4">
                    <div class="d-flex gap-2 flex-wrap" id="clientTags">
                        <!-- Tags loaded via JS -->
                    </div>
                    <button class="btn btn-sm btn-outline-gold mt-2" onclick="showAddTagModal()">
                        <i class="bi bi-tag me-1"></i>Add Tag
                    </button>
                </div>

                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Booking History -->
                        <div class="neo-card mb-4">
                            <h5 class="card-header-title mb-3">Booking History</h5>
                            <div id="bookingHistory">Loading...</div>
                        </div>

                        <!-- Communication Log -->
                        <div class="neo-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-header-title m-0">Communication Log</h5>
                                <button class="btn btn-sm btn-gold" onclick="showAddCommunicationModal()">
                                    <i class="bi bi-plus-lg me-1"></i>Log Communication
                                </button>
                            </div>
                            <div id="communicationLog">Loading...</div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Client Info -->
                        <div class="neo-card mb-4">
                            <h5 class="card-header-title mb-3">Client Information</h5>
                            <div class="mb-2"><i class="bi bi-envelope me-2 text-gold"></i><?= htmlspecialchars($client['Email']) ?></div>
                            <div class="mb-2"><i class="bi bi-phone me-2 text-gold"></i><?= htmlspecialchars($client['contactNo'] ?? 'Not provided') ?></div>
                            <div class="mb-2"><i class="bi bi-calendar me-2 text-gold"></i>Joined: <?= date('M d, Y', strtotime($client['created_at'])) ?></div>
                            <div class="mb-2"><i class="bi bi-cash-stack me-2 text-gold"></i>Total Spent: ₱<?= number_format($client['total_spent'], 2) ?></div>
                        </div>

                        <!-- Notes -->
                        <div class="neo-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-header-title m-0">Notes</h5>
                                <button class="btn btn-sm btn-gold" onclick="showAddNoteModal()">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            <div id="clientNotes">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-gold">Add Note</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea id="noteText" class="form-control bg-dark text-light border-secondary" rows="4" placeholder="Enter note..."></textarea>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gold" onclick="saveNote()">Save Note</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Tag Modal -->
    <div class="modal fade" id="addTagModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-gold">Add Tag</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select id="tagSelect" class="form-select bg-dark text-light border-secondary">
                        <!-- Options loaded via JS -->
                    </select>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gold" onclick="assignTag()">Assign Tag</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Communication Modal -->
    <div class="modal fade" id="addCommunicationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-gold">Log Communication</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-light">Type</label>
                        <select id="commType" class="form-select bg-dark text-light border-secondary">
                            <option value="email">Email</option>
                            <option value="call">Phone Call</option>
                            <option value="meeting">Meeting</option>
                            <option value="message">Message</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Subject</label>
                        <input type="text" id="commSubject" class="form-control bg-dark text-light border-secondary" placeholder="Subject...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Notes</label>
                        <textarea id="commNotes" class="form-control bg-dark text-light border-secondary" rows="3" placeholder="Details..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Date/Time</label>
                        <input type="datetime-local" id="commDate" class="form-control bg-dark text-light border-secondary" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gold" onclick="saveCommunication()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/feedback.js"></script>
    <script>
        const clientId = <?= $clientId ?>;
        
        // Load all data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadClientTags();
            loadClientNotes();
            loadBookingHistory();
            loadCommunicationLog();
            loadAvailableTags();
        });

        function loadClientTags() {
            fetch(`api/crm.php?action=get_tags&userId=${clientId}`)
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('clientTags');
                    if (data.success && data.tags.length > 0) {
                        container.innerHTML = data.tags.map(tag => `
                            <span class="badge" style="background-color: ${tag.tag_color}; cursor: pointer;" onclick="removeTag(${tag.tagID})">
                                ${tag.tag_name} <i class="bi bi-x"></i>
                            </span>
                        `).join('');
                    } else {
                        container.innerHTML = '<span class="text-muted small">No tags</span>';
                    }
                });
        }

        function loadClientNotes() {
            fetch(`api/crm.php?action=get_notes&userId=${clientId}`)
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('clientNotes');
                    if (data.success && data.notes.length > 0) {
                        container.innerHTML = data.notes.map(note => `
                            <div class="border-bottom border-secondary pb-2 mb-2">
                                <p class="text-light mb-1">${note.note}</p>
                                <small class="text-muted">By ${note.FirstName} - ${new Date(note.created_at).toLocaleString()}</small>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p class="text-muted small">No notes yet</p>';
                    }
                });
        }

        function loadBookingHistory() {
            fetch(`../user/getAppointments.php?userId=${clientId}&admin=1`)
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('bookingHistory');
                    if (data.success && data.appointments.length > 0) {
                        container.innerHTML = data.appointments.map(b => `
                            <div class="border-bottom border-secondary pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-gold">${b.eventType}</strong>
                                    <span class="badge bg-${b.bookingStatus === 'confirmed' ? 'success' : 'warning'}">${b.bookingStatus}</span>
                                </div>
                                <div class="text-light">Date: ${b.eventDateFormatted}</div>
                                <div class="text-light">Total: ${b.totalAmountFormatted}</div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p class="text-muted">No bookings yet</p>';
                    }
                });
        }

        function loadCommunicationLog() {
            fetch(`api/crm.php?action=get_communications&userId=${clientId}`)
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('communicationLog');
                    if (data.success && data.communications.length > 0) {
                        container.innerHTML = data.communications.map(c => `
                            <div class="border-bottom border-secondary pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-gold"><i class="bi bi-${getCommIcon(c.type)} me-1"></i>${c.subject || c.type}</strong>
                                    <small class="text-muted">${new Date(c.communication_date).toLocaleString()}</small>
                                </div>
                                <p class="text-light mb-1">${c.notes || 'No details'}</p>
                                <small class="text-muted">By ${c.FirstName}</small>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p class="text-muted">No communications logged</p>';
                    }
                });
        }

        function getCommIcon(type) {
            const icons = {email: 'envelope', call: 'telephone', meeting: 'calendar', message: 'chat'};
            return icons[type] || 'chat';
        }

        function showAddNoteModal() {
            new bootstrap.Modal(document.getElementById('addNoteModal')).show();
        }

        function saveNote() {
            const note = document.getElementById('noteText').value;
            if (!note) return;

            fetch('api/crm.php?action=add_note', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({userId: clientId, note})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addNoteModal')).hide();
                    document.getElementById('noteText').value = '';
                    loadClientNotes();
                    LuxuryToast.show('Success', 'Note added', 'success');
                }
            });
        }

        function loadAvailableTags() {
            fetch('api/crm.php?action=get_all_tags')
                .then(r => r.json())
                .then(data => {
                    const select = document.getElementById('tagSelect');
                    select.innerHTML = data.tags.map(t => `<option value="${t.tagID}">${t.tag_name}</option>`).join('');
                });
        }

        function showAddTagModal() {
            new bootstrap.Modal(document.getElementById('addTagModal')).show();
        }

        function assignTag() {
            const tagId = document.getElementById('tagSelect').value;
            fetch('api/crm.php?action=add_tag', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({userId: clientId, tagId})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addTagModal')).hide();
                    loadClientTags();
                    LuxuryToast.show('Success', 'Tag assigned', 'success');
                }
            });
        }

        function removeTag(tagId) {
            if (!confirm('Remove this tag?')) return;
            fetch('api/crm.php?action=remove_tag', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({userId: clientId, tagId})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    loadClientTags();
                    LuxuryToast.show('Success', 'Tag removed', 'success');
                }
            });
        }

        function showAddCommunicationModal() {
            new bootstrap.Modal(document.getElementById('addCommunicationModal')).show();
        }

        function saveCommunication() {
            const data = {
                userId: clientId,
                type: document.getElementById('commType').value,
                subject: document.getElementById('commSubject').value,
                notes: document.getElementById('commNotes').value,
                date: document.getElementById('commDate').value.replace('T', ' ') + ':00'
            };

            fetch('api/crm.php?action=log_communication', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addCommunicationModal')).hide();
                    loadCommunicationLog();
                    LuxuryToast.show('Success', 'Communication logged', 'success');
                }
            });
        }
    </script>
</body>
</html>
