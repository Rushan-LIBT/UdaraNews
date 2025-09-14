<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once 'config.php';

$article = null;
$articleId = $_GET['id'] ?? null;

if (!$articleId) {
    header('Location: admin.php');
    exit;
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
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - Udara NEWS</title>
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

        .article-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .article-header {
            position: relative;
            height: 400px;
            background: linear-gradient(135deg, #3498db, #2c3e50);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .article-header.has-image {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .article-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1;
        }

        .article-header-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
            padding: 2rem;
        }

        .article-meta {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .meta-item {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
        }

        .article-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .article-summary {
            font-size: 1.2rem;
            opacity: 0.9;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .article-content {
            padding: 3rem;
        }

        .article-body {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #2c3e50;
        }

        .article-body p {
            margin-bottom: 1.5rem;
        }

        .article-footer {
            background: #f8f9fa;
            padding: 2rem 3rem;
            border-top: 1px solid #ecf0f1;
        }

        .article-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .info-group h4 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-group p {
            color: #7f8c8d;
            font-weight: 500;
        }

        .category-badge {
            background: #3498db;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }

        .featured-badge {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }

        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
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
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .article-title {
                font-size: 2rem;
            }

            .article-content {
                padding: 2rem;
            }

            .article-footer {
                padding: 2rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin.php" class="back-link">← Back to Admin Panel</a>
        
        <div class="article-container">
            <div class="article-header <?php echo $article['image'] ? 'has-image' : ''; ?>" 
                 <?php if ($article['image']): ?>style="background-image: url('<?php echo htmlspecialchars($article['image']); ?>');"<?php endif; ?>>
                <div class="article-header-content">
                    <div class="article-meta">
                        <span class="meta-item"><?php echo ucfirst($article['category']); ?></span>
                        <span class="meta-item">By <?php echo htmlspecialchars($article['author']); ?></span>
                        <span class="meta-item"><?php echo date('M j, Y', strtotime($article['created_at'])); ?></span>
                        <?php if ($article['is_featured']): ?>
                            <span class="meta-item">⭐ Featured</span>
                        <?php endif; ?>
                    </div>
                    <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <p class="article-summary"><?php echo htmlspecialchars($article['summary']); ?></p>
                </div>
            </div>

            <div class="article-content">
                <div class="article-body">
                    <?php if ($article['content']): ?>
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    <?php else: ?>
                        <p><em>No full content available for this article.</em></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="article-footer">
                <div class="article-info">
                    <div class="info-group">
                        <h4>Category</h4>
                        <p><span class="category-badge"><?php echo ucfirst($article['category']); ?></span></p>
                    </div>
                    <div class="info-group">
                        <h4>Author</h4>
                        <p><?php echo htmlspecialchars($article['author']); ?></p>
                    </div>
                    <div class="info-group">
                        <h4>Published</h4>
                        <p><?php echo date('F j, Y g:i A', strtotime($article['created_at'])); ?></p>
                    </div>
                    <div class="info-group">
                        <h4>Status</h4>
                        <p>
                            <?php if ($article['is_featured']): ?>
                                <span class="featured-badge">Featured Article</span>
                            <?php else: ?>
                                Regular Article
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-primary">✏️ Edit Article</a>
                    <a href="admin.php" class="btn btn-secondary">📋 Back to Admin</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>