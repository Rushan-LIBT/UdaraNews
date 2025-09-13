<?php
require_once 'config.php';

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

// Get all news for display
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM news ORDER BY created_at DESC");
    $stmt->execute();
    $allNews = $stmt->fetchAll();
} catch (Exception $e) {
    $allNews = [];
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 10px;
        }

        .header h1 {
            text-align: center;
            font-size: 2.5rem;
        }

        .header p {
            text-align: center;
            margin-top: 0.5rem;
            opacity: 0.9;
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

        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
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
        <a href="index.html" class="btn btn-secondary back-link">← Back to Website</a>
        
        <div class="header">
            <h1>Udara NEWS Admin Panel</h1>
            <p>Manage your news articles and content</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h2>Add New Article</h2>
            <form method="POST" action="">
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
                    <label for="image">Image URL</label>
                    <input type="url" id="image" name="image" placeholder="https://example.com/image.jpg">
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
                                        <button onclick="editArticle(<?php echo $news['id']; ?>)" class="btn btn-success">Edit</button>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this article?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
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
        function editArticle(id) {
            // Simple edit functionality - in a real application, you might use a modal or separate page
            const title = prompt('Enter new title:');
            if (title) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const inputs = [
                    {name: 'action', value: 'edit'},
                    {name: 'id', value: id},
                    {name: 'title', value: title},
                    {name: 'summary', value: prompt('Enter summary:') || ''},
                    {name: 'content', value: prompt('Enter content:') || ''},
                    {name: 'image', value: prompt('Enter image URL:') || ''},
                    {name: 'category', value: prompt('Enter category (politics, sports, technology, business, general):') || 'general'},
                    {name: 'author', value: prompt('Enter author:') || 'Admin'}
                ];
                
                inputs.forEach(input => {
                    const field = document.createElement('input');
                    field.type = 'hidden';
                    field.name = input.name;
                    field.value = input.value;
                    form.appendChild(field);
                });
                
                const featured = confirm('Make this article featured?');
                if (featured) {
                    const featuredInput = document.createElement('input');
                    featuredInput.type = 'hidden';
                    featuredInput.name = 'is_featured';
                    featuredInput.value = '1';
                    form.appendChild(featuredInput);
                }
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>