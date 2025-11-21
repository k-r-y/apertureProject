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

// Fetch all users (clients) for the dropdown
$query = "SELECT userID, fullName, email FROM users WHERE role = 'User' ORDER BY fullName";
$result = $conn->query($query);
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
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
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.02);
            cursor: pointer;
        }

        .upload-area:hover,
        .upload-area.drag-over {
            border-color: var(--gold);
            background: rgba(212, 175, 55, 0.05);
        }

        .upload-area i {
            font-size: 3rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .preview-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .preview-item .remove-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .preview-item .remove-btn:hover {
            background: #dc3545;
            transform: scale(1.1);
        }

        .preview-item .caption-input {
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.5);
            border: none;
            border-top: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 0.85rem;
        }

        .preview-item .caption-input:focus {
            outline: none;
            background: rgba(0, 0, 0, 0.7);
        }

        .upload-progress {
            display: none;
            margin-top: 1rem;
        }

        .upload-progress.active {
            display: block;
        }
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
                            <h3 class="serif text-gold mb-4">Select User & Upload Photos</h3>

                            <!-- User Selection -->
                            <div class="mb-4">
                                <label for="userSelect" class="form-label text-light">Select User</label>
                                <select id="userSelect" class="form-select" required>
                                    <option value="" selected disabled>Choose a user...</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['userID'] ?>">
                                            <?= htmlspecialchars($user['fullName']) ?> (<?= htmlspecialchars($user['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Upload Area -->
                            <div class="upload-area" id="uploadArea">
                                <i class="bi bi-cloud-upload"></i>
                                <h4 class="text-light mb-2">Drag & Drop Photos Here</h4>
                                <p class="text-secondary mb-3">or click to browse</p>
                                <button type="button" class="btn btn-gold" id="browseBtn">
                                    <i class="bi bi-folder2-open me-2"></i>Browse Files
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
                        <div class="stat-card">
                            <h5 class="text-gold mb-3"><i class="bi bi-info-circle me-2"></i>Upload Guidelines</h5>
                            <ul class="text-light small" style="line-height: 1.8;">
                                <li>Select the user who will receive these photos</li>
                                <li>Upload multiple photos at once</li>
                                <li>Add optional captions to each photo</li>
                                <li>Maximum file size: 5MB per photo</li>
                                <li>Supported formats: JPG, JPEG, PNG, GIF</li>
                                <li>Photos will be visible only to the selected user</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const browseBtn = document.getElementById('browseBtn');
        const previewGrid = document.getElementById('previewGrid');
        const uploadBtn = document.getElementById('uploadBtn');
        const userSelect = document.getElementById('userSelect');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');

        let selectedFiles = [];

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
                const reader = new FileReader();
                reader.onload = (e) => {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
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
            selectedFiles.splice(index, 1);
            renderPreviews();
            updateUploadButton();
        }

        function updateUploadButton() {
            uploadBtn.disabled = selectedFiles.length === 0 || !userSelect.value;
        }

        userSelect.addEventListener('change', updateUploadButton);

        // Upload photos
        uploadBtn.addEventListener('click', async () => {
            if (!userSelect.value) {
                alert('Please select a user');
                return;
            }

            if (selectedFiles.length === 0) {
                alert('Please select at least one photo');
                return;
            }

            const formData = new FormData();
            formData.append('userID', userSelect.value);

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
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            alert(`Successfully uploaded ${response.uploaded} photo(s)!`);
                            selectedFiles = [];
                            previewGrid.innerHTML = '';
                            fileInput.value = '';
                            userSelect.value = '';
                            updateUploadButton();
                        } else {
                            alert('Upload failed: ' + response.message);
                        }
                    } else {
                        alert('Upload failed. Please try again.');
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
