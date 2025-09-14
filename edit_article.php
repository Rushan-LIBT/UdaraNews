<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once 'config.php';

$article = null;
$message = '';
$messageType = '';

// Get article ID
$articleId = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$articleId) {
    header('Location: admin.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $pdo = getDBConnection();
        
        if ($_POST['action'] === 'update') {
            $stmt = $pdo->prepare("
                UPDATE news 
                SET title = ?, summary = ?, content = ?, image = ?, category = ?, author = ?, is_featured = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $_POST['title'],
                $_POST['summary'],
                $_POST['content'],
                $_POST['image'],
                $_POST['category'],
                $_POST['author'],
                isset($_POST['is_featured']) ? 1 : 0,
                $articleId
            ]);
            
            if ($result) {
                $message = 'Article updated successfully!';
                $messageType = 'success';
            }
        }
    } catch (Exception $e) {
        $message = 'Error updating article: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get article data
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$articleId]);
    $article = $stmt->fetch();
    
    if (!$article) {
        header('Location: admin.php');
        exit;
    }
} catch (Exception $e) {
    $message = 'Error loading article: ' . $e->getMessage();
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article - Udara NEWS Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            padding: 0.8rem 1.5rem;
            background: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }

        .message {
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 5px;
            font-weight: 500;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .edit-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
            font-family: inherit;
        }

        .form-group textarea#content {
            min-height: 200px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border: 2px solid #ecf0f1;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .checkbox-group label {
            margin: 0;
            font-weight: 500;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #ecf0f1;
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .article-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border-left: 4px solid #3498db;
        }

        .article-info h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .article-info p {
            color: #7f8c8d;
            margin-bottom: 0.3rem;
        }

        .image-preview {
            margin-top: 0.5rem;
        }

        .image-preview img {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            border: 2px solid #ecf0f1;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin.php" class="back-link">← Back to Admin Panel</a>
        
        <div class="header">
            <h1>Edit Article</h1>
            <p>Update article information and content</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($article): ?>
            <div class="article-info">
                <h3>Article Information</h3>
                <p><strong>Created:</strong> <?php echo date('F j, Y g:i A', strtotime($article['created_at'])); ?></p>
                <p><strong>Last Updated:</strong> <?php echo date('F j, Y g:i A', strtotime($article['updated_at'])); ?></p>
                <p><strong>Article ID:</strong> #<?php echo $article['id']; ?></p>
            </div>

            <div class="edit-form">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Article Title *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="author">Author *</label>
                            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($article['author']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="general" <?php echo $article['category'] === 'general' ? 'selected' : ''; ?>>General</option>
                                <option value="politics" <?php echo $article['category'] === 'politics' ? 'selected' : ''; ?>>Politics</option>
                                <option value="sports" <?php echo $article['category'] === 'sports' ? 'selected' : ''; ?>>Sports</option>
                                <option value="technology" <?php echo $article['category'] === 'technology' ? 'selected' : ''; ?>>Technology</option>
                                <option value="business" <?php echo $article['category'] === 'business' ? 'selected' : ''; ?>>Business</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_featured" name="is_featured" <?php echo $article['is_featured'] ? 'checked' : ''; ?>>
                                <label for="is_featured">⭐ Featured Article</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="summary">Article Summary *</label>
                        <textarea id="summary" name="summary" required placeholder="Brief summary of the article..."><?php echo htmlspecialchars($article['summary']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Featured Image</label>
                        <div class="image-upload-section">
                            <div class="upload-tabs">
                                <button type="button" class="tab-btn active" data-tab="upload">Upload Image</button>
                                <button type="button" class="tab-btn" data-tab="url">Use URL</button>
                            </div>

                            <div class="tab-content active" id="upload-tab">
                                <div class="file-upload-area" id="file-upload-area">
                                    <input type="file" id="image-file" name="image-file" accept="image/*" style="display: none;">
                                    <div class="upload-placeholder">
                                        <div class="upload-icon">📁</div>
                                        <p>Click here to upload an image</p>
                                        <small>JPG, PNG, GIF, WebP (Max 5MB)</small>
                                    </div>
                                    <div class="upload-preview" id="upload-preview" style="display: none;"></div>
                                </div>
                            </div>

                            <div class="tab-content" id="url-tab">
                                <input type="url" id="image-url" name="image" value="<?php echo htmlspecialchars($article['image']); ?>" placeholder="https://example.com/image.jpg">
                                <div class="url-preview" id="url-preview">
                                    <?php if ($article['image']): ?>
                                        <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="Current image" onerror="this.style.display='none'">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <input type="hidden" id="final-image-path" name="image" value="<?php echo htmlspecialchars($article['image']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="content">Full Article Content</label>
                        <textarea id="content" name="content" placeholder="Write the full article content here..."><?php echo htmlspecialchars($article['content']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="admin.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">💾 Update Article</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Image Upload Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            const fileUploadArea = document.getElementById('file-upload-area');
            const fileInput = document.getElementById('image-file');
            const uploadPreview = document.getElementById('upload-preview');
            const urlInput = document.getElementById('image-url');
            const urlPreview = document.getElementById('url-preview');
            const finalImagePath = document.getElementById('final-image-path');

            // Set initial tab based on current image
            const currentImage = '<?php echo addslashes($article['image']); ?>';
            if (currentImage && !currentImage.startsWith('uploads/')) {
                // Current image is a URL, switch to URL tab
                tabBtns.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                document.querySelector('[data-tab="url"]').classList.add('active');
                document.getElementById('url-tab').classList.add('active');
            }

            // Tab switching
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active classes
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    // Add active class to clicked tab
                    this.classList.add('active');
                    document.getElementById(targetTab + '-tab').classList.add('active');
                });
            });

            // File upload area click
            fileUploadArea.addEventListener('click', function() {
                fileInput.click();
            });

            // Drag and drop functionality
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileUpload(files[0]);
                }
            });

            // File input change
            fileInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    handleFileUpload(this.files[0]);
                }
            });

            // URL input change
            urlInput.addEventListener('input', function() {
                const url = this.value.trim();
                finalImagePath.value = url;
                if (url) {
                    showUrlPreview(url);
                } else {
                    urlPreview.innerHTML = '';
                }
            });

            function handleFileUpload(file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File too large. Maximum size is 5MB.');
                    return;
                }

                // Show loading state
                showUploadPreview(file, true);

                // Upload file
                const formData = new FormData();
                formData.append('image', file);

                fetch('upload_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        finalImagePath.value = data.image_path;
                        showUploadPreview(file, false, data.image_path);
                    } else {
                        alert('Upload failed: ' + data.message);
                        uploadPreview.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    alert('Upload failed. Please try again.');
                    uploadPreview.style.display = 'none';
                });
            }

            function showUploadPreview(file, isLoading, imagePath = null) {
                uploadPreview.style.display = 'block';

                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadPreview.innerHTML = `
                        <div class="file-info">
                            <img src="${e.target.result}" alt="Preview">
                            <div class="file-details">
                                <div class="file-name">${file.name}</div>
                                <div class="file-size">${formatFileSize(file.size)}</div>
                                ${isLoading ? '<div style="color: #3498db;">Uploading...</div>' : '<div style="color: #27ae60;">✓ Uploaded successfully</div>'}
                            </div>
                            ${!isLoading ? '<button type="button" class="remove-file" onclick="removeUploadedFile()">Remove</button>' : ''}
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }

            function showUrlPreview(url) {
                urlPreview.innerHTML = `
                    <img src="${url}" alt="URL Preview" onerror="this.style.display='none'">
                `;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Make removeUploadedFile global
            window.removeUploadedFile = function() {
                uploadPreview.style.display = 'none';
                fileInput.value = '';
                finalImagePath.value = '';
            }
        });

        // Auto-resize textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    </script>
</body>
</html>