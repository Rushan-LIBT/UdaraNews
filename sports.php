<?php
// Prevent any output before headers
ob_start();

require_once 'config.php';

// Initialize variables with default values
$sportsNewsData = [];
$featuredSportsData = [];

// Load sports news data on server side
try {
    $pdo = getDBConnection();

    // Check if database and table exist, if not, initialize
    $stmt = $pdo->query("SHOW TABLES LIKE 'news'");
    if ($stmt->rowCount() == 0) {
        // Table doesn't exist, initialize database
        initializeDatabase();
    }

    // Get sports news
    $stmt = $pdo->prepare('SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news WHERE category = ? ORDER BY created_at DESC LIMIT 20');
    $stmt->execute(['sports']);
    $sportsNewsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get featured sports news
    $stmt = $pdo->prepare('SELECT id, title, summary, content, image, category, author, created_at, is_featured FROM news WHERE category = ? AND is_featured = 1 ORDER BY created_at DESC LIMIT 6');
    $stmt->execute(['sports']);
    $featuredSportsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Fallback to sample data if database fails
    $sportsNewsData = [
        [
            'id' => 1,
            'title' => 'Sports News Coming Soon',
            'summary' => 'Get ready for comprehensive sports coverage and live updates.',
            'content' => 'We are preparing to deliver the best sports news and analysis for you.',
            'image' => 'create_placeholder.php?w=800&h=400&bg=27ae60&text=Sports',
            'category' => 'sports',
            'author' => 'Sports Team',
            'created_at' => date('Y-m-d H:i:s'),
            'is_featured' => 1
        ]
    ];
    $featuredSportsData = $sportsNewsData;
    error_log("Error loading sports data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports - Udara NEWS</title>
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
                    <a href="politics.php" class="nav-link">Politics</a>
                    <a href="sports.php" class="nav-link active">Sports</a>
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
        <section class="hero-section" id="sports-hero">
            <div class="hero-slider">
                <div class="slide active" data-bg-image="">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">Sports</span>
                        <h1 class="slide-title">Loading Sports News...</h1>
                        <p class="slide-excerpt">Get the latest scores, highlights, and analysis from the world of sports</p>
                        <div class="slide-meta">
                            <span class="slide-time">Loading...</span>
                            <span class="slide-author">By Sports Team</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="slider-controls" style="display: none;">
                <button class="slider-btn prev-btn">‹</button>
                <div class="slider-dots"></div>
                <button class="slider-btn next-btn">›</button>
            </div>

            <div class="hero-search">
                <input type="text" class="hero-search-input" placeholder="Search sports news...">
                <button class="hero-search-btn">🔍</button>
            </div>
        </section>

        <section class="category-section">
            <div class="container">
                <div class="category-header">
                    <h2 class="section-title">Sports Coverage</h2>
                    <p class="section-subtitle">Complete coverage of games, matches, tournaments, and athletic achievements</p>
                </div>

                <div class="category-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="total-articles">0</div>
                        <div class="stat-label">Total Articles</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="recent-articles">0</div>
                        <div class="stat-label">This Week</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="featured-count">0</div>
                        <div class="stat-label">Featured Stories</div>
                    </div>
                </div>

                <div class="sports-categories">
                    <h4>Sports Categories</h4>
                    <div class="sports-filters">
                        <button class="sports-filter-btn active" data-sport="all">All Sports</button>
                        <button class="sports-filter-btn" data-sport="football">Football</button>
                        <button class="sports-filter-btn" data-sport="basketball">Basketball</button>
                        <button class="sports-filter-btn" data-sport="baseball">Baseball</button>
                        <button class="sports-filter-btn" data-sport="soccer">Soccer</button>
                        <button class="sports-filter-btn" data-sport="tennis">Tennis</button>
                        <button class="sports-filter-btn" data-sport="olympics">Olympics</button>
                    </div>
                </div>

                <div class="category-controls">
                    <div class="sort-controls">
                        <label for="sort-by">Sort by:</label>
                        <select id="sort-by">
                            <option value="date">Latest First</option>
                            <option value="date-asc">Oldest First</option>
                            <option value="title">Title A-Z</option>
                            <option value="author">Author</option>
                        </select>
                    </div>
                    <div class="view-controls">
                        <button id="grid-view" class="view-btn active">Grid</button>
                        <button id="list-view" class="view-btn">List</button>
                    </div>
                </div>

                <div class="news-container" id="sports-news-container">
                    <div class="loading-container">
                        <div class="loading-spinner"></div>
                        <p>Loading sports news...</p>
                    </div>
                </div>

                <div class="load-more-container">
                    <button id="load-more-btn" class="load-more-btn" style="display: none;">Load More Articles</button>
                </div>
            </div>
        </section>

        <section class="live-scores">
            <div class="container">
                <h2 class="section-title">Live Scores & Updates</h2>
                <div class="scores-grid">
                    <div class="score-card">
                        <div class="score-header">
                            <span class="sport-badge">Football</span>
                            <span class="live-badge">LIVE</span>
                        </div>
                        <div class="score-content">
                            <div class="team">
                                <h5>Team A</h5>
                                <div class="score">21</div>
                            </div>
                            <div class="vs">VS</div>
                            <div class="team">
                                <h5>Team B</h5>
                                <div class="score">14</div>
                            </div>
                        </div>
                    </div>
                    <div class="score-card">
                        <div class="score-header">
                            <span class="sport-badge basketball">Basketball</span>
                            <span class="time-badge">Q3 8:45</span>
                        </div>
                        <div class="score-content">
                            <div class="team">
                                <h5>Lions</h5>
                                <div class="score">89</div>
                            </div>
                            <div class="vs">VS</div>
                            <div class="team">
                                <h5>Eagles</h5>
                                <div class="score">76</div>
                            </div>
                        </div>
                    </div>
                    <div class="score-card">
                        <div class="score-header">
                            <span class="sport-badge soccer">Soccer</span>
                            <span class="time-badge">85'</span>
                        </div>
                        <div class="score-content">
                            <div class="team">
                                <h5>United</h5>
                                <div class="score">2</div>
                            </div>
                            <div class="vs">VS</div>
                            <div class="team">
                                <h5>City</h5>
                                <div class="score">1</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="trending-section">
            <div class="container">
                <h2 class="section-title">Trending in Sports</h2>
                <div class="trending-tags">
                    <span class="tag">Championship Finals</span>
                    <span class="tag">Player Transfers</span>
                    <span class="tag">Olympic Games</span>
                    <span class="tag">Rookie Records</span>
                    <span class="tag">Team Rankings</span>
                    <span class="tag">Injury Updates</span>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Udara <span class="brand-highlight">NEWS</span></h3>
                    <p>Your trusted source for breaking news and updates from around the world.</p>
                    <div class="social-links">
                        <a href="#" class="social-link">📘</a>
                        <a href="#" class="social-link">🐦</a>
                        <a href="#" class="social-link">📷</a>
                        <a href="#" class="social-link">💼</a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Categories</h4>
                    <ul>
                        <li><a href="politics.php">Politics</a></li>
                        <li><a href="sports.php">Sports</a></li>
                        <li><a href="#technology">Technology</a></li>
                        <li><a href="#business">Business</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="contact.html">Contact</a></li>
                        <li><a href="#privacy">Privacy Policy</a></li>
                        <li><a href="#terms">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p>📧 info@udaranews.com</p>
                    <p>📞 +1 (555) 123-4567</p>
                    <p>📍 123 News Street, City, Country</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Udara NEWS. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Sports news data loaded from server-side PHP
        const serverSportsData = <?php echo json_encode($sportsNewsData); ?>;
        const serverFeaturedSports = <?php echo json_encode($featuredSportsData); ?>;

        // Sports-specific data for the news grid
        let sportsNews = [
            {
                id: 1,
                title: "Championship Victory Sparks City-Wide Celebration",
                summary: "Local team's stunning championship win brings thousands to the streets in unprecedented celebration.",
                content: "The city erupted in celebration last night as the home team clinched their first championship in over two decades...",
                image: "create_placeholder.php?w=800&h=400&bg=27ae60&text=Sports",
                category: "football",
                author: "Mike Rodriguez",
                created_at: "2024-01-16T18:00:00Z",
                is_featured: 1
            },
            {
                id: 2,
                title: "Olympic Preparations Show Promising Results",
                summary: "Athletes demonstrate exceptional form and readiness as Olympic trials conclude with record-breaking performances.",
                content: "The Olympic trials wrapped up this weekend with several world records broken and new stars emerging...",
                image: "create_placeholder.php?w=800&h=400&bg=3498db&text=Olympics",
                category: "olympics",
                author: "Sarah Chen",
                created_at: "2024-01-16T15:00:00Z",
                is_featured: 1
            },
            {
                id: 3,
                title: "Basketball Season Reaches Thrilling Climax",
                summary: "Playoff races intensify as teams make final pushes for postseason positioning in dramatic fashion.",
                content: "With just weeks remaining in the regular season, the basketball playoff picture is becoming clearer...",
                image: "create_placeholder.php?w=800&h=400&bg=e74c3c&text=Basketball",
                category: "basketball",
                author: "David Johnson",
                created_at: "2024-01-16T12:00:00Z",
                is_featured: 0
            }
        ];

        // Initialize sports page with API-first functionality
        document.addEventListener('DOMContentLoaded', async function() {
            console.log('Loading sports page with API-first approach...');
            console.log('Server-side fallback data available:', {
                serverSportsData: serverSportsData.length,
                serverFeaturedSports: serverFeaturedSports.length
            });

            // Load sports news using API
            await loadSportsNews();

            // Load featured sports stories for hero slider
            await loadFeaturedSports();

            // Setup filter buttons
            setupSportsFilters();

            // Initialize hero slider
            initializeHeroSlider();

            // Initialize mobile menu
            setupMobileMenu();

            // Initialize view controls
            setupViewControls();

            // Initialize sort controls
            setupSortControls();

            // Setup search functionality
            setupSportsSearch();

            console.log('Sports page loaded successfully with API integration!');
        });

        async function loadSportsNews() {
            const container = document.getElementById('sports-news-container');

            try {
                NewsUtils.showLoading(container, 'Loading sports news...');

                // Try API first
                const response = await newsAPI.getNewsByCategory('sports', { limit: 20 });

                if (response.data && response.data.length > 0) {
                    console.log(`Loaded ${response.data.length} sports articles from API`);
                    sportsNews = response.data;
                    displaySportsNews(sportsNews);
                    updateStats(sportsNews);
                } else {
                    throw new Error('No sports articles returned from API');
                }

            } catch (error) {
                console.warn('Sports API failed, falling back to server-side data:', error);

                // Fallback to server-side data
                if (serverSportsData && serverSportsData.length > 0) {
                    console.log(`Using ${serverSportsData.length} sports articles from server-side data`);
                    sportsNews = serverSportsData;
                    displaySportsNews(sportsNews);
                    updateStats(sportsNews);
                } else {
                    NewsUtils.showEmpty(container, 'No sports articles found. Database may be empty or not connected.');
                }
            }
        }

        // Date formatting utility
        function formatDate(dateString) {
            if (!dateString) return 'Unknown date';
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} minutes ago`;
            if (diffHours < 24) return `${diffHours} hours ago`;
            if (diffDays < 7) return `${diffDays} days ago`;

            return date.toLocaleDateString();
        }

        // News card creation function
        function createNewsCard(article) {
            const card = document.createElement('div');
            card.className = 'news-card';
            card.onclick = () => showArticleModal(article);

            card.innerHTML = `
                <div class="news-image">
                    <img src="${article.image || 'create_placeholder.php?w=400&h=250&bg=3498db&text=Sports'}"
                         alt="${article.title}" loading="lazy">
                    <div class="news-category">${article.category || 'Sports'}</div>
                </div>
                <div class="news-content">
                    <h3 class="news-title">${article.title}</h3>
                    <p class="news-excerpt">${article.summary || article.content?.substring(0, 120) + '...' || 'No summary available'}</p>
                    <div class="news-meta">
                        <span class="news-author">${article.author || 'Sports Team'}</span>
                        <span class="news-date">${formatDate(article.created_at)}</span>
                    </div>
                </div>
            `;

            return card;
        }

        function displaySportsNews(newsData) {
            const container = document.getElementById('sports-news-container');
            container.innerHTML = '';

            newsData.forEach(article => {
                const newsCard = createNewsCard(article);
                container.appendChild(newsCard);
            });

            console.log(`Displayed ${newsData.length} sports news articles`);
        }

        function loadFeaturedSports() {
            console.log('Loading featured sports news from server-side data...');

            if (serverFeaturedSports && serverFeaturedSports.length > 0) {
                console.log(`Loaded ${serverFeaturedSports.length} featured sports articles from database`);
                initializeHeroSliderWithData(serverFeaturedSports);
            } else {
                console.log('No featured sports articles found in database, using sample data');
                const featuredSample = sportsNews.filter(article => article.is_featured);
                if (featuredSample.length > 0) {
                    initializeHeroSliderWithData(featuredSample);
                }
            }
        }

        function setupSportsFilters() {
            const filterButtons = document.querySelectorAll('.sports-filter-btn');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const sport = this.getAttribute('data-sport');
                    filterSportsNews(sport);
                });
            });
        }

        function filterSportsNews(sport) {
            const container = document.getElementById('sports-news-container');
            container.innerHTML = '';

            const filteredNews = sport === 'all'
                ? sportsNews
                : sportsNews.filter(article => article.category === sport);

            filteredNews.forEach(article => {
                const newsCard = createNewsCard(article);
                container.appendChild(newsCard);
            });

            console.log(`Filtered to ${filteredNews.length} articles for sport: ${sport}`);
        }

        // Article modal functionality
        function showArticleModal(article) {
            const modal = document.getElementById('articleModal') || createModal();
            const modalContent = modal.querySelector('.modal-body');

            modalContent.innerHTML = `
                <div class="modal-article-image">
                    <img src="${article.image || 'create_placeholder.php?w=800&h=400&bg=3498db&text=Sports'}" alt="${article.title}">
                </div>
                <div class="modal-article-content">
                    <span class="modal-category">${article.category || 'Sports'}</span>
                    <h2>${article.title}</h2>
                    <div class="modal-meta">
                        <span class="modal-author">By ${article.author || 'Sports Team'}</span>
                        <span class="modal-date">${formatDate(article.created_at)}</span>
                    </div>
                    <div class="modal-content">
                        ${article.content || article.summary || 'Full article content not available.'}
                    </div>
                </div>
            `;

            modal.style.display = 'block';
        }

        function createModal() {
            const modal = document.createElement('div');
            modal.id = 'articleModal';
            modal.className = 'modal';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body"></div>
                </div>
            `;

            document.body.appendChild(modal);

            const closeBtn = modal.querySelector('.close');
            closeBtn.onclick = () => modal.style.display = 'none';

            modal.onclick = (e) => {
                if (e.target === modal) modal.style.display = 'none';
            };

            return modal;
        }

        // Hero slider functionality
        function initializeHeroSlider() {
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelector('.slider-dots');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');

            if (slides.length === 0) return;

            let currentSlide = 0;
            const totalSlides = slides.length;

            if (totalSlides > 1) {
                document.querySelector('.slider-controls').style.display = 'flex';

                slides.forEach((_, index) => {
                    const dot = document.createElement('span');
                    dot.className = `dot ${index === 0 ? 'active' : ''}`;
                    dot.addEventListener('click', () => goToSlide(index));
                    dots.appendChild(dot);
                });

                prevBtn.addEventListener('click', () => goToSlide((currentSlide - 1 + totalSlides) % totalSlides));
                nextBtn.addEventListener('click', () => goToSlide((currentSlide + 1) % totalSlides));

                setInterval(() => goToSlide((currentSlide + 1) % totalSlides), 5000);
            }

            function goToSlide(n) {
                slides[currentSlide].classList.remove('active');
                document.querySelectorAll('.dot')[currentSlide].classList.remove('active');

                currentSlide = n;

                slides[currentSlide].classList.add('active');
                document.querySelectorAll('.dot')[currentSlide].classList.add('active');
            }
        }

        function initializeHeroSliderWithData(featuredArticles) {
            const heroSlider = document.querySelector('.hero-slider');
            if (!heroSlider || featuredArticles.length === 0) return;

            heroSlider.innerHTML = '';

            featuredArticles.slice(0, 3).forEach((article, index) => {
                const slide = document.createElement('div');
                slide.className = `slide ${index === 0 ? 'active' : ''}`;
                slide.style.backgroundImage = `url(${article.image || 'create_placeholder.php?w=1200&h=600&bg=27ae60&text=Sports'})`;

                slide.innerHTML = `
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <span class="slide-category">${article.category || 'Sports'}</span>
                        <h1 class="slide-title">${article.title}</h1>
                        <p class="slide-excerpt">${article.summary || article.content?.substring(0, 150) + '...' || ''}</p>
                        <div class="slide-meta">
                            <span class="slide-time">${formatDate(article.created_at)}</span>
                            <span class="slide-author">By ${article.author || 'Sports Team'}</span>
                        </div>
                        <button class="slide-read-more" onclick="showArticleModal(${JSON.stringify(article).replace(/"/g, '&quot;')})">Read Full Story</button>
                    </div>
                `;

                heroSlider.appendChild(slide);
            });

            initializeHeroSlider();
        }

        // Mobile menu functionality
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

        // View controls
        function setupViewControls() {
            const gridViewBtn = document.getElementById('grid-view');
            const listViewBtn = document.getElementById('list-view');
            const newsContainer = document.getElementById('sports-news-container');

            if (gridViewBtn && listViewBtn) {
                gridViewBtn.addEventListener('click', () => {
                    gridViewBtn.classList.add('active');
                    listViewBtn.classList.remove('active');
                    newsContainer.className = 'news-container';
                });

                listViewBtn.addEventListener('click', () => {
                    listViewBtn.classList.add('active');
                    gridViewBtn.classList.remove('active');
                    newsContainer.className = 'news-container list-view';
                });
            }
        }

        // Sort controls
        function setupSortControls() {
            const sortSelect = document.getElementById('sort-by');
            if (sortSelect) {
                sortSelect.addEventListener('change', (e) => {
                    const sortBy = e.target.value;
                    let sortedNews = [...sportsNews];

                    switch (sortBy) {
                        case 'date':
                            sortedNews.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                            break;
                        case 'date-asc':
                            sortedNews.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                            break;
                        case 'title':
                            sortedNews.sort((a, b) => a.title.localeCompare(b.title));
                            break;
                        case 'author':
                            sortedNews.sort((a, b) => (a.author || '').localeCompare(b.author || ''));
                            break;
                    }

                    displaySportsNews(sortedNews);
                });
            }
        }

        // Update statistics
        function updateStats(newsData) {
            const totalArticlesEl = document.getElementById('total-articles');
            const recentArticlesEl = document.getElementById('recent-articles');
            const featuredCountEl = document.getElementById('featured-count');

            if (totalArticlesEl) totalArticlesEl.textContent = newsData.length;

            if (recentArticlesEl) {
                const oneWeekAgo = new Date();
                oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
                const recentCount = newsData.filter(article =>
                    new Date(article.created_at) > oneWeekAgo
                ).length;
                recentArticlesEl.textContent = recentCount;
            }

            if (featuredCountEl) {
                const featuredCount = newsData.filter(article => article.is_featured).length;
                featuredCountEl.textContent = featuredCount;
            }
        }

        // Sports search functionality
        function setupSportsSearch() {
            const searchInput = document.querySelector('.hero-search-input');
            const searchBtn = document.querySelector('.hero-search-btn');

            if (searchInput && searchBtn) {
                async function performSearch() {
                    const searchTerm = searchInput.value.trim();
                    if (!searchTerm) {
                        await loadSportsNews(); // Reload sports news
                        return;
                    }

                    const container = document.getElementById('sports-news-container');

                    try {
                        NewsUtils.showLoading(container, `Searching sports for "${searchTerm}"...`);

                        const response = await newsAPI.searchNews(searchTerm, { category: 'sports', limit: 20 });

                        if (response.data && response.data.length > 0) {
                            console.log(`Found ${response.data.length} sports search results for "${searchTerm}"`);
                            displaySportsNews(response.data);
                        } else {
                            NewsUtils.showEmpty(container, `No sports results found for "${searchTerm}".`);
                        }

                    } catch (error) {
                        console.error('Sports search failed:', error);
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
    </script>
</body>
</html>