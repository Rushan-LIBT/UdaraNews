document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });
    }

    // Hero search functionality
    const heroSearchInput = document.querySelector('.hero-search-input');
    const heroSearchBtn = document.querySelector('.hero-search-btn');

    function performSearch() {
        const query = heroSearchInput.value.trim();
        if (query.length > 0) {
            // Filter articles based on search query
            filterNewsBySearch(query);
        }
    }

    if (heroSearchInput && heroSearchBtn) {
        heroSearchBtn.addEventListener('click', performSearch);
        
        heroSearchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    // News filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const newsContainer = document.getElementById('news-container');
    const loadMoreBtn = document.getElementById('load-more-btn');
    
    let currentCategory = 'all';
    let currentPage = 1;
    let isLoading = false;

    // Filter button event listeners
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            // Add active class to clicked button
            button.classList.add('active');
            
            // Get selected category
            currentCategory = button.getAttribute('data-category');
            currentPage = 1;
            
            // Clear news container and load filtered news
            newsContainer.innerHTML = '<div class="loading-container"><div class="loading-spinner"></div><p>Loading...</p></div>';
            loadNews(currentCategory, currentPage, true);
        });
    });

    // Search filtering function
    function filterNewsBySearch(query) {
        const allArticles = document.querySelectorAll('.news-card');
        let visibleCount = 0;

        allArticles.forEach(article => {
            const title = article.querySelector('.card-title')?.textContent.toLowerCase() || '';
            const excerpt = article.querySelector('.card-excerpt')?.textContent.toLowerCase() || '';
            const category = article.querySelector('.card-category')?.textContent.toLowerCase() || '';
            
            const matchesSearch = title.includes(query.toLowerCase()) || 
                                excerpt.includes(query.toLowerCase()) || 
                                category.includes(query.toLowerCase());

            if (matchesSearch) {
                article.style.display = 'block';
                visibleCount++;
            } else {
                article.style.display = 'none';
            }
        });

        // Scroll to news section
        const newsSection = document.querySelector('.news-section');
        if (newsSection) {
            newsSection.scrollIntoView({ behavior: 'smooth' });
        }

        // Show message if no results found
        if (visibleCount === 0) {
            newsContainer.innerHTML += '<div class="no-results"><h3>No articles found</h3><p>Try searching with different keywords.</p></div>';
        }
    }

    // Load more news button
    loadMoreBtn.addEventListener('click', () => {
        if (!isLoading) {
            currentPage++;
            loadNews(currentCategory, currentPage, false);
        }
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Check for search parameter in URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search');
    
    if (searchQuery && searchInput) {
        searchInput.value = searchQuery;
        searchNews(searchQuery);
        loadFeaturedNews();
    } else {
        // Initialize news loading
        loadNews('all', 1, true);
        loadFeaturedNews();
    }

    // Function to load news articles
    function loadNews(category, page, replace = false) {
        isLoading = true;
        loadMoreBtn.textContent = 'Loading...';
        loadMoreBtn.disabled = true;

        fetch(`get_news.php?category=${category}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (replace) {
                        newsContainer.innerHTML = '';
                    } else {
                        // Remove loading spinner if it exists
                        const loading = newsContainer.querySelector('.loading');
                        if (loading) loading.remove();
                    }

                    if (data.news.length === 0 && replace) {
                        newsContainer.innerHTML = '<p class="no-news">No news articles found.</p>';
                    } else {
                        data.news.forEach(article => {
                            const newsCard = createNewsCard(article);
                            newsContainer.appendChild(newsCard);
                        });
                    }

                    // Hide load more button if no more news
                    if (data.news.length < 6) {
                        loadMoreBtn.style.display = 'none';
                    } else {
                        loadMoreBtn.style.display = 'block';
                    }
                } else {
                    console.error('Error loading news:', data.message);
                    if (replace) {
                        newsContainer.innerHTML = '<p class="error">Error loading news articles.</p>';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (replace) {
                    newsContainer.innerHTML = '<p class="error">Error loading news articles.</p>';
                }
            })
            .finally(() => {
                isLoading = false;
                loadMoreBtn.textContent = 'Load More News';
                loadMoreBtn.disabled = false;
            });
    }

    // Function to load featured news
    function loadFeaturedNews() {
        const featuredContainer = document.getElementById('featured-container');
        
        fetch('get_featured_news.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    featuredContainer.innerHTML = '';
                    data.featured.forEach(article => {
                        const featuredCard = createFeaturedCard(article);
                        featuredContainer.appendChild(featuredCard);
                    });
                } else {
                    console.error('Error loading featured news:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Function to create news card HTML
    function createNewsCard(article) {
        const cardCol = document.createElement('div');
        cardCol.className = 'col-lg-4 col-md-6 col-12';
        
        const card = document.createElement('div');
        card.className = 'card h-100 shadow-sm news-card';
        card.innerHTML = `
            <img src="${article.image || 'https://via.placeholder.com/300x200?text=No+Image'}" class="card-img-top" alt="${article.title}" style="height: 200px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
                <div class="mb-2">
                    <span class="badge bg-primary">${article.category}</span>
                </div>
                <h5 class="card-title">${article.title}</h5>
                <p class="card-text flex-grow-1">${article.summary}</p>
                <div class="mt-auto">
                    <small class="text-muted d-flex justify-content-between">
                        <span><i class="bi bi-person me-1"></i>By ${article.author}</span>
                        <span><i class="bi bi-clock me-1"></i>${formatDate(article.date)}</span>
                    </small>
                </div>
            </div>
        `;

        // Add click event to open full article
        card.addEventListener('click', () => {
            openArticleModal(article);
        });
        card.style.cursor = 'pointer';

        cardCol.appendChild(card);
        return cardCol;
    }

    // Function to create featured card HTML
    function createFeaturedCard(article) {
        const cardCol = document.createElement('div');
        cardCol.className = 'col-lg-6 col-12';
        
        const card = document.createElement('div');
        card.className = 'card mb-3 shadow-sm featured-card';
        card.innerHTML = `
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="${article.image || 'https://via.placeholder.com/200x150?text=No+Image'}" 
                         class="img-fluid rounded-start h-100" 
                         alt="${article.title}" 
                         style="object-fit: cover; min-height: 150px;">
                </div>
                <div class="col-md-8">
                    <div class="card-body h-100 d-flex flex-column">
                        <span class="badge bg-danger mb-2 align-self-start">${article.category}</span>
                        <h5 class="card-title">${article.title}</h5>
                        <p class="card-text flex-grow-1">${article.summary.substring(0, 100)}...</p>
                        <small class="text-muted">
                            <i class="bi bi-person me-1"></i>By ${article.author} • 
                            <i class="bi bi-clock me-1"></i>${formatDate(article.date)}
                        </small>
                    </div>
                </div>
            </div>
        `;

        // Add click event to open full article
        card.addEventListener('click', () => {
            openArticleModal(article);
        });
        card.style.cursor = 'pointer';

        cardCol.appendChild(card);
        return cardCol;
    }

    // Function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('en-US', options);
    }

    // Function to open article modal
    function openArticleModal(article) {
        // Create modal if it doesn't exist
        let modal = document.getElementById('article-modal');
        if (!modal) {
            modal = createArticleModal();
            document.body.appendChild(modal);
        }

        // Populate modal content
        modal.querySelector('.modal-image').src = article.image || 'https://via.placeholder.com/800x400?text=No+Image';
        modal.querySelector('.modal-category').textContent = article.category;
        modal.querySelector('.modal-title').textContent = article.title;
        modal.querySelector('.modal-author').textContent = `By ${article.author}`;
        modal.querySelector('.modal-date').textContent = formatDate(article.date);
        modal.querySelector('.modal-content').innerHTML = article.content || article.summary;

        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    // Function to create article modal
    function createArticleModal() {
        const modal = document.createElement('div');
        modal.id = 'article-modal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-overlay">
                <div class="modal-container">
                    <span class="modal-close">&times;</span>
                    <div class="modal-header">
                        <img class="modal-image" src="" alt="">
                        <span class="modal-category"></span>
                        <h2 class="modal-title"></h2>
                        <div class="modal-meta">
                            <span class="modal-author"></span>
                            <span class="modal-date"></span>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="modal-content"></div>
                    </div>
                </div>
            </div>
        `;

        // Add modal styles
        const modalStyles = `
            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                z-index: 10000;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .modal-overlay {
                width: 100%;
                height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .modal-container {
                background: white;
                max-width: 800px;
                max-height: 90vh;
                border-radius: 10px;
                overflow-y: auto;
                position: relative;
            }

            .modal-close {
                position: absolute;
                top: 15px;
                right: 20px;
                font-size: 30px;
                cursor: pointer;
                z-index: 10001;
                color: #333;
            }

            .modal-header {
                position: relative;
            }

            .modal-image {
                width: 100%;
                height: 300px;
                object-fit: cover;
            }

            .modal-category {
                position: absolute;
                bottom: 80px;
                left: 20px;
                background: #e74c3c;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 20px;
                font-size: 0.9rem;
            }

            .modal-title {
                padding: 20px 20px 10px;
                font-size: 1.8rem;
                color: #2c3e50;
                line-height: 1.3;
            }

            .modal-meta {
                padding: 0 20px 20px;
                display: flex;
                justify-content: space-between;
                color: #7f8c8d;
                border-bottom: 1px solid #ecf0f1;
            }

            .modal-body {
                padding: 20px;
            }

            .modal-content {
                line-height: 1.8;
                color: #333;
                font-size: 1.1rem;
            }

            @media (max-width: 768px) {
                .modal-container {
                    margin: 0;
                    border-radius: 0;
                    max-height: 100vh;
                }
                
                .modal-title {
                    font-size: 1.4rem;
                }
                
                .modal-meta {
                    flex-direction: column;
                    gap: 5px;
                }
            }
        `;

        // Add styles to head if not already added
        if (!document.getElementById('modal-styles')) {
            const styleSheet = document.createElement('style');
            styleSheet.id = 'modal-styles';
            styleSheet.textContent = modalStyles;
            document.head.appendChild(styleSheet);
        }

        // Add close event listeners
        modal.querySelector('.modal-close').addEventListener('click', closeModal);
        modal.querySelector('.modal-overlay').addEventListener('click', (e) => {
            if (e.target === modal.querySelector('.modal-overlay')) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                closeModal();
            }
        });

        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        return modal;
    }

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            searchTimeout = setTimeout(() => {
                if (query.length > 2) {
                    searchNews(query);
                } else if (query.length === 0) {
                    loadNews(currentCategory, 1, true);
                }
            }, 300);
        });
    }

    function searchNews(query) {
        newsContainer.innerHTML = '<div class="loading"><div class="loading-spinner"></div></div>';
        
        fetch(`search_news.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    newsContainer.innerHTML = '';
                    if (data.news.length === 0) {
                        newsContainer.innerHTML = '<p class="no-news">No news articles found for your search.</p>';
                    } else {
                        data.news.forEach(article => {
                            const newsCard = createNewsCard(article);
                            newsContainer.appendChild(newsCard);
                        });
                    }
                    loadMoreBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                newsContainer.innerHTML = '<p class="error">Error searching news articles.</p>';
            });
    }
});