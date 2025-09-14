<?php
// Prevent any output before headers
ob_start();

// Force database initialization - ONLY when explicitly called
require_once 'config.php';

// Clean any previous output
ob_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Initialization - Udara NEWS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        .info { color: #3498db; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Initialization</h1>

<?php
try {
    echo "<p class='info'>Initializing database...</p>";
    initializeDatabase();
    echo "<p class='success'>✓ Database initialization completed</p>";

    // Test the connection and show results
    $pdo = getDBConnection();

    // Count articles by category
    $stmt = $pdo->prepare('SELECT category, COUNT(*) as count FROM news GROUP BY category');
    $stmt->execute();
    $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Articles by Category:</h2>";
    echo "<ul>";
    foreach ($counts as $count) {
        echo "<li>{$count['category']}: {$count['count']} articles</li>";
    }
    echo "</ul>";

    // Show featured articles
    $stmt = $pdo->prepare('SELECT title, category FROM news WHERE is_featured = 1');
    $stmt->execute();
    $featured = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Featured Articles:</h2>";
    echo "<ul>";
    foreach ($featured as $article) {
        echo "<li>[{$article['category']}] {$article['title']}</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='index.php'>Go to Home Page</a> | <a href='politics.php'>Go to Politics Page</a> | <a href='debug_content.php'>Debug Content</a></p>";
?>
    </div>
</body>
</html>