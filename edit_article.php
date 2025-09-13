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
            max-width: 900px;
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
                <form method="POST" action="">
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
                        <label for="image">Featured Image URL</label>
                        <input type="url" id="image" name="image" value="<?php echo htmlspecialchars($article['image']); ?>" placeholder="https://example.com/image.jpg">
                        <?php if ($article['image']): ?>
                            <div class="image-preview">
                                <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="Current image" onerror="this.style.display='none'">
                            </div>
                        <?php endif; ?>
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
        // Image preview functionality
        document.getElementById('image').addEventListener('input', function() {
            const imageUrl = this.value;
            const preview = document.querySelector('.image-preview img');
            
            if (imageUrl && preview) {
                preview.src = imageUrl;
                preview.style.display = 'block';
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