// Category page functionality for politics, sports, etc.
let currentPage = 1;
let currentCategory = 'all';
let currentSort = 'date';
let currentView = 'grid';
let isLoading = false;
let allArticles = [];

// Sample data for different categories
const sampleData = {
    politics: [
        {
            id: 1,
            title: "Senate Passes Historic Climate Bill",
            excerpt: "After months of debate, the Senate approves landmark climate legislation with bipartisan support.",
            category: "politics",
            author: "Sarah Johnson",
            date: "2024-01-15",
            image: "https://via.placeholder.com/400x250/3498db/ffffff?text=Politics+News",
            featured: true
        },
        {
            id: 2,
            title: "Presidential Election Campaign Updates",
            excerpt: "Latest developments from the campaign trail as candidates prepare for the upcoming primaries.",
            category: "politics",
            author: "Michael Chen",
            date: "2024-01-14",
            image: "https://via.placeholder.com/400x250/e74c3c/ffffff?text=Election+News",
            featured: false
        },
        {
            id: 3,
            title: "Supreme Court Reviews Major Case",
            excerpt: "The highest court in the land considers a case that could reshape constitutional interpretation.",
            category: "politics",
            author: "Dr. Rachel Adams",
            date: "2024-01-13",
            image: "https://via.placeholder.com/400x250/9b59b6/ffffff?text=Supreme+Court",
            featured: true
        },
        {
            id: 4,
            title: "International Trade Agreement Signed",
            excerpt: "New trade deal promises to boost economic cooperation between allied nations.",
            category: "politics",
            author: "John Anderson",
            date: "2024-01-12",
            image: "https://via.placeholder.com/400x250/f39c12/ffffff?text=Trade+Deal",
            featured: false
        },
        {
            id: 5,
            title: "Local Government Reforms Announced",
            excerpt: "City officials unveil comprehensive plan to modernize municipal services and infrastructure.",
            category: "politics",
            author: "Maria Gonzalez",
            date: "2024-01-11",
            image: "https://via.placeholder.com/400x250/27ae60/ffffff?text=Local+Gov",
            featured: false
        }
    ],
    sports: [
        {
            id: 6,
            title: "Championship Game Breaks Viewership Records",
            excerpt: "Historic matchup draws largest television audience in sports broadcasting history.",
            category: "sports",
            author: "Maria Gonzalez",
            date: "2024-01-15",
            image: "https://via.placeholder.com/400x250/27ae60/ffffff?text=Championship",
            featured: true,
            sport: "football"
        },
        {
            id: 7,
            title: "Olympic Training Facilities Unveiled",
            excerpt: "State-of-the-art training complex opens to prepare athletes for upcoming international competition.",
            category: "sports",
            author: "Dr. James Wilson",
            date: "2024-01-14",
            image: "https://via.placeholder.com/400x250/3498db/ffffff?text=Olympics",
            featured: true,
            sport: "olympics"
        },
        {
            id: 8,
            title: "Basketball Season Reaches Playoffs",
            excerpt: "Intense competition heats up as teams battle for championship positioning.",
            category: "sports",
            author: "Sarah Johnson",
            date: "2024-01-13",
            image: "https://via.placeholder.com/400x250/e74c3c/ffffff?text=Basketball",
            featured: false,
            sport: "basketball"
        },
        {
            id: 9,
            title: "Tennis Tournament Sees Upset Victory",
            excerpt: "Unseeded player defeats former world champion in straight sets stunning upset.",
            category: "sports",
            author: "Michael Chen",
            date: "2024-01-12",
            image: "https://via.placeholder.com/400x250/9b59b6/ffffff?text=Tennis",
            featured: false,
            sport: "tennis"
        },
        {
            id: 10,
            title: "Soccer World Cup Preparations Begin",
            excerpt: "National teams announce preliminary squads for the upcoming international tournament.",
            category: "sports",
            author: "John Anderson",
            date: "2024-01-11",
            image: "https://via.placeholder.com/400x250/f39c12/ffffff?text=Soccer",
            featured: false,
            sport: "soccer"
        },
        {
            id: 11,
            title: "Baseball Season Opening Day Delayed",
            excerpt: "Weather concerns force postponement of highly anticipated season opener.",
            category: "sports",
            author: "Dr. Rachel Adams",
            date: "2024-01-10",
            image: "https://via.placeholder.com/400x250/95a5a6/ffffff?text=Baseball",
            featured: false,
            sport: "baseball"
        }
    ]
};

// Initialize category page
function initializeCategoryPage(category) {
    currentCategory = category;
    allArticles = sampleData[category] || [];
    
    // Update page statistics
    updateStatistics();
    
    // Setup event listeners
    setupEventListeners();
    
    // Setup mobile menu
    setupMobileMenu();
    
    // Setup search functionality
    setupSearchFunctionality();
    
    // Load initial articles
    loadArticles();
}

// Update statistics display
function updateStatistics() {
    const totalArticles = allArticles.length;
    const recentArticles = allArticles.filter(article => {
        const articleDate = new Date(article.date);
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        return articleDate >= weekAgo;
    }).length;
    const featuredCount = allArticles.filter(article => article.featured).length;
    
    // Animate counters
    animateCounter('total-articles', totalArticles);
    animateCounter('recent-articles', recentArticles);
    animateCounter('featured-count', featuredCount);
}

// Animate counter numbers
function animateCounter(elementId, target) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    let current = 0;
    const increment = target / 20;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 50);
}

// Setup event listeners
function setupEventListeners() {
    // Sort dropdown
    const sortSelect = document.getElementById('sort-by');
    if (sortSelect) {
        sortSelect.addEventListener('change', (e) => {
            currentSort = e.target.value;
            currentPage = 1;
            loadArticles();
        });
    }
    
    // View toggle buttons
    const gridViewBtn = document.getElementById('grid-view');
    const listViewBtn = document.getElementById('list-view');
    
    if (gridViewBtn) {
        gridViewBtn.addEventListener('click', () => {
            currentView = 'grid';
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
            loadArticles();
        });
    }
    
    if (listViewBtn) {
        listViewBtn.addEventListener('click', () => {
            currentView = 'list';
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
            loadArticles();
        });
    }
    
    // Sports filter buttons (for sports page)
    const sportsFilterBtns = document.querySelectorAll('.sports-filter-btn');
    sportsFilterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const sport = e.target.dataset.sport;
            
            // Update active button
            sportsFilterBtns.forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            
            // Filter articles
            if (sport === 'all') {
                allArticles = sampleData[currentCategory] || [];
            } else {
                allArticles = (sampleData[currentCategory] || []).filter(article => 
                    article.sport === sport
                );
            }
            
            currentPage = 1;
            updateStatistics();
            loadArticles();
        });
    });
    
    // Load more button
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => {
            currentPage++;
            loadArticles(true);
        });
    }
}

// Setup mobile menu
function setupMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
        
        // Close menu when clicking on links
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });
    }
}

// Setup search functionality
function setupSearchFunctionality() {
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const query = e.target.value.trim();
                if (query.length > 0) {
                    window.location.href = `index.html?search=${encodeURIComponent(query)}`;
                }
            }
        });
    }
}

// Load and display articles
function loadArticles(append = false) {
    const container = document.querySelector(`#${currentCategory}-news-container`) || 
                    document.querySelector('.news-container');
    
    if (!container) return;
    
    if (isLoading) return;
    isLoading = true;
    
    // Show loading if not appending
    if (!append) {
        container.innerHTML = `
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <p>Loading ${currentCategory} news...</p>
            </div>
        `;
    }
    
    // Load articles immediately
    const sortedArticles = sortArticles([...allArticles]);
    const articlesPerPage = 6;
    const startIndex = (currentPage - 1) * articlesPerPage;
    const endIndex = startIndex + articlesPerPage;
    const articlesToShow = sortedArticles.slice(0, endIndex);
    
    if (!append) {
        container.innerHTML = '';
    } else {
        // Remove existing loading container
        const existingLoading = container.querySelector('.loading-container');
        if (existingLoading) {
            existingLoading.remove();
        }
    }
    
    if (currentView === 'grid') {
        container.className = 'news-grid';
        if (!append) {
            articlesToShow.forEach(article => {
                container.appendChild(createNewsCard(article, 'grid'));
            });
        } else {
            const newArticles = sortedArticles.slice(startIndex, endIndex);
            newArticles.forEach(article => {
                container.appendChild(createNewsCard(article, 'grid'));
            });
        }
    } else {
        container.className = 'news-list';
        if (!append) {
            articlesToShow.forEach(article => {
                container.appendChild(createNewsCard(article, 'list'));
            });
        } else {
            const newArticles = sortedArticles.slice(startIndex, endIndex);
            newArticles.forEach(article => {
                container.appendChild(createNewsCard(article, 'list'));
            });
        }
    }
    
    // Show/hide load more button
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        if (endIndex < sortedArticles.length) {
            loadMoreBtn.style.display = 'inline-block';
        } else {
            loadMoreBtn.style.display = 'none';
        }
    }
    
    isLoading = false;
}

// Sort articles based on current sort setting
function sortArticles(articles) {
    switch (currentSort) {
        case 'date':
            return articles.sort((a, b) => new Date(b.date) - new Date(a.date));
        case 'date-asc':
            return articles.sort((a, b) => new Date(a.date) - new Date(b.date));
        case 'title':
            return articles.sort((a, b) => a.title.localeCompare(b.title));
        case 'author':
            return articles.sort((a, b) => a.author.localeCompare(b.author));
        default:
            return articles;
    }
}

// Create news card element
function createNewsCard(article, viewType = 'grid') {
    const card = document.createElement('div');
    
    if (viewType === 'grid') {
        card.className = 'news-card';
        card.innerHTML = `
            <img src="${article.image}" alt="${article.title}" loading="lazy">
            <div class="card-content">
                <div class="card-category">${article.category.charAt(0).toUpperCase() + article.category.slice(1)}</div>
                <h3 class="card-title">${article.title}</h3>
                <p class="card-excerpt">${article.excerpt}</p>
                <div class="card-meta">
                    <span class="card-author">${article.author}</span>
                    <span class="card-date">${formatDate(article.date)}</span>
                </div>
            </div>
        `;
    } else {
        card.className = 'news-card list-view';
        card.innerHTML = `
            <div class="list-content">
                <img src="${article.image}" alt="${article.title}" loading="lazy">
                <div class="list-text">
                    <div class="card-category">${article.category.charAt(0).toUpperCase() + article.category.slice(1)}</div>
                    <h3 class="card-title">${article.title}</h3>
                    <p class="card-excerpt">${article.excerpt}</p>
                    <div class="card-meta">
                        <span class="card-author">${article.author}</span>
                        <span class="card-date">${formatDate(article.date)}</span>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Add click event for article detail
    card.addEventListener('click', () => {
        showArticleModal(article);
    });
    
    return card;
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Show article modal
function showArticleModal(article) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'article-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>${article.title}</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="article-meta">
                    <span class="article-category">${article.category.charAt(0).toUpperCase() + article.category.slice(1)}</span>
                    <span class="article-author">By ${article.author}</span>
                    <span class="article-date">${formatDate(article.date)}</span>
                </div>
                <img src="${article.image}" alt="${article.title}" class="article-image">
                <div class="article-content">
                    <p>${article.excerpt}</p>
                    <p>This is a sample news article. In a real implementation, this would contain the full article content retrieved from your news API or database.</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    // Close modal events
    const closeBtn = modal.querySelector('.modal-close');
    closeBtn.addEventListener('click', closeModal);
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') {
            closeModal();
            document.removeEventListener('keydown', escHandler);
        }
    });
    
    function closeModal() {
        document.body.removeChild(modal);
        document.body.style.overflow = '';
    }
}

// Add styles for list view and modal
const additionalStyles = `
/* List View Styles */
.news-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.news-card.list-view {
    cursor: pointer;
}

.list-content {
    display: flex;
    gap: 2rem;
    align-items: start;
}

.list-content img {
    width: 200px;
    height: 130px;
    object-fit: cover;
    border-radius: 10px;
    flex-shrink: 0;
}

.list-text {
    flex: 1;
}

.list-text .card-title {
    margin-bottom: 0.5rem;
}

.list-text .card-excerpt {
    margin-bottom: 1rem;
}

/* Article Modal Styles */
.article-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
    padding: 2rem;
}

.modal-content {
    background: white;
    border-radius: 15px;
    max-width: 800px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem;
    border-bottom: 1px solid #ecf0f1;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border-radius: 15px 15px 0 0;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    color: white;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.modal-close:hover {
    opacity: 1;
}

.modal-body {
    padding: 2rem;
}

.article-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.article-category {
    background: #3498db;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
}

.article-author,
.article-date {
    color: #7f8c8d;
    font-size: 0.9rem;
}

.article-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.article-content p {
    margin-bottom: 1.5rem;
    line-height: 1.8;
    color: #2c3e50;
}

/* Responsive Modal */
@media (max-width: 768px) {
    .article-modal {
        padding: 1rem;
    }
    
    .modal-header {
        padding: 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .article-meta {
        gap: 1rem;
    }
    
    .list-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .list-content img {
        width: 100%;
        height: 200px;
    }
}
`;

// Add additional styles to the document
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);