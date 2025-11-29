<?php
// photoUpload.php - Admin page to upload photos for users

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/auth.php';

// Check if user is admin
if (!isset($_SESSION["userId"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    header("Location: ../logIn.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Upload - Aperture Admin</title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../libs/sweetalert2/sweetalert2.min.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        
    </style>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">Upload Photos</h1>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="stat-card">
                            <h3 class="serif text-gold mb-4">Select Booking & Upload Photos</h3>

                            <!-- Booking Selection -->
                            <div class="mb-3">
                                <label for="bookingSelect" class="form-label text-light">Select Booking</label>
                                <select id="bookingSelect" class="form-select" required>
                                    <option value="" selected disabled>Loading eligible bookings...</option>
                                </select>
                                <div class="form-text text-muted">Only fully paid completed OR post-production bookings are shown.</div>
                            </div>

                            <!-- Google Drive Link -->
                            <div class="mb-4">
                                <label for="gdriveLink" class="form-label text-light">Google Drive Link (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-gold"><i class="bi bi-google"></i></span>
                                    <input type="url" class="form-control bg-dark text-light border-secondary" id="gdriveLink" placeholder="https://drive.google.com/...">
                                </div>
                                <div class="form-text text-muted">This link will be accessible to the user in their gallery.</div>
                            </div>

                            <!-- Photo Type Selection -->
                            <div class="mb-4">
                                <label class="form-label text-light">Photo Type</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="photoType" id="typeEdited" value="edited" checked>
                                        <label class="form-check-label text-light" for="typeEdited">
                                            Edited Photos (Final Delivery)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="photoType" id="typeRaw" value="raw">
                                        <label class="form-check-label text-light" for="typeRaw">
                                            Raw Photos (Unedited)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Area -->
                            <div class="upload-area" id="uploadArea">
                                <i class="bi bi-cloud-upload"></i>
                                <h4 class="text-light mb-2">Drag & Drop Photos Here</h4>
                                <p class="text-secondary mb-3">or click to browse</p>
                                <button type="button" class="btn btn-gold" id="browseBtn">
                                    <i class="bi bi-folder2-open me-2 text-dark"></i>Browse Files
                                </button>
                                <input type="file" id="fileInput" multiple accept="image/*" class="d-none">
                                <p class="text-secondary small mt-3 mb-0">Supported: JPG, PNG, GIF (Max 5MB each)</p>
                            </div>

                            <!-- Preview Grid -->
                            <div class="preview-grid" id="previewGrid"></div>

                            <!-- Upload Progress -->
                            <div class="upload-progress" id="uploadProgress">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" 
                                         role="progressbar" style="width: 0%" id="progressBar"></div>
                                </div>
                                <p class="text-secondary small mt-2 mb-0" id="progressText">Uploading...</p>
                            </div>

                            <!-- Upload Button -->
                            <div class="mt-4">
                                <button type="button" class="btn btn-gold btn-lg" id="uploadBtn" disabled>
                                    <i class="bi bi-upload me-2"></i>Upload Photos
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="neo-card">
                            <h5 class="text-gold"><i class="bi bi-info-circle me-2"></i>Upload Guidelines</h5>
                            <ul class="text-light small mb-auto" style="line-height: 1.8;">
                                <li>Select a completed/paid booking</li>
                                <li>Add a Google Drive link for full gallery access</li>
                                <li>Upload highlight photos directly to the site</li>
                                <li>Maximum file size: 5MB per photo</li>
                                <li>Supported formats: JPG, JPEG, PNG, GIF</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script src="js/notifications.js"></script>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const browseBtn = document.getElementById('browseBtn');
        const previewGrid = document.getElementById('previewGrid');
        const uploadBtn = document.getElementById('uploadBtn');
        const bookingSelect = document.getElementById('bookingSelect');
        const gdriveLinkInput = document.getElementById('gdriveLink');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');

        let selectedFiles = [];
        let bookingsData = {};

        // Load eligible bookings
        fetch('api/get_eligible_bookings.php')
            .then(r => r.json())
            .then(data => {
                if (data.success && data.bookings.length > 0) {
                    bookingSelect.innerHTML = '<option value="" selected disabled>Choose a booking...</option>' + 
                        data.bookings.map(b => {
                            bookingsData[b.bookingID] = b;
                            return `<option value="${b.bookingID}">#${b.bookingID} - ${b.FirstName} ${b.LastName} (${b.event_type} on ${b.event_date})</option>`;
                        }).join('');
                } else {
                    bookingSelect.innerHTML = '<option value="" disabled>No eligible bookings found</option>';
                }
            });

        // Auto-fill GDrive link if exists
        bookingSelect.addEventListener('change', () => {
            const bookingId = bookingSelect.value;
            if (bookingId && bookingsData[bookingId] && bookingsData[bookingId].gdrive_link) {
                gdriveLinkInput.value = bookingsData[bookingId].gdrive_link;
            } else {
                gdriveLinkInput.value = '';
            }
            updateUploadButton();
        });

        // Browse button click
        browseBtn.addEventListener('click', () => fileInput.click());
        uploadArea.addEventListener('click', (e) => {
            if (e.target === uploadArea || e.target.closest('.upload-area') && !e.target.closest('button')) {
                fileInput.click();
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            handleFiles(e.dataTransfer.files);
        });

        function handleFiles(files) {
            const validFiles = Array.from(files).filter(file => {
                if (!file.type.startsWith('image/')) {
                    alert(`${file.name} is not an image file`);
                    return false;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert(`${file.name} is too large (max 5MB)`);
                    return false;
                }
                return true;
            });

            selectedFiles = [...selectedFiles, ...validFiles];
            renderPreviews();
            updateUploadButton();
        }

        function renderPreviews() {
            previewGrid.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                if (!file) return; // Skip null entries
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.setAttribute('data-file-index', index);
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button class="remove-btn" onclick="removeFile(${index})">
                            <i class="bi bi-x"></i>
                        </button>
                        <input type="text" class="caption-input form-control" 
                               placeholder="Add caption (optional)" 
                               data-index="${index}">
                    `;
                    previewGrid.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeFile(index) {
            selectedFiles[index] = null;
            selectedFiles = selectedFiles.filter(f => f !== null);
            renderPreviews();
            updateUploadButton();
        }

        function showErrorModal(response) {
            const errorList = response.failed.map(item => 
                `<li><strong>${item.filename}</strong>: ${item.reason}</li>`
            ).join('');
            
            Swal.fire({
                icon: response.uploaded > 0 ? 'warning' : 'error',
                title: response.uploaded > 0 ? 'Partial Upload' : 'Upload Failed',
                html: `
                    <div class="text-start">
                        <p><strong>Uploaded:</strong> ${response.uploaded} of ${response.total}</p>
                        ${response.failed.length > 0 ? `
                            <p class="mt-3"><strong>Failed Images:</strong></p>
                            <ul class="text-danger">${errorList}</ul>
                            <p class="text-muted small mt-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Failed images remain in the preview. Fix the issues and try uploading again.
                            </p>
                        ` : ''}
                    </div>
                `,
                confirmButtonText: 'OK',
                width: '600px',
                confirmButtonColor: '#D4AF37'
            });
        }

        function updateUploadButton() {
            // Enable if booking selected AND (files selected OR gdrive link changed)
            // For simplicity, we'll just require booking selected and at least one file OR a link
            // But the backend expects photos. If user just wants to update link, we should allow that too?
            // The current backend requires photos. Let's stick to requiring photos for now, or modify backend.
            // Requirement: "uploading an image... also add the gdrive link". Implies both.
            // But practically, one might want to just add link.
            // Let's allow upload if booking is selected. If no files, we just update link.
            
            const hasBooking = !!bookingSelect.value;
            const hasFiles = selectedFiles.length > 0;
            const hasLink = !!gdriveLinkInput.value;
            
            uploadBtn.disabled = !(hasBooking && (hasFiles || hasLink));
        }
        
        gdriveLinkInput.addEventListener('input', updateUploadButton);

        // Upload photos
        uploadBtn.addEventListener('click', async () => {
            if (!bookingSelect.value) {
                alert('Please select a booking');
                return;
            }

            const formData = new FormData();
            formData.append('bookingID', bookingSelect.value);
            formData.append('gdriveLink', gdriveLinkInput.value);
            
            // Add Photo Type
            const photoType = document.querySelector('input[name="photoType"]:checked').value;
            formData.append('photoType', photoType);

            selectedFiles.forEach((file, index) => {
                formData.append('photos[]', file);
                const captionInput = document.querySelector(`input[data-index="${index}"]`);
                if (captionInput && captionInput.value) {
                    formData.append('captions[]', captionInput.value);
                } else {
                    formData.append('captions[]', '');
                }
            });

            uploadBtn.disabled = true;
            uploadProgress.classList.add('active');
            progressBar.style.width = '0%';

            try {
                const xhr = new XMLHttpRequest();

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                        progressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status === 200) {
                        let response;
                        try {
                             response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            console.error("Invalid JSON response", xhr.responseText);
                            alert("Server error: Invalid response");
                            return;
                        }
                        
                        // Mark successful uploads
                        if (response.successful && response.successful.length > 0) {
                            response.successful.forEach(item => {
                                const previewItem = document.querySelector(`[data-file-index="${item.index}"]`);
                                if (previewItem) {
                                    previewItem.classList.add('success');
                                }
                                selectedFiles[item.index] = null; // Mark for removal
                            });
                        }
                        
                        // Mark failed uploads with errors
                        if (response.failed && response.failed.length > 0) {
                            response.failed.forEach(item => {
                                const previewItem = document.querySelector(`[data-file-index="${item.index}"]`);
                                if (previewItem) {
                                    previewItem.classList.add('error');
                                    const errorBadge = document.createElement('div');
                                    errorBadge.className = 'error-badge';
                                    errorBadge.textContent = item.reason;
                                    previewItem.appendChild(errorBadge);
                                }
                            });
                            showErrorModal(response);
                        }
                        
                        // Handle success (link updated or photos uploaded)
                        if (response.success) {
                             setTimeout(() => {
                                selectedFiles = selectedFiles.filter(f => f !== null);
                                
                                if (selectedFiles.length === 0) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                    previewGrid.innerHTML = '';
                                    fileInput.value = '';
                                    // Don't clear booking/link immediately so user sees it
                                } else {
                                    renderPreviews();
                                }
                                updateUploadButton();
                            }, 1500);
                        } else {
                             Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Operation failed'
                            });
                        }

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: 'Server error. Please try again.'
                        });
                    }
                    uploadProgress.classList.remove('active');
                    uploadBtn.disabled = false;
                });

                xhr.addEventListener('error', () => {
                    alert('Upload failed. Please try again.');
                    uploadProgress.classList.remove('active');
                    uploadBtn.disabled = false;
                });

                xhr.open('POST', 'uploadPhoto.php', true);
                xhr.send(formData);

            } catch (error) {
                console.error('Upload error:', error);
                alert('Upload failed. Please try again.');
                uploadProgress.classList.remove('active');
                uploadBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
