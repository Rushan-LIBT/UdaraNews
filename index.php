<?php
// Prevent any output before headers
ob_start();

require_once 'config.php';

// Initialize variables with default values
$allNewsData = [];
$featuredNewsData = [];

// Load news data on server side
try {
    $pdo = getDBConnection();

    // Check if database and table exist, if not, initialize
    $stmt = $pdo->query("SHOW TABLES LIKE 'news'");
    if ($stmt->rowCount() == 0) {
        // Table doesn't exist, initialize database
        initializeDatabase();
    }

    // Get latest news (all categories)
    $stmt = $pdo->prepare('SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news ORDER BY created_at DESC LIMIT 20');
    $stmt->execute();
    $allNewsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get featured news
    $stmt = $pdo->prepare('SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 6');
    $stmt->execute();
    $featuredNewsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Fallback to sample data if database fails
    $allNewsData = [
        [
            'id' => 1,
            'title' => 'Welcome to Udara News',
            'summary' => 'Your trusted source for breaking news and updates.',
            'content' => 'Welcome to Udara News - your comprehensive source for the latest news and updates from around the world.',
            'image' => 'create_placeholder.php?w=800&h=400&bg=3498db&text=Welcome',
            'category' => 'general',
            'author' => 'Udara Team',
            'created_at' => date('Y-m-d H:i:s'),
            'is_featured' => 1
        ]
    ];
    $featuredNewsData = $allNewsData;
    error_log("Error loading news data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Udara NEWS - Latest News & Updates</title>
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
                    <a href="#home" class="nav-link">Home</a>
                    <a href="politics.php" class="nav-link">Politics</a>
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
        <section class="hero-section" id="home">
            <div class="hero-slider">
                <div class="slide active" data-bg-image="https://images.unsplash.com/photo-1569163139394-de44eed40de5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">Breaking News</span>
                        <h1 class="slide-title">Global Climate Summit Reaches Historic Agreement</h1>
                        <p class="slide-excerpt">World leaders unite on unprecedented climate action plan with binding commitments for carbon neutrality by 2050.</p>
                        <div class="slide-meta">
                            <span class="slide-time">2 hours ago</span>
                            <span class="slide-author">By Sarah Johnson</span>
                        </div>
                    </div>
                </div>
                <div class="slide" data-bg-image="https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">Technology</span>
                        <h1 class="slide-title">Revolutionary AI Breakthrough Changes Medical Diagnosis</h1>
                        <p class="slide-excerpt">New artificial intelligence system demonstrates 99% accuracy in early cancer detection, potentially saving millions of lives.</p>
                        <div class="slide-meta">
                            <span class="slide-time">4 hours ago</span>
                            <span class="slide-author">By Michael Chen</span>
                        </div>
                    </div>
                </div>
                <div class="slide" data-bg-image="https://images.unsplash.com/photo-1551698618-1dfe5d97d256?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">Sports</span>
                        <h1 class="slide-title">Underdog Team Wins Championship in Stunning Upset</h1>
                        <p class="slide-excerpt">Against all odds, the rookie-filled team defeats three-time champions in a thrilling finale that captivated millions worldwide.</p>
                        <div class="slide-meta">
                            <span class="slide-time">6 hours ago</span>
                            <span class="slide-author">By David Martinez</span>
                        </div>
                    </div>
                </div>
                <div class="slide" data-bg-image="https://images.unsplash.com/photo-1444653614773-995cb1ef9efa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">Business</span>
                        <h1 class="slide-title">Tech Giant Announces Major Green Energy Investment</h1>
                        <p class="slide-excerpt">$50 billion commitment to renewable energy infrastructure marks the largest corporate sustainability investment in history.</p>
                        <div class="slide-meta">
                            <span class="slide-time">8 hours ago</span>
                            <span class="slide-author">By Lisa Thompson</span>
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
                <input type="text" class="hero-search-input" placeholder="What's happening today?">
                <button class="hero-search-btn">🔍</button>
            </div>
        </section>

        <section class="news-section">
            <div class="container">
                <h2 class="section-title">Trending Now</h2>
                <p class="section-subtitle">The stories shaping our world today</p>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-category="all">All Stories</button>
                    <button class="filter-btn" data-category="politics">Politics</button>
                    <button class="filter-btn" data-category="sports">Sports</button>
                    <button class="filter-btn" data-category="technology">Tech</button>
                    <button class="filter-btn" data-category="business">Business</button>
                    <button class="filter-btn" data-category="health">Health</button>
                </div>
                <div class="news-grid" id="news-container">
                    <!-- News articles will be loaded here via PHP/JavaScript -->
                </div>
                <div class="load-more-container">
                    <button id="load-more-btn" class="load-more-btn">Discover More Stories</button>
                </div>
            </div>
        </section>

        <section class="featured-section">
            <div class="container">
                <h2 class="section-title">Editor's Picks</h2>
                <p class="section-subtitle">Handpicked stories you shouldn't miss</p>
                <div class="featured-grid" id="featured-container">
                    <!-- Featured articles will be loaded here -->
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
                    <h4>Categories</h4>
                    <ul>
                        <li><a href="politics.php">Politics</a></li>
                        <li><a href="sports.php">Sports</a></li>
                        <li><a href="#technology">Technology</a></li>
                        <li><a href="#business">Business</a></li>
                        <li><a href="#health">Health</a></li>
                        <li><a href="#environment">Environment</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="contact.html">Contact</a></li>
                        <li><a href="#privacy">Privacy Policy</a></li>
                        <li><a href="#terms">Terms of Service</a></li>
                        <li><a href="#newsletter">Newsletter</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Connect With Us</h4>
                    <p>📧 newsroom@udaranews.com</p>
                    <p>📞 +1 (555) NEWS-NOW</p>
                    <p>📍 Digital Newsroom, Global Coverage</p>
                    <p style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8;">24/7 Breaking News Coverage</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Udara NEWS. All rights reserved. | Trusted journalism since day one.</p>
            </div>
        </div>
    </footer>

    <script>
        // News data loaded from server-side PHP
        const allNewsData = <?php echo json_encode($allNewsData); ?>;
        const featuredNewsData = <?php echo json_encode($featuredNewsData); ?>;

        let currentCategory = 'all';
        let displayedArticles = [];

        // Track initialization state to prevent multiple loads
        let pageInitialized = false;
        let contentLoaded = false;

        // Prevent any accidental re-initialization
        if (window.__udaraPageInitializing) return;
        window.__udaraPageInitializing = true;

        // Initialize page
        document.addEventListener('DOMContentLoaded', async function() {
            if (pageInitialized) {
                console.warn('Page already initialized, skipping');
                return;
            }
            pageInitialized = true;

            console.log('Initializing home page with fallback-first approach...');
            console.log('Server-side fallback data available:', {
                allNewsData: allNewsData.length,
                featuredNewsData: featuredNewsData.length
            });

            // First, immediately show server-side data to prevent blank page
            showInitialContent();

            // Initialize hero slider
            initializeHeroSlider();

            // Then try to enhance with API data (non-blocking)
            setTimeout(() => {
                if (!contentLoaded) {
                    loadNewsDataWithFallback();
                    loadFeaturedNewsWithFallback();
                }
            }, 100);

            // Setup interactions
            setupFilters();
            setupMobileMenu();
            setupSearch();

            console.log('Home page initialized successfully!');
            window.__udaraPageInitialized = true;
        });

        // Show initial server-side content immediately to prevent blank page
        function showInitialContent() {
            if (window.__initialContentShown) {
                // Prevent fallback from being shown more than once
                return;
            }
            window.__initialContentShown = true;
            console.log('Showing initial server-side content...');

            // Display server-side news data immediately
            if (allNewsData && allNewsData.length > 0) {
                displayedArticles = [...allNewsData];
                displayNews(displayedArticles);
                console.log(`Displayed ${allNewsData.length} initial news articles from server`);
            }

            // Display server-side featured data immediately
            if (featuredNewsData && featuredNewsData.length > 0) {
                displayFeaturedNews(featuredNewsData);
                console.log(`Displayed ${featuredNewsData.length} initial featured articles from server`);
            } else {
                console.warn('No server-side featured data available');
            }

            contentLoaded = true;
        }

        // Enhanced loading functions that don't cause blinking
        async function loadNewsDataWithFallback() {
            // Only attempt API enhancement once
            if (contentLoaded) {
                console.log('News data already loaded, skipping API call');
                return;
            }

            let apiAttempted = false;
            try {
                apiAttempted = true;
                console.log('Attempting to enhance news data from API...');
                const response = await newsAPI.getAllNews({ limit: 20 });

                if (response.data && response.data.length > 0) {
                    console.log(`Enhanced with ${response.data.length} news articles from API`);
                    displayedArticles = response.data;
                    displayNews(displayedArticles);
                    contentLoaded = true; // Mark as loaded to prevent further attempts
                } else {
                    console.log('API returned no data, keeping server-side content');
                    contentLoaded = true; // Still mark as loaded to prevent retries
                }

            } catch (error) {
                if (apiAttempted) {
                    // Only log once, do not retry
                    console.warn('API enhancement failed, keeping server-side data:', error);
                    contentLoaded = true; // Prevent further attempts
                }
            }
        }

        async function loadFeaturedNewsWithFallback() {
            // Only attempt API enhancement once
            if (window.__featuredLoaded) {
                console.log('Featured news already loaded, skipping API call');
                return;
            }
            let apiAttempted = false;
            try {
                apiAttempted = true;
                console.log('Attempting to enhance featured news from API...');
                const response = await newsAPI.getFeaturedNews('', { limit: 6 });

                if (response.data && response.data.length > 0) {
                    console.log(`Enhanced with ${response.data.length} featured articles from API`);
                    displayFeaturedNews(response.data);
                    window.__featuredLoaded = true;
                } else {
                    console.log('Featured API returned no data, keeping server-side content');
                    window.__featuredLoaded = true; // Still mark as loaded to prevent retries
                }

            } catch (error) {
                if (apiAttempted) {
                    // Only log once, do not retry
                    console.warn('Featured API enhancement failed, keeping server-side data:', error);
                    window.__featuredLoaded = true;
                }
            }
        }

        // Legacy functions kept for compatibility but made safer
        async function loadNewsData() {
            await loadNewsDataWithFallback();
        }

        async function loadFeaturedNews() {
            await loadFeaturedNewsWithFallback();
        }

        function displayFeaturedNews(articles) {
            const container = document.getElementById('featured-container');
            if (container) {
                container.innerHTML = '';
                articles.forEach(article => {
                    const featuredCard = createFeaturedCard(article);
                    container.appendChild(featuredCard);
                });
            }
        }

        function displayNews(articles) {
            const container = document.getElementById('news-container');
            if (!container) return;

            container.innerHTML = '';

            articles.forEach(article => {
                const newsCard = createNewsCard(article);
                container.appendChild(newsCard);
            });

            console.log(`Displayed ${articles.length} news articles`);
        }

        function setupFilters() {
            const filterButtons = document.querySelectorAll('.filter-btn');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');

                    const category = this.getAttribute('data-category');
                    filterNews(category);
                });
            });
        }

        async function filterNews(category) {
            currentCategory = category;
            console.log(`Filtering news by category: ${category}`);

            // Always use client-side filtering first for instant results (no blinking)
            let filteredArticles;
            if (category === 'all') {
                filteredArticles = [...allNewsData];
            } else {
                filteredArticles = allNewsData.filter(article =>
                    article.category.toLowerCase() === category.toLowerCase()
                );
            }

            // Display filtered results immediately
            if (filteredArticles.length > 0) {
                displayNews(filteredArticles);
                console.log(`Instantly filtered to ${filteredArticles.length} articles (client-side)`);
            } else {
                const container = document.getElementById('news-container');
                NewsUtils.showEmpty(container, `No ${category === 'all' ? '' : category} articles found.`);
            }

            // Optionally try to enhance with API data in background (non-blocking)
            try {
                let response;
                if (category === 'all') {
                    response = await newsAPI.getAllNews({ limit: 20 });
                } else {
                    response = await newsAPI.getNewsByCategory(category, { limit: 20 });
                }

                if (response.data && response.data.length > 0 && response.data.length !== filteredArticles.length) {
                    console.log(`Enhanced filtering with ${response.data.length} ${category} articles from API`);
                    displayNews(response.data);
                }

            } catch (error) {
                console.warn('Filter API enhancement failed, keeping client-side results:', error);
                // Don't change anything - keep the client-side filtered results
            }
        }

        function createNewsCard(article) {
            const cardCol = document.createElement('div');
            cardCol.className = 'news-card';

            const imageUrl = article.image && article.image.trim() !== ''
                ? article.image
                : 'create_placeholder.php?w=400&h=250&bg=3498db&text=News';

            cardCol.innerHTML = `
                <article class="card">
                    <div class="card-image">
                        <img src="${imageUrl}" alt="${article.title}" onerror="this.src='create_placeholder.php?w=400&h=250&bg=6c757d&text=Image Error'">
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
                showArticleModal(article);
            });

            return cardCol;
        }

        function createFeaturedCard(article) {
            const cardCol = document.createElement('div');
            cardCol.className = 'featured-card';

            const imageUrl = article.image && article.image.trim() !== ''
                ? article.image
                : 'create_placeholder.php?w=500&h=300&bg=e74c3c&text=Featured';

            cardCol.innerHTML = `
                <article class="featured-article">
                    <div class="featured-image">
                        <img src="${imageUrl}" alt="${article.title}" onerror="this.src='create_placeholder.php?w=500&h=300&bg=6c757d&text=Featured Error'">
                        <div class="featured-category">${article.category}</div>
                    </div>
                    <div class="featured-content">
                        <h3 class="featured-title">${article.title}</h3>
                        <p class="featured-excerpt">${article.summary.substring(0, 120)}...</p>
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
                showArticleModal(article);
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
                            <span>By ${article.author}</span> • <span>${formatDate(article.created_at)}</span>
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

        // Track slider state to prevent multiple initializations
        let sliderInitialized = false;
        let sliderInterval = null;

        function initializeHeroSlider() {
            // Prevent multiple initializations and interval stacking
            if (sliderInitialized) {
                console.warn('Hero slider already initialized, skipping');
                return;
            }
            sliderInitialized = true;

            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');

            if (slides.length === 0) {
                console.warn('No slides found for hero slider');
                return;
            }

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

            // Ensure only one interval is running
            if (sliderInterval) {
                clearInterval(sliderInterval);
                sliderInterval = null;
            }

            // Auto-advance slides only if more than one slide
            if (slides.length > 1) {
                sliderInterval = setInterval(nextSlide, 5000);
                console.log('Hero slider auto-advance enabled');
            }

            // Navigation controls
            if (nextBtn) nextBtn.addEventListener('click', () => {
                nextSlide();
                // Reset interval on manual navigation for smoothness
                if (sliderInterval) {
                    clearInterval(sliderInterval);
                    sliderInterval = setInterval(nextSlide, 5000);
                }
            });
            if (prevBtn) prevBtn.addEventListener('click', () => {
                prevSlide();
                if (sliderInterval) {
                    clearInterval(sliderInterval);
                    sliderInterval = setInterval(nextSlide, 5000);
                }
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    currentSlide = index;
                    showSlide(currentSlide);
                    if (sliderInterval) {
                        clearInterval(sliderInterval);
                        sliderInterval = setInterval(nextSlide, 5000);
                    }
                });
            });

            // Show the first slide initially
            showSlide(currentSlide);
            console.log(`Hero slider initialized with ${slides.length} slides`);
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

        // Search functionality
        function setupSearch() {
            const searchInput = document.querySelector('.hero-search-input');
            const searchBtn = document.querySelector('.hero-search-btn');

            if (searchInput && searchBtn) {
                async function performSearch() {
                    const searchTerm = searchInput.value.trim();
                    if (!searchTerm) {
                        await loadNewsData(); // Reload all news
                        return;
                    }

                    const container = document.getElementById('news-container');

                    try {
                        NewsUtils.showLoading(container, `Searching for "${searchTerm}"...`);

                        const response = await newsAPI.searchNews(searchTerm, { limit: 20 });

                        if (response.data && response.data.length > 0) {
                            console.log(`Found ${response.data.length} search results for "${searchTerm}"`);
                            displayNews(response.data);
                        } else {
                            NewsUtils.showEmpty(container, `No results found for "${searchTerm}".`);
                        }

                    } catch (error) {
                        console.error('Search failed:', error);
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

        // Load more functionality
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                // For now, just hide the button since all articles are loaded
                loadMoreBtn.style.display = 'none';
                console.log('All articles loaded');
            });
        }
    </script>
</body>
</html>