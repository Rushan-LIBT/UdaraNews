<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Check for session timeout (24 hours)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 86400) {
    session_destroy();
    header('Location: admin_login.php');
    exit;
}

require_once 'config.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php');
    exit;
}

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $stmt = $pdo->prepare("
                        INSERT INTO news (title, summary, content, image, category, author, is_featured) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['summary'],
                        $_POST['content'],
                        $_POST['image'],
                        $_POST['category'],
                        $_POST['author'],
                        isset($_POST['is_featured']) ? 1 : 0
                    ]);
                    $message = 'News article added successfully!';
                    $messageType = 'success';
                    break;
                    
                case 'edit':
                    $stmt = $pdo->prepare("
                        UPDATE news 
                        SET title = ?, summary = ?, content = ?, image = ?, category = ?, author = ?, is_featured = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['summary'],
                        $_POST['content'],
                        $_POST['image'],
                        $_POST['category'],
                        $_POST['author'],
                        isset($_POST['is_featured']) ? 1 : 0,
                        $_POST['id']
                    ]);
                    $message = 'News article updated successfully!';
                    $messageType = 'success';
                    break;
                    
                case 'delete':
                    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $message = 'News article deleted successfully!';
                    $messageType = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get all news for display and statistics
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM news ORDER BY created_at DESC");
    $stmt->execute();
    $allNews = $stmt->fetchAll();
    
    // Get statistics
    $totalArticles = count($allNews);
    $featuredArticles = count(array_filter($allNews, function($article) { return $article['is_featured']; }));
    $categories = array_count_values(array_column($allNews, 'category'));
    $todayArticles = count(array_filter($allNews, function($article) { 
        return date('Y-m-d', strtotime($article['created_at'])) === date('Y-m-d'); 
    }));
    
} catch (Exception $e) {
    $allNews = [];
    $totalArticles = $featuredArticles = $todayArticles = 0;
    $categories = [];
    $message = 'Error loading news: ' . $e->getMessage();
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Udara NEWS - Admin Panel</title>
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
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header h1 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            text-align: center;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid #3498db;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-label {
            color: #7f8c8d;
            margin-top: 0.5rem;
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

        .form-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .form-section h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ecf0f1;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background: #229954;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .news-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .news-table h2 {
            background: #34495e;
            color: white;
            padding: 1.5rem;
            margin: 0;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .featured-badge {
            background: #e74c3c;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .category-badge {
            background: #3498db;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .admin-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(52, 73, 94, 0.1);
            border-radius: 10px;
            border: 1px solid #ecf0f1;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-welcome {
            font-weight: 600;
            color: #2c3e50;
        }

        .login-time {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .admin-header-bar {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .admin-info {
                flex-direction: column;
                gap: 0.5rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-section {
                padding: 1rem;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                text-align: center;
            }

            table {
                font-size: 0.9rem;
            }

            th, td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header-bar">
            <a href="index.php" class="btn btn-secondary">← Back to Website</a>
            <div class="admin-info">
                <span class="admin-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
                <span class="login-time">Logged in: <?php echo date('M j, Y g:i A', $_SESSION['login_time']); ?></span>
                <a href="?logout=1" class="btn btn-danger" onclick="return confirm('Are you sure you want to logout?')">🚪 Logout</a>
            </div>
        </div>
        
        <div class="header">
            <h1>Udara NEWS Admin Panel</h1>
            <p>Manage your news articles and content</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalArticles; ?></div>
                <div class="stat-label">Total Articles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $featuredArticles; ?></div>
                <div class="stat-label">Featured Articles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $todayArticles; ?></div>
                <div class="stat-label">Published Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($categories); ?></div>
                <div class="stat-label">Categories Used</div>
            </div>
        </div>

        <div class="form-section">
            <h2>Add New Article</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="summary">Summary *</label>
                    <textarea id="summary" name="summary" required></textarea>
                </div>

                <div class="form-group">
                    <label for="content">Full Content</label>
                    <textarea id="content" name="content" rows="8"></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
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
                            <input type="url" id="image-url" name="image" placeholder="https://example.com/image.jpg">
                            <div class="url-preview" id="url-preview"></div>
                        </div>

                        <input type="hidden" id="final-image-path" name="image" value="">
                    </div>
                </div>

                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="general">General</option>
                        <option value="politics">Politics</option>
                        <option value="sports">Sports</option>
                        <option value="technology">Technology</option>
                        <option value="business">Business</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="author">Author *</label>
                    <input type="text" id="author" name="author" required>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_featured" name="is_featured">
                        <label for="is_featured">Featured Article</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Add Article</button>
            </form>
        </div>

        <div class="news-table">
            <h2>Manage Articles</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Featured</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allNews as $news): ?>
                            <tr>
                                <td><?php echo $news['id']; ?></td>
                                <td><?php echo htmlspecialchars(substr($news['title'], 0, 50)) . '...'; ?></td>
                                <td><span class="category-badge"><?php echo ucfirst($news['category']); ?></span></td>
                                <td><?php echo htmlspecialchars($news['author']); ?></td>
                                <td><?php echo $news['is_featured'] ? '<span class="featured-badge">Featured</span>' : ''; ?></td>
                                <td><?php echo date('M j, Y', strtotime($news['created_at'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit_article.php?id=<?php echo $news['id']; ?>" class="btn btn-success">✏️ Edit</a>
                                        <a href="view_article.php?id=<?php echo $news['id']; ?>" class="btn btn-primary">👁️ View</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this article?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
                                            <button type="submit" class="btn btn-danger">🗑️ Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Auto-clear form after successful submission
        <?php if ($messageType === 'success' && isset($_POST['action']) && $_POST['action'] === 'add'): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('form[method="POST"]').reset();
            });
        <?php endif; ?>

        // Confirmation for delete actions
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('button[type="submit"].btn-danger');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('⚠️ Are you sure you want to delete this article? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });
        });

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

                    // Clear final image path when switching tabs
                    finalImagePath.value = '';
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
                if (url) {
                    finalImagePath.value = url;
                    showUrlPreview(url);
                } else {
                    finalImagePath.value = '';
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
    </script>
</body>
</html>