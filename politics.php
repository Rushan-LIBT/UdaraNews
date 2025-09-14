<?php
// Prevent any output before headers
ob_start();

require_once 'config.php';

// Initialize variables with default values
$politicsNewsData = [];
$featuredPoliticsData = [];

// Load politics news data on server side
try {
    $pdo = getDBConnection();

    // Check if database and table exist, if not, initialize
    $stmt = $pdo->query("SHOW TABLES LIKE 'news'");
    if ($stmt->rowCount() == 0) {
        // Table doesn't exist, initialize database
        initializeDatabase();
    }

    // Get politics news
    $stmt = $pdo->prepare('SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news WHERE category = ? ORDER BY created_at DESC LIMIT 20');
    $stmt->execute(['politics']);
    $politicsNewsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get featured politics news
    $stmt = $pdo->prepare('SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news WHERE category = ? AND is_featured = 1 ORDER BY created_at DESC LIMIT 6');
    $stmt->execute(['politics']);
    $featuredPoliticsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Fallback to sample data if database fails
    $politicsNewsData = [
        [
            'id' => 1,
            'title' => 'Political News Coming Soon',
            'summary' => 'Stay tuned for the latest political updates and analysis.',
            'content' => 'We are working to bring you the most comprehensive political coverage.',
            'image' => 'create_placeholder.php?w=800&h=400&bg=e74c3c&text=Politics',
            'category' => 'politics',
            'author' => 'Politics Team',
            'created_at' => date('Y-m-d H:i:s'),
            'is_featured' => 1
        ]
    ];
    $featuredPoliticsData = $politicsNewsData;
    error_log("Error loading politics data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politics - Udara NEWS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="js/api-client.js"></script>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-brand">
                    <h1>Udara <span class="brand-highlight">NEWS</span></h1>
                </div>
                <div class="nav-menu" id="nav-menu">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="politics.php" class="nav-link active">Politics</a>
                    <a href="sports.php" class="nav-link">Sports</a>
                    <a href="#technology" class="nav-link">Technology</a>
                    <a href="#business" class="nav-link">Business</a>
                    <a href="about.html" class="nav-link">About</a>
                    <a href="contact.html" class="nav-link">Contact</a>
                </div>
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero-section" id="politics">
            <div class="hero-slider">
                <div class="slide active" data-bg-image="create_placeholder.php?w=1920&h=600&bg=e74c3c&text=Politics News">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">Politics</span>
                        <h1 class="slide-title">Congressional Budget Debate Intensifies</h1>
                        <p class="slide-excerpt">Lawmakers clash over federal spending priorities as deadline approaches for budget approval and government funding decisions.</p>
                        <div class="slide-meta">
                            <span class="slide-time">2 hours ago</span>
                            <span class="slide-author">By Thomas Wilson</span>
                        </div>
                    </div>
                </div>
                <div class="slide" data-bg-image="create_placeholder.php?w=1920&h=600&bg=2980b9&text=Election News">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">Politics</span>
                        <h1 class="slide-title">Governor Signs Education Reform Bill</h1>
                        <p class="slide-excerpt">Sweeping changes to state education system promised to improve student outcomes and provide better teacher support nationwide.</p>
                        <div class="slide-meta">
                            <span class="slide-time">4 hours ago</span>
                            <span class="slide-author">By Jennifer Martinez</span>
                        </div>
                    </div>
                </div>
                <div class="slide" data-bg-image="create_placeholder.php?w=1920&h=600&bg=16a085&text=Government News">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">Politics</span>
                        <h1 class="slide-title">Immigration Policy Updates Announced</h1>
                        <p class="slide-excerpt">Federal administration unveils new measures aimed at streamlining legal immigration processes and border security protocols.</p>
                        <div class="slide-meta">
                            <span class="slide-time">6 hours ago</span>
                            <span class="slide-author">By David Chen</span>
                        </div>
                    </div>
                </div>
                <div class="slide" data-bg-image="create_placeholder.php?w=1920&h=600&bg=8e44ad&text=Policy News">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">Politics</span>
                        <h1 class="slide-title">Voting Rights Act Amendments Proposed</h1>
                        <p class="slide-excerpt">Bipartisan group of senators introduces legislation to strengthen voting access and security across all states.</p>
                        <div class="slide-meta">
                            <span class="slide-time">8 hours ago</span>
                            <span class="slide-author">By Angela Rodriguez</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="slider-controls">
                <button class="slider-btn prev-btn">‹</button>
                <div class="slider-dots">
                    <span class="dot active" data-slide="0"></span>
                    <span class="dot" data-slide="1"></span>
                    <span class="dot" data-slide="2"></span>
                    <span class="dot" data-slide="3"></span>
                </div>
                <button class="slider-btn next-btn">›</button>
            </div>

            <div class="hero-search">
                <input type="text" class="hero-search-input" placeholder="Search political news...">
                <button class="hero-search-btn">🔍</button>
            </div>
        </section>

        <section class="news-section">
            <div class="container">
                <h2 class="section-title">Political Coverage</h2>
                <p class="section-subtitle">Latest developments in government, policy, and political affairs</p>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-category="all">All Politics</button>
                    <button class="filter-btn" data-category="federal">Federal</button>
                    <button class="filter-btn" data-category="state">State</button>
                    <button class="filter-btn" data-category="local">Local</button>
                    <button class="filter-btn" data-category="international">International</button>
                    <button class="filter-btn" data-category="elections">Elections</button>
                </div>
                <div class="news-grid" id="news-container">
                    <!-- Political news articles will be loaded here -->
                </div>
                <div class="load-more-container">
                    <button id="load-more-btn" class="load-more-btn">Load More Political News</button>
                </div>
            </div>
        </section>

        <section class="featured-section">
            <div class="container">
                <h2 class="section-title">Featured Political Stories</h2>
                <p class="section-subtitle">In-depth analysis and breaking political developments</p>
                <div class="featured-grid" id="featured-container">
                    <!-- Featured political articles will be loaded here -->
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Udara <span class="brand-highlight">NEWS</span></h3>
                    <p>Delivering truth-driven journalism with integrity. Where stories matter and voices are heard.</p>
                    <div class="social-links">
                        <a href="#" class="social-link" title="Facebook">📘</a>
                        <a href="#" class="social-link" title="Twitter">🐦</a>
                        <a href="#" class="social-link" title="Instagram">📷</a>
                        <a href="#" class="social-link" title="LinkedIn">💼</a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="politics.php">Politics</a></li>
                        <li><a href="sports.php">Sports</a></li>
                        <li><a href="about.html">About</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Categories</h4>
                    <ul>
                        <li><a href="#politics">Politics</a></li>
                        <li><a href="#federal">Federal News</a></li>
                        <li><a href="#state">State Politics</a></li>
                        <li><a href="#elections">Elections</a></li>
                        <li><a href="#policy">Policy Analysis</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>About Politics</h4>
                    <p>Comprehensive coverage of political developments, government affairs, and policy analysis from local to international levels.</p>
                    <div class="newsletter">
                        <input type="email" placeholder="Enter your email" class="newsletter-input">
                        <button class="newsletter-btn">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; 2024 Udara NEWS. All rights reserved.</p>
                    <div class="footer-links">
                        <a href="#privacy">Privacy Policy</a>
                        <a href="#terms">Terms of Service</a>
                        <a href="contact.html">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Politics news data loaded from server-side PHP
        const serverPoliticsData = <?php echo json_encode($politicsNewsData); ?>;
        const serverFeaturedData = <?php echo json_encode($featuredPoliticsData); ?>;

        // Politics-specific data for the news grid
        let politicsNews = [
            {
                id: 1,
                title: "Senate Banking Committee Reviews Financial Regulations",
                summary: "Committee members examine proposed changes to banking oversight and consumer protection measures in heated session.",
                content: "The Senate Banking Committee held a marathon session today...",
                image: "create_placeholder.php?w=800&h=400&bg=e74c3c&text=Politics",
                category: "federal",
                author: "Maria Gonzalez",
                created_at: "2024-01-16T10:00:00Z",
                is_featured: true
            },
            {
                id: 2,
                title: "State Legislature Debates Healthcare Expansion",
                summary: "Lawmakers consider proposal to expand state healthcare coverage to additional low-income residents.",
                content: "State lawmakers are debating a comprehensive healthcare expansion bill...",
                image: "create_placeholder.php?w=800&h=400&bg=2980b9&text=Healthcare",
                category: "state",
                author: "Robert Taylor",
                created_at: "2024-01-16T08:00:00Z",
                is_featured: false
            },
            {
                id: 3,
                title: "Supreme Court to Hear Constitutional Challenge",
                summary: "High court agrees to review case that could reshape federal and state government authority boundaries.",
                content: "The Supreme Court announced today that it will hear a significant case...",
                image: "create_placeholder.php?w=800&h=400&bg=16a085&text=Supreme Court",
                category: "federal",
                author: "Dr. Rachel Adams",
                created_at: "2024-01-15T14:00:00Z",
                is_featured: true
            },
            {
                id: 4,
                title: "International Trade Relations Strengthen",
                summary: "New bilateral agreements promise enhanced economic cooperation and reduced trade barriers between allied nations.",
                content: "Diplomatic negotiations concluded successfully today...",
                image: "create_placeholder.php?w=800&h=400&bg=d35400&text=International",
                category: "international",
                author: "John Anderson",
                created_at: "2024-01-15T12:00:00Z",
                is_featured: false
            },
            {
                id: 5,
                title: "Local Election Campaign Season Begins",
                summary: "Candidates file for municipal offices as communities prepare for local election campaigns.",
                content: "Filing deadlines have passed and the field is set for local elections...",
                image: "create_placeholder.php?w=800&h=400&bg=8e44ad&text=Elections",
                category: "local",
                author: "Lisa Anderson",
                created_at: "2024-01-15T09:00:00Z",
                is_featured: false
            },
            {
                id: 6,
                title: "Infrastructure Investment Bill Advances",
                summary: "Bipartisan legislation for roads, bridges, and broadband receives committee approval in procedural vote.",
                content: "The Infrastructure Investment Bill moved forward today...",
                image: "create_placeholder.php?w=800&h=400&bg=c0392b&text=Infrastructure",
                category: "federal",
                author: "Michael O'Connor",
                created_at: "2024-01-14T16:00:00Z",
                is_featured: true
            }
        ];

        // Initialize politics page with API-first approach
        document.addEventListener('DOMContentLoaded', async function() {
            console.log('Loading politics page with API-first approach...');
            console.log('Server-side fallback data available:', {
                serverPoliticsData: serverPoliticsData.length,
                serverFeaturedData: serverFeaturedData.length
            });

            // Load politics news using API
            await loadPoliticsNews();

            // Load featured political stories
            await loadFeaturedPolitics();

            // Setup filter buttons
            setupPoliticsFilters();

            // Initialize hero slider
            initializeHeroSlider();

            // Setup mobile menu
            setupMobileMenu();

            // Setup search functionality
            setupPoliticsSearch();

            console.log('Politics page loaded successfully with API integration!');
        });

        async function loadPoliticsNews() {
            const container = document.getElementById('news-container');

            try {
                NewsUtils.showLoading(container, 'Loading politics news...');

                // Try API first
                const response = await newsAPI.getNewsByCategory('politics', { limit: 20 });

                if (response.data && response.data.length > 0) {
                    console.log(`Loaded ${response.data.length} politics articles from API`);
                    politicsNews = response.data;
                    displayPoliticsNews(politicsNews);
                } else {
                    throw new Error('No politics articles returned from API');
                }

            } catch (error) {
                console.warn('Politics API failed, falling back to server-side data:', error);

                // Fallback to server-side data
                if (serverPoliticsData && serverPoliticsData.length > 0) {
                    console.log(`Using ${serverPoliticsData.length} politics articles from server-side data`);
                    politicsNews = serverPoliticsData;
                    displayPoliticsNews(politicsNews);
                } else {
                    NewsUtils.showEmpty(container, 'No politics articles found. Database may be empty or not connected.');
                }
            }
        }

        function displayPoliticsNews(newsData) {
            const container = document.getElementById('news-container');
            container.innerHTML = '';

            newsData.forEach(article => {
                const newsCard = createNewsCard(article);
                container.appendChild(newsCard);
            });

            console.log(`Displayed ${newsData.length} political news articles`);
        }

        function loadFeaturedPolitics() {
            console.log('Loading featured politics news from server-side data...');

            if (serverFeaturedData && serverFeaturedData.length > 0) {
                console.log(`Loaded ${serverFeaturedData.length} featured politics articles from database`);
                displayFeaturedPolitics(serverFeaturedData);
            } else {
                console.log('No featured politics articles found in database, using sample data');
                const featuredSample = politicsNews.filter(article => article.is_featured);
                displayFeaturedPolitics(featuredSample);
            }
        }

        function displayFeaturedPolitics(featuredArticles) {
            const container = document.getElementById('featured-container');
            container.innerHTML = '';

            featuredArticles.forEach(article => {
                const featuredCard = createFeaturedCard(article);
                container.appendChild(featuredCard);
            });

            console.log(`Displayed ${featuredArticles.length} featured political stories`);
        }

        function setupPoliticsFilters() {
            const filterButtons = document.querySelectorAll('.filter-btn');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');

                    const category = this.getAttribute('data-category');
                    filterPoliticsNews(category);
                });
            });
        }

        function filterPoliticsNews(category) {
            const container = document.getElementById('news-container');
            container.innerHTML = '';

            const filteredNews = category === 'all'
                ? politicsNews
                : politicsNews.filter(article => article.category === category);

            filteredNews.forEach(article => {
                const newsCard = createNewsCard(article);
                container.appendChild(newsCard);
            });

            console.log(`Filtered to ${filteredNews.length} articles for category: ${category}`);
        }

        // Use the same news card creation functions from the home page
        function createNewsCard(article) {
            const cardCol = document.createElement('div');
            cardCol.className = 'news-card';

            const imageUrl = article.image || 'create_placeholder.php?w=400&h=250&bg=e74c3c&text=Politics';

            cardCol.innerHTML = `
                <article class="card">
                    <div class="card-image">
                        <img src="${imageUrl}" alt="${article.title}" onerror="this.src='create_placeholder.php?w=400&h=250&bg=6c757d&text=Image Error'"
                        <div class="card-category">${article.category}</div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">${article.title}</h3>
                        <p class="card-excerpt">${article.summary}</p>
                        <div class="card-meta">
                            <span class="card-author">By ${article.author}</span>
                            <span class="card-time">${formatDate(article.created_at)}</span>
                        </div>
                    </div>
                </article>
            `;

            cardCol.style.cursor = 'pointer';
            cardCol.addEventListener('click', () => {
                console.log('Opening article:', article.title);
                // You can add modal functionality here
            });

            return cardCol;
        }

        function createFeaturedCard(article) {
            const cardCol = document.createElement('div');
            cardCol.className = 'featured-card';

            const imageUrl = article.image || 'create_placeholder.php?w=500&h=300&bg=e74c3c&text=Featured Politics';

            cardCol.innerHTML = `
                <article class="featured-article">
                    <div class="featured-image">
                        <img src="${imageUrl}" alt="${article.title}" onerror="this.src='create_placeholder.php?w=500&h=300&bg=6c757d&text=Featured Error'"
                        <div class="featured-category">${article.category}</div>
                    </div>
                    <div class="featured-content">
                        <h3 class="featured-title">${article.title}</h3>
                        <p class="featured-excerpt">${article.summary}</p>
                        <div class="featured-meta">
                            <span class="featured-author">By ${article.author}</span>
                            <span class="featured-time">${formatDate(article.created_at)}</span>
                        </div>
                    </div>
                </article>
            `;

            cardCol.style.cursor = 'pointer';
            cardCol.addEventListener('click', () => {
                console.log('Opening featured article:', article.title);
            });

            return cardCol;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffHours = Math.ceil(diffTime / (1000 * 60 * 60));
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffHours < 24) return `${diffHours} hours ago`;
            if (diffDays === 1) return 'Yesterday';
            if (diffDays <= 7) return `${diffDays} days ago`;
            return date.toLocaleDateString();
        }

        function showArticleModal(article) {
            // Simple modal implementation
            const modal = document.createElement('div');
            modal.className = 'article-modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            `;

            modal.innerHTML = `
                <div class="modal-content" style="
                    background: white;
                    max-width: 800px;
                    max-height: 90vh;
                    overflow-y: auto;
                    border-radius: 8px;
                    position: relative;
                ">
                    <button class="modal-close" onclick="this.closest('.article-modal').remove()" style="
                        position: absolute;
                        top: 15px;
                        right: 20px;
                        background: none;
                        border: none;
                        font-size: 24px;
                        cursor: pointer;
                        z-index: 1;
                    ">×</button>
                    <img src="${article.image}" alt="${article.title}" style="width: 100%; height: 300px; object-fit: cover;">
                    <div style="padding: 20px;">
                        <div style="color: #e74c3c; font-weight: bold; margin-bottom: 10px;">${article.category.toUpperCase()}</div>
                        <h2 style="margin-bottom: 15px;">${article.title}</h2>
                        <div style="color: #666; margin-bottom: 20px; font-size: 14px;">
                            <span>By ${article.author}</span> • <span>${formatDate(article.created_at || article.date)}</span>
                        </div>
                        <div style="line-height: 1.6;">${article.content || article.summary}</div>
                    </div>
                </div>
            `;

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });

            document.body.appendChild(modal);
        }

        function initializeHeroSlider() {
            // Hero slider functionality
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');

            if (slides.length === 0) return;

            let currentSlide = 0;

            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === index);
                });
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }

            function prevSlide() {
                currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(currentSlide);
            }

            // Auto-advance slides
            setInterval(nextSlide, 5000);

            // Navigation controls
            if (nextBtn) nextBtn.addEventListener('click', nextSlide);
            if (prevBtn) prevBtn.addEventListener('click', prevSlide);

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    currentSlide = index;
                    showSlide(currentSlide);
                });
            });
        }

        // Mobile menu setup
        // Politics search functionality
        function setupPoliticsSearch() {
            const searchInput = document.querySelector('.hero-search-input');
            const searchBtn = document.querySelector('.hero-search-btn');

            if (searchInput && searchBtn) {
                async function performSearch() {
                    const searchTerm = searchInput.value.trim();
                    if (!searchTerm) {
                        await loadPoliticsNews(); // Reload politics news
                        return;
                    }

                    const container = document.getElementById('news-container');

                    try {
                        NewsUtils.showLoading(container, `Searching politics for "${searchTerm}"...`);

                        const response = await newsAPI.searchNews(searchTerm, { category: 'politics', limit: 20 });

                        if (response.data && response.data.length > 0) {
                            console.log(`Found ${response.data.length} politics search results for "${searchTerm}"`);
                            displayPoliticsNews(response.data);
                        } else {
                            NewsUtils.showEmpty(container, `No politics results found for "${searchTerm}".`);
                        }

                    } catch (error) {
                        console.error('Politics search failed:', error);
                        NewsUtils.showError(container, `Search failed: ${error.message}`);
                    }
                }

                searchBtn.addEventListener('click', performSearch);
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });
            }
        }

        function setupMobileMenu() {
            const hamburger = document.getElementById('hamburger');
            const navMenu = document.getElementById('nav-menu');

            if (hamburger && navMenu) {
                hamburger.addEventListener('click', () => {
                    navMenu.classList.toggle('active');
                    hamburger.classList.toggle('active');
                });
            }
        }

    </script>
</body>
</html>