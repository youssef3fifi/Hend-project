/**
 * Main JavaScript File
 * Common functionality for all pages
 */

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Update cart count on page load
    updateCartCount();
});

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of toast (success, error, info)
 */
function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-exclamation-circle' : 
                 'fa-info-circle';
    
    toast.innerHTML = `
        <i class="fas ${icon} toast-icon"></i>
        <div>${message}</div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Update cart count in header
 */
function updateCartCount() {
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        cartCount.textContent = storage.getCartCount();
    }
}

/**
 * Add item to cart
 * @param {number} bookId - Book ID
 * @param {number} quantity - Quantity to add
 */
function addToCart(bookId, quantity = 1) {
    const result = storage.addToCart(bookId, quantity);
    
    if (result.success) {
        showToast('Item added to cart!', 'success');
        updateCartCount();
    } else {
        showToast(result.message || 'Failed to add to cart', 'error');
    }
}

/**
 * Format price
 * @param {number} price - Price to format
 * @returns {string} Formatted price
 */
function formatPrice(price) {
    return '$' + parseFloat(price).toFixed(2);
}

/**
 * Generate star rating HTML
 * @param {number} rating - Rating value (0-5)
 * @returns {string} HTML for stars
 */
function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star"></i>';
        } else if (i - 0.5 <= rating) {
            stars += '<i class="fas fa-star-half-alt"></i>';
        } else {
            stars += '<i class="far fa-star"></i>';
        }
    }
    return stars;
}

/**
 * Get stock badge HTML
 * @param {number} stock - Stock quantity
 * @returns {string} HTML for stock badge
 */
function getStockBadge(stock) {
    if (stock === 0) {
        return '<span class="stock-badge out-of-stock">Out of Stock</span>';
    } else if (stock < 10) {
        return '<span class="stock-badge low-stock">Low Stock</span>';
    } else {
        return '<span class="stock-badge in-stock">In Stock</span>';
    }
}

/**
 * Show loading spinner
 * @param {HTMLElement} element - Element to show spinner in
 */
function showLoading(element) {
    element.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            <p>Loading...</p>
        </div>
    `;
}

/**
 * Show empty state
 * @param {HTMLElement} element - Element to show empty state in
 * @param {string} message - Message to display
 */
function showEmptyState(element, message = 'No items found') {
    element.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>${message}</h3>
        </div>
    `;
}
