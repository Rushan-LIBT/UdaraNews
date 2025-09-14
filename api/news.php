<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get request parameters
$action = $_GET['action'] ?? 'get_all';
$category = $_GET['category'] ?? '';
$limit = (int)($_GET['limit'] ?? 20);
$offset = (int)($_GET['offset'] ?? 0);
$page = (int)($_GET['page'] ?? 1);
$search = $_GET['search'] ?? '';
$featured = isset($_GET['featured']) ? (bool)$_GET['featured'] : null;

// Calculate offset from page if page is provided
if ($page > 1) {
    $offset = ($page - 1) * $limit;
}

try {
    $pdo = getDBConnection();
    $response = ['success' => true, 'data' => []];

    switch ($action) {
        case 'get_all':
            $sql = 'SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news ORDER BY created_at DESC LIMIT ? OFFSET ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$limit, $offset]);
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count
            $countStmt = $pdo->prepare('SELECT COUNT(*) as total FROM news');
            $countStmt->execute();
            $response['total'] = (int)$countStmt->fetch()['total'];
            break;

        case 'get_by_category':
            if (empty($category)) {
                throw new Exception('Category parameter is required');
            }
            $sql = 'SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news WHERE category = ? ORDER BY created_at DESC LIMIT ? OFFSET ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category, $limit, $offset]);
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count for category
            $countStmt = $pdo->prepare('SELECT COUNT(*) as total FROM news WHERE category = ?');
            $countStmt->execute([$category]);
            $response['total'] = (int)$countStmt->fetch()['total'];
            break;

        case 'get_featured':
            $whereClause = 'WHERE is_featured = 1';
            $params = [];

            if (!empty($category)) {
                $whereClause .= ' AND category = ?';
                $params[] = $category;
            }

            $sql = "SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count
            $countSql = str_replace('SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news', 'SELECT COUNT(*) as total FROM news', explode(' LIMIT', $sql)[0]);
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute(array_slice($params, 0, -2));
            $response['total'] = (int)$countStmt->fetch()['total'];
            break;

        case 'search':
            if (empty($search)) {
                throw new Exception('Search query parameter is required');
            }
            $searchTerm = '%' . $search . '%';
            $whereClause = 'WHERE (title LIKE ? OR summary LIKE ? OR content LIKE ? OR author LIKE ?)';
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];

            if (!empty($category)) {
                $whereClause .= ' AND category = ?';
                $params[] = $category;
            }

            $sql = "SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count
            $countSql = str_replace('SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news', 'SELECT COUNT(*) as total FROM news', explode(' LIMIT', $sql)[0]);
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute(array_slice($params, 0, -2));
            $response['total'] = (int)$countStmt->fetch()['total'];
            break;

        case 'get_stats':
            $stats = [];

            // Total articles
            $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM news');
            $stmt->execute();
            $stats['total_articles'] = (int)$stmt->fetch()['total'];

            // Articles by category
            $stmt = $pdo->prepare('SELECT category, COUNT(*) as count FROM news GROUP BY category ORDER BY count DESC');
            $stmt->execute();
            $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Featured articles count
            $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM news WHERE is_featured = 1');
            $stmt->execute();
            $stats['featured_count'] = (int)$stmt->fetch()['total'];

            // Recent articles (last 7 days)
            $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM news WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)');
            $stmt->execute();
            $stats['recent_count'] = (int)$stmt->fetch()['total'];

            $response['data'] = $stats;
            break;

        case 'get_single':
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Valid ID parameter is required');
            }
            $sql = 'SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news WHERE id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$article) {
                throw new Exception('Article not found');
            }
            $response['data'] = $article;
            break;

        default:
            // Legacy support for existing usage
            $whereClause = '';
            $params = [];

            if ($category && $category !== 'all') {
                $whereClause .= 'WHERE category = ?';
                $params[] = $category;
            }

            if ($featured !== null) {
                $whereClause .= ($whereClause ? ' AND ' : 'WHERE ') . 'is_featured = ?';
                $params[] = $featured;
            }

            $sql = "SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count
            $countSql = str_replace('SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news', 'SELECT COUNT(*) as total FROM news', explode(' LIMIT', $sql)[0]);
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute(array_slice($params, 0, -2));
            $total = (int)$countStmt->fetch()['total'];

            // Legacy response format
            $response = [
                'success' => true,
                'news' => $articles,
                'data' => $articles, // Also include in new format
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ];
            echo json_encode($response, JSON_PRETTY_PRINT);
            exit;
    }

    // Add metadata to response
    if (isset($response['total'])) {
        $response['meta'] = [
            'timestamp' => date('c'),
            'count' => count($response['data']),
            'total' => $response['total'],
            'limit' => $limit,
            'offset' => $offset,
            'page' => $page,
            'pages' => isset($response['total']) ? ceil($response['total'] / $limit) : 1
        ];
    } else {
        $response['meta'] = [
            'timestamp' => date('c'),
            'count' => is_array($response['data']) ? count($response['data']) : 1
        ];
    }

} catch (Exception $e) {
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('c')
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>