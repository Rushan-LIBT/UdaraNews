<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get search query
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    if (empty($query)) {
        $response = [
            'success' => false,
            'message' => 'Search query is required'
        ];
    } else {
        // Search in title, summary, and content
        $sql = "SELECT id, title, summary, content, image, category, author, created_at 
                FROM news 
                WHERE title LIKE ? OR summary LIKE ? OR content LIKE ?
                ORDER BY created_at DESC 
                LIMIT 20";
        
        $searchTerm = '%' . $query . '%';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $news = $stmt->fetchAll();
        
        // Format the data
        $formattedNews = [];
        foreach ($news as $article) {
            $formattedNews[] = [
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
            'news' => $formattedNews,
            'query' => $query,
            'count' => count($formattedNews)
        ];
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error searching news: ' . $e->getMessage()
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>