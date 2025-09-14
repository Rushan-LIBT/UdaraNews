<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'udara_news');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Create database and tables if they don't exist
function initializeDatabase() {
    try {
        // Connect to MySQL without specifying database
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
        
        // Create news table
        $newsTableSQL = "
            CREATE TABLE IF NOT EXISTS news (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                summary TEXT NOT NULL,
                content TEXT,
                image VARCHAR(500),
                category ENUM('politics', 'sports', 'technology', 'business', 'general') DEFAULT 'general',
                author VARCHAR(100) NOT NULL,
                is_featured BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_category (category),
                INDEX idx_featured (is_featured),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($newsTableSQL);
        
        // Insert sample data if table is empty
        $count = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
        if ($count == 0) {
            insertSampleData($pdo);
        }
        
    } catch (PDOException $e) {
        die("Database initialization failed: " . $e->getMessage());
    }
}

// Insert sample news data
function insertSampleData($pdo) {
    $sampleNews = [
        [
            'title' => 'Breaking: Major Political Development in National Elections',
            'summary' => 'Significant changes announced in the upcoming national elections process, affecting millions of voters across the country.',
            'content' => 'In a groundbreaking announcement today, election officials revealed major reforms to the national voting system. These changes, designed to increase transparency and accessibility, will be implemented before the next major election cycle. The reforms include extended voting hours, improved security measures, and enhanced accessibility features for disabled voters. Political analysts suggest these changes could significantly impact voter turnout and election outcomes.',
            'image' => 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=400&q=80',
            'category' => 'politics',
            'author' => 'Sarah Johnson',
            'is_featured' => 1
        ],
        [
            'title' => 'Tech Giants Announce Revolutionary AI Partnership',
            'summary' => 'Leading technology companies join forces to develop next-generation artificial intelligence solutions for global challenges.',
            'content' => 'Major technology corporations have announced a unprecedented partnership to accelerate artificial intelligence research and development. This collaboration aims to address climate change, healthcare challenges, and educational disparities through innovative AI solutions. The partnership includes shared research facilities, joint funding initiatives, and open-source technology sharing. Industry experts predict this could mark a new era in AI development and application.',
            'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=400&q=80',
            'category' => 'technology',
            'author' => 'Michael Chen',
            'is_featured' => 1
        ],
        [
            'title' => 'Championship Finals Set Record-Breaking Attendance',
            'summary' => 'Sports fans flock to witness historic championship match, setting new attendance records and television viewership.',
            'content' => 'The highly anticipated championship finals drew unprecedented crowds both in-person and through broadcast viewership. Stadium officials confirmed that ticket demand exceeded capacity by 400%, while television and streaming numbers broke all previous records. The event showcased outstanding athletic performances and demonstrated the unifying power of sports. Local businesses reported significant economic benefits from the influx of visitors.',
            'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=400&q=80',
            'category' => 'sports',
            'author' => 'David Rodriguez',
            'is_featured' => 0
        ],
        [
            'title' => 'Global Markets Show Strong Recovery Trends',
            'summary' => 'International financial markets demonstrate resilience with sustained growth across multiple sectors and regions.',
            'content' => 'Financial analysts report encouraging signs of economic recovery as global markets continue their upward trajectory. Key indicators show improved consumer confidence, increased business investments, and stabilizing inflation rates. The recovery spans multiple sectors including technology, healthcare, and renewable energy. Economists attribute this positive trend to coordinated policy responses and innovative business adaptation strategies.',
            'image' => 'https://images.unsplash.com/photo-1444653614773-995cb1ef9efa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=400&q=80',
            'category' => 'business',
            'author' => 'Emma Thompson',
            'is_featured' => 1
        ],
        [
            'title' => 'Revolutionary Green Energy Project Launches',
            'summary' => 'Innovative renewable energy initiative promises to transform power generation and reduce carbon emissions significantly.',
            'content' => 'A groundbreaking renewable energy project has officially launched, featuring cutting-edge solar and wind technologies. The project aims to generate clean electricity for over 2 million households while creating thousands of jobs in the green energy sector. Environmental scientists praise the initiative as a crucial step toward achieving carbon neutrality goals. The project includes advanced energy storage systems and smart grid integration.',
            'image' => 'https://images.unsplash.com/photo-1569163139394-de44eed40de5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=400&q=80',
            'category' => 'general',
            'author' => 'Dr. Lisa Park',
            'is_featured' => 0
        ],
        [
            'title' => 'International Space Mission Achieves Historic Milestone',
            'summary' => 'Space exploration reaches new heights with successful completion of ambitious international collaborative mission.',
            'content' => 'The international space community celebrates a historic achievement as the collaborative mission successfully completes its primary objectives. This mission, involving multiple countries and space agencies, has advanced our understanding of deep space exploration and planetary science. The mission returned valuable scientific data and demonstrated the effectiveness of international cooperation in space exploration. Future missions are already being planned based on these successful outcomes.',
            'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=400&q=80',
            'category' => 'technology',
            'author' => 'Dr. James Wilson',
            'is_featured' => 0
        ],
        [
            'title' => 'Healthcare Innovation Promises Better Patient Outcomes',
            'summary' => 'Medical researchers unveil breakthrough treatment methods that could revolutionize patient care and recovery times.',
            'content' => 'Medical professionals announce significant advances in treatment methodologies that promise to improve patient outcomes across various health conditions. The innovative approaches combine traditional medicine with cutting-edge technology, resulting in faster recovery times and reduced side effects. Clinical trials have shown remarkable success rates, leading to expedited approval processes. Healthcare systems worldwide are preparing to implement these breakthrough treatments.',
            'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=400&q=80',
            'category' => 'general',
            'author' => 'Dr. Rachel Adams',
            'is_featured' => 0
        ],
        [
            'title' => 'Olympic Preparations Showcase Global Unity',
            'summary' => 'Athletes from around the world demonstrate exceptional preparation and sportsmanship ahead of major international competition.',
            'content' => 'The upcoming Olympic Games showcase remarkable examples of international cooperation and athletic excellence. Athletes from diverse backgrounds have been training together, sharing techniques, and building friendships that transcend national boundaries. The preparation phase has highlighted the universal values of sport: dedication, respect, and friendship. Olympic officials report unprecedented enthusiasm and participation levels from both athletes and host communities.',
            'image' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=400&q=80',
            'category' => 'sports',
            'author' => 'Maria Gonzalez',
            'is_featured' => 0
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO news (title, summary, content, image, category, author, is_featured) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($sampleNews as $news) {
        $stmt->execute([
            $news['title'],
            $news['summary'],
            $news['content'],
            $news['image'],
            $news['category'],
            $news['author'],
            $news['is_featured']
        ]);
    }
}

// Initialize database only when explicitly called
// Comment out automatic initialization to prevent infinite loading
// initializeDatabase();
?>