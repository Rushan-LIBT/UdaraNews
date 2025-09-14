# Udara News API Documentation

## Base URL
```
/api/news.php
```

## Available Endpoints

### 1. Get All News
**GET** `/api/news.php?action=get_all`

Parameters:
- `limit` (optional, default: 20) - Number of articles to return
- `offset` (optional, default: 0) - Number of articles to skip
- `page` (optional, default: 1) - Page number (automatically calculates offset)

**Example:**
```
GET /api/news.php?action=get_all&limit=10&page=1
```

### 2. Get News by Category
**GET** `/api/news.php?action=get_by_category`

Parameters:
- `category` (required) - Category name (politics, sports, technology, business, general)
- `limit` (optional, default: 20) - Number of articles to return
- `offset` (optional, default: 0) - Number of articles to skip

**Example:**
```
GET /api/news.php?action=get_by_category&category=politics&limit=10
```

### 3. Get Featured News
**GET** `/api/news.php?action=get_featured`

Parameters:
- `category` (optional) - Filter by category
- `limit` (optional, default: 20) - Number of articles to return
- `offset` (optional, default: 0) - Number of articles to skip

**Example:**
```
GET /api/news.php?action=get_featured&category=sports&limit=5
```

### 4. Search News
**GET** `/api/news.php?action=search`

Parameters:
- `search` (required) - Search query
- `category` (optional) - Filter by category
- `limit` (optional, default: 20) - Number of articles to return
- `offset` (optional, default: 0) - Number of articles to skip

**Example:**
```
GET /api/news.php?action=search&search=election&category=politics
```

### 5. Get Single Article
**GET** `/api/news.php?action=get_single`

Parameters:
- `id` (required) - Article ID

**Example:**
```
GET /api/news.php?action=get_single&id=123
```

### 6. Get Statistics
**GET** `/api/news.php?action=get_stats`

Returns overall statistics about articles.

**Example:**
```
GET /api/news.php?action=get_stats
```

### 7. Legacy Support
**GET** `/api/news.php` (without action parameter)

Parameters:
- `category` (optional, default: 'all') - Category filter
- `featured` (optional) - Filter by featured status (0 or 1)
- `limit` (optional, default: 10) - Number of articles to return
- `page` (optional, default: 1) - Page number

**Example:**
```
GET /api/news.php?category=sports&featured=1&limit=5
```

## Response Format

### Success Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Article Title",
            "summary": "Article summary",
            "content": "Full article content",
            "image": "https://example.com/image.jpg",
            "category": "politics",
            "author": "Author Name",
            "created_at": "2024-01-01T12:00:00Z",
            "is_featured": 1
        }
    ],
    "total": 50,
    "meta": {
        "timestamp": "2024-01-01T12:00:00+00:00",
        "count": 10,
        "total": 50,
        "limit": 10,
        "offset": 0,
        "page": 1,
        "pages": 5
    }
}
```

### Error Response
```json
{
    "success": false,
    "error": "Error message",
    "timestamp": "2024-01-01T12:00:00+00:00"
}
```

## Statistics Response
```json
{
    "success": true,
    "data": {
        "total_articles": 100,
        "by_category": [
            {
                "category": "politics",
                "count": 25
            },
            {
                "category": "sports",
                "count": 30
            }
        ],
        "featured_count": 15,
        "recent_count": 8
    }
}
```

## JavaScript API Client

The API includes a JavaScript client library (`/js/api-client.js`) that provides:

### Global Instance
```javascript
window.newsAPI = new UdaraNewsAPI();
```

### Available Methods
```javascript
// Get all news
const response = await newsAPI.getAllNews({ limit: 10 });

// Get news by category
const response = await newsAPI.getNewsByCategory('politics', { limit: 10 });

// Get featured news
const response = await newsAPI.getFeaturedNews('sports', { limit: 5 });

// Search news
const response = await newsAPI.searchNews('election', { category: 'politics' });

// Get single article
const response = await newsAPI.getSingleNews(123);

// Get statistics
const response = await newsAPI.getStats();

// Legacy support
const response = await newsAPI.getLegacyNews({ category: 'sports', featured: true });
```

### Utility Functions
```javascript
// Format date
const formatted = NewsUtils.formatDate('2024-01-01T12:00:00Z');

// Create news card
const cardElement = NewsUtils.createNewsCard(article, 'news');

// Show article modal
NewsUtils.showArticleModal(article);

// Show loading/error/empty states
NewsUtils.showLoading(container, 'Loading...');
NewsUtils.showError(container, 'Error message');
NewsUtils.showEmpty(container, 'No results found');
```

## CORS Support

The API includes CORS headers to allow cross-origin requests:
- `Access-Control-Allow-Origin: *`
- `Access-Control-Allow-Methods: GET, POST, OPTIONS`
- `Access-Control-Allow-Headers: Content-Type`

## Error Handling

The API returns appropriate HTTP status codes:
- `200 OK` - Successful request
- `500 Internal Server Error` - Server error

All responses include a `success` boolean field and error details when applicable.