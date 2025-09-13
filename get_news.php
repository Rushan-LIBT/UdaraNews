<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get parameters
    $category = isset($_GET['category']) ? $_GET['category'] : 'all';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 6; // Number of articles per page
    $offset = ($page - 1) * $limit;
    
    // Build query based on category
    $sql = "SELECT id, title, summary, content, image, category, author, created_at 
            FROM news";
    $params = [];
    
    if ($category !== 'all') {
        $sql .= " WHERE category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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
    
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) FROM news";
    $countParams = [];
    
    if ($category !== 'all') {
        $countSql .= " WHERE category = ?";
        $countParams[] = $category;
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalCount = $countStmt->fetchColumn();
    
    $response = [
        'success' => true,
        'news' => $formattedNews,
        'total' => $totalCount,
        'page' => $page,
        'limit' => $limit,
        'totalPages' => ceil($totalCount / $limit)
    ];
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error fetching news: ' . $e->getMessage()
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>