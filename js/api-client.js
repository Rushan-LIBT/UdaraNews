/**
 * Udara News API Client
 * Centralized API client for all news-related operations
 */
class UdaraNewsAPI {
    constructor(baseURL = './api/news.php') {
        this.baseURL = baseURL;
        this.defaultOptions = {
            limit: 20,
            offset: 0
        };

        // Prevent rapid successive API calls and infinite loops
        this.requestCache = new Map();
        this.requestCooldown = 2000; // 2 second cooldown between identical requests
        this.maxRetries = 1; // Maximum retry attempts
        this.activeRequests = new Set(); // Track active requests to prevent duplicates
        this.failedRequests = new Set(); // Track permanently failed requests
    }

    /**
     * Make API request with caching and retry prevention
     * @param {string} action - API action
     * @param {object} params - Request parameters
     * @returns {Promise<object>} API response
     */
    async request(action = '', params = {}) {
        const requestKey = JSON.stringify({ action, params });
        const now = Date.now();

        // Check if this exact request already failed permanently
        if (this.failedRequests.has(requestKey)) {
            console.warn('Request blocked - this request has permanently failed');
            throw new Error('Request permanently failed - using fallback data');
        }

        // Check if same request is currently active (prevent duplicate concurrent requests)
        if (this.activeRequests.has(requestKey)) {
            console.warn('Request blocked - identical request already in progress');
            throw new Error('Request already in progress - using fallback data');
        }

        // Check if same request was made recently (prevent rapid repeated calls)
        if (this.requestCache.has(requestKey)) {
            const lastRequest = this.requestCache.get(requestKey);
            if ((now - lastRequest.timestamp) < this.requestCooldown) {
                console.warn('Request blocked - too soon since last identical request');
                if (lastRequest.response) {
                    return lastRequest.response;
                } else {
                    throw new Error('Request rate limited - using fallback data');
                }
            }
        }

        // Mark request as active
        this.activeRequests.add(requestKey);

        try {
            const url = new URL(this.baseURL, window.location.href);

            // Add action and params to URL
            if (action) url.searchParams.set('action', action);
            Object.keys(params).forEach(key => {
                if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
                    url.searchParams.set(key, params[key]);
                }
            });

            console.log('API Request:', url.toString());

            // Set request timeout to prevent hanging
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000); // 8 second timeout

            const response = await fetch(url.toString(), {
                signal: controller.signal,
                headers: {
                    'Cache-Control': 'no-cache'
                }
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'API request failed');
            }

            // Cache successful response
            this.requestCache.set(requestKey, {
                timestamp: now,
                response: data
            });

            // Remove from active requests
            this.activeRequests.delete(requestKey);

            console.log('API Response:', data);
            return data;

        } catch (error) {
            // Remove from active requests
            this.activeRequests.delete(requestKey);

            // Mark as permanently failed after first failure to prevent retries
            this.failedRequests.add(requestKey);

            // Cache failed request to prevent immediate retry
            this.requestCache.set(requestKey, {
                timestamp: now,
                response: null
            });

            console.error('API Error (marked as permanently failed):', error);
            throw error;
        }
    }

    /**
     * Get all news articles
     * @param {object} options - Request options
     * @returns {Promise<object>} News articles
     */
    async getAllNews(options = {}) {
        const params = { ...this.defaultOptions, ...options };
        return await this.request('get_all', params);
    }

    /**
     * Get news by category
     * @param {string} category - Category name
     * @param {object} options - Request options
     * @returns {Promise<object>} News articles
     */
    async getNewsByCategory(category, options = {}) {
        const params = { ...this.defaultOptions, category, ...options };
        return await this.request('get_by_category', params);
    }

    /**
     * Get featured news
     * @param {string} category - Optional category filter
     * @param {object} options - Request options
     * @returns {Promise<object>} Featured news articles
     */
    async getFeaturedNews(category = '', options = {}) {
        const params = { ...this.defaultOptions, ...options };
        if (category) params.category = category;
        return await this.request('get_featured', params);
    }

    /**
     * Search news articles
     * @param {string} query - Search query
     * @param {object} options - Request options
     * @returns {Promise<object>} Search results
     */
    async searchNews(query, options = {}) {
        const params = { ...this.defaultOptions, search: query, ...options };
        return await this.request('search', params);
    }

    /**
     * Get single news article
     * @param {number} id - Article ID
     * @returns {Promise<object>} Single article
     */
    async getSingleNews(id) {
        return await this.request('get_single', { id });
    }

    /**
     * Get statistics
     * @returns {Promise<object>} Statistics data
     */
    async getStats() {
        return await this.request('get_stats');
    }

    /**
     * Legacy support - get news with old parameters format
     * @param {object} params - Legacy parameters
     * @returns {Promise<object>} News articles
     */
    async getLegacyNews(params = {}) {
        const url = new URL(this.baseURL, window.location.href);
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
                url.searchParams.set(key, params[key]);
            }
        });

        console.log('Legacy API Request:', url.toString());

        const response = await fetch(url.toString());
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'API request failed');
        }

        console.log('Legacy API Response:', data);
        return data;
    }
}

/**
 * Utility functions for common operations
 */
class NewsUtils {
    /**
     * Format date for display
     * @param {string} dateString - ISO date string
     * @returns {string} Formatted date
     */
    static formatDate(dateString) {
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

    /**
     * Create news card HTML
     * @param {object} article - Article data
     * @param {string} cardType - Type of card (news, featured, etc.)
     * @returns {HTMLElement} News card element
     */
    static createNewsCard(article, cardType = 'news') {
        const card = document.createElement('div');
        card.className = `${cardType}-card`;
        card.style.cursor = 'pointer';

        const imageUrl = article.image && article.image.trim() !== ''
            ? article.image
            : `create_placeholder.php?w=400&h=250&bg=3498db&text=${encodeURIComponent(article.category || 'News')}`;

        card.innerHTML = `
            <div class="${cardType}-image">
                <img src="${imageUrl}" alt="${article.title}" loading="lazy"
                     onerror="this.src='create_placeholder.php?w=400&h=250&bg=6c757d&text=Image Error'">
                <div class="${cardType}-category">${article.category || 'General'}</div>
            </div>
            <div class="${cardType}-content">
                <h3 class="${cardType}-title">${article.title}</h3>
                <p class="${cardType}-excerpt">${article.summary || article.content?.substring(0, 120) + '...' || 'No summary available'}</p>
                <div class="${cardType}-meta">
                    <span class="${cardType}-author">By ${article.author || 'Unknown'}</span>
                    <span class="${cardType}-date">${this.formatDate(article.created_at)}</span>
                </div>
            </div>
        `;

        // Add click handler for modal
        card.addEventListener('click', () => {
            NewsUtils.showArticleModal(article);
        });

        return card;
    }

    /**
     * Show article in modal
     * @param {object} article - Article data
     */
    static showArticleModal(article) {
        // Remove existing modal if any
        const existingModal = document.getElementById('articleModal');
        if (existingModal) {
            existingModal.remove();
        }

        const modal = document.createElement('div');
        modal.id = 'articleModal';
        modal.className = 'modal';
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

        const imageUrl = article.image && article.image.trim() !== ''
            ? article.image
            : `create_placeholder.php?w=800&h=400&bg=3498db&text=${encodeURIComponent(article.category || 'News')}`;

        modal.innerHTML = `
            <div class="modal-content" style="
                background: white;
                max-width: 800px;
                max-height: 90vh;
                overflow-y: auto;
                border-radius: 8px;
                position: relative;
            ">
                <button class="modal-close" style="
                    position: absolute;
                    top: 15px;
                    right: 20px;
                    background: none;
                    border: none;
                    font-size: 24px;
                    cursor: pointer;
                    z-index: 1;
                    color: white;
                    text-shadow: 0 0 3px rgba(0,0,0,0.5);
                ">×</button>
                <div class="modal-article-image" style="position: relative;">
                    <img src="${imageUrl}" alt="${article.title}" style="width: 100%; height: 300px; object-fit: cover;">
                </div>
                <div class="modal-article-content" style="padding: 20px;">
                    <span class="modal-category" style="color: #e74c3c; font-weight: bold; margin-bottom: 10px; display: block;">
                        ${(article.category || 'GENERAL').toUpperCase()}
                    </span>
                    <h2 style="margin-bottom: 15px; color: #2c3e50;">${article.title}</h2>
                    <div class="modal-meta" style="color: #666; margin-bottom: 20px; font-size: 14px;">
                        <span>By ${article.author || 'Unknown'}</span> •
                        <span>${NewsUtils.formatDate(article.created_at)}</span>
                    </div>
                    <div class="modal-content-text" style="line-height: 1.6; color: #333;">
                        ${article.content || article.summary || 'Full article content not available.'}
                    </div>
                </div>
            </div>
        `;

        // Add event listeners
        const closeBtn = modal.querySelector('.modal-close');
        closeBtn.onclick = () => modal.remove();

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });

        document.body.appendChild(modal);
    }

    /**
     * Display error message in container
     * @param {HTMLElement} container - Container element
     * @param {string} message - Error message
     */
    static showError(container, message) {
        if (container) {
            container.innerHTML = `
                <div style="text-align: center; color: #e74c3c; padding: 2rem; background: #fdf2f2; border-radius: 8px; margin: 1rem 0;">
                    <strong>Error:</strong> ${message}
                </div>
            `;
        }
    }

    /**
     * Display loading message in container
     * @param {HTMLElement} container - Container element
     * @param {string} message - Loading message
     */
    static showLoading(container, message = 'Loading...') {
        if (container) {
            container.innerHTML = `
                <div style="text-align: center; color: #666; padding: 2rem;">
                    <div class="loading-spinner" style="display: inline-block; width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 10px;"></div>
                    ${message}
                </div>
            `;
        }
    }

    /**
     * Display empty state message in container
     * @param {HTMLElement} container - Container element
     * @param {string} message - Empty state message
     */
    static showEmpty(container, message = 'No articles found.') {
        if (container) {
            container.innerHTML = `
                <div style="text-align: center; color: #666; padding: 2rem; background: #f8f9fa; border-radius: 8px; margin: 1rem 0;">
                    ${message}
                </div>
            `;
        }
    }
}

// Global instance
window.newsAPI = new UdaraNewsAPI();
window.NewsUtils = NewsUtils;

// Add CSS for loading animation
if (!document.getElementById('api-client-styles')) {
    const style = document.createElement('style');
    style.id = 'api-client-styles';
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}

console.log('Udara News API Client loaded successfully');