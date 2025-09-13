<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get featured news articles
    $sql = "SELECT id, title, summary, content, image, category, author, created_at 
            FROM news 
            WHERE is_featured = 1 
            ORDER BY created_at DESC 
            LIMIT 4";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $featured = $stmt->fetchAll();
    
    // Format the data
    $formattedFeatured = [];
    foreach ($featured as $article) {
        $formattedFeatured[] = [
            'id' => $article['id'],
            'title' => $article['title'],
            'summary' => $article['summary'],
            'content' => $article['content'],
            'image' => $article['image'],
            'category' => ucfirst($article['category']),
            'author' => $article['author'],
            'date' => $article['created_at']
        ];
    }
    
    $response = [
        'success' => true,
        'featured' => $formattedFeatured
    ];
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error fetching featured news: ' . $e->getMessage()
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>