<?php
$pageTitle = 'Shop';
include '../includes/header.php';
?>

<div class="container">
    <div class="section-title">
        <h2>Browse Our Collection</h2>
        <p>Find your perfect book</p>
    </div>
    
    <!-- Filters -->
    <div class="filters">
        <div class="filter-row">
            <div class="filter-group">
                <label for="categoryFilter">Category</label>
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="searchFilter">Search</label>
                <input type="text" id="searchFilter" placeholder="Search books...">
            </div>
            
            <div class="filter-group">
                <label for="minPrice">Min Price</label>
                <input type="number" id="minPrice" placeholder="$0" min="0" step="0.01">
            </div>
            
            <div class="filter-group">
                <label for="maxPrice">Max Price</label>
                <input type="number" id="maxPrice" placeholder="$100" min="0" step="0.01">
            </div>
            
            <div class="filter-group" style="display: flex; align-items: flex-end;">
                <button class="btn btn-primary" onclick="applyFilters()">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </div>
    </div>
    
    <!-- Books Grid -->
    <div class="books-grid" id="booksGrid">
        <div class="loading">
            <div class="spinner"></div>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="pagination" id="pagination"></div>
</div>

<script>
let currentPage = 1;
let totalPages = 1;

// Load books
async function loadBooks(page = 1) {
    try {
        showLoading(document.getElementById('booksGrid'));
        
        const params = new URLSearchParams();
        params.append('page', page);
        params.append('limit', 12);
        
        const category = document.getElementById('categoryFilter').value;
        if (category) params.append('category', category);
        
        const search = document.getElementById('searchFilter').value;
        if (search) params.append('search', search);
        
        const minPrice = document.getElementById('minPrice').value;
        if (minPrice) params.append('min_price', minPrice);
        
        const maxPrice = document.getElementById('maxPrice').value;
        if (maxPrice) params.append('max_price', maxPrice);
        
        const response = await fetch(`${API_ENDPOINTS.books}?${params}`);
        const data = await response.json();
        
        const grid = document.getElementById('booksGrid');
        
        if (data.success && data.data.length > 0) {
            grid.innerHTML = data.data.map(book => `
                <div class="book-card">
                    <div class="book-card-image">
                        <img src="${book.image_url || 'https://via.placeholder.com/300x400?text=No+Image'}" 
                             alt="${book.title}"
                             onerror="this.src='https://via.placeholder.com/300x400?text=No+Image'">
                    </div>
                    <div class="book-card-content">
                        <h3 class="book-card-title">
                            <a href="book-details.php?id=${book.id}">${book.title}</a>
                        </h3>
                        <p class="book-card-author">${book.author}</p>
                        <div class="book-card-rating">
                            <span class="stars">${generateStars(book.rating)}</span>
                            <span>${book.rating}</span>
                        </div>
                        <div class="book-card-footer">
                            <span class="book-card-price">${formatPrice(book.price)}</span>
                            ${getStockBadge(book.stock_quantity)}
                        </div>
                        <button class="btn btn-primary btn-block mt-2" 
                                onclick="addToCart(${book.id})"
                                ${book.stock_quantity === 0 ? 'disabled' : ''}>
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
            `).join('');
            
            // Update pagination
            currentPage = data.pagination.page;
            totalPages = data.pagination.pages;
            updatePagination();
        } else {
            showEmptyState(grid, 'No books found');
            document.getElementById('pagination').innerHTML = '';
        }
    } catch (error) {
        console.error('Error loading books:', error);
        document.getElementById('booksGrid').innerHTML = '<p>Error loading books</p>';
    }
}

// Update pagination
function updatePagination() {
    const pagination = document.getElementById('pagination');
    
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let html = `
        <button onclick="loadBooks(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>
    `;
    
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            html += `
                <button onclick="loadBooks(${i})" class="${i === currentPage ? 'active' : ''}">
                    ${i}
                </button>
            `;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            html += '<button disabled>...</button>';
        }
    }
    
    html += `
        <button onclick="loadBooks(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>
    `;
    
    pagination.innerHTML = html;
}

// Load categories for filter
async function loadCategories() {
    try {
        const response = await fetch(API_ENDPOINTS.categories);
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('categoryFilter');
            data.data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

// Apply filters
function applyFilters() {
    loadBooks(1);
}

// Initialize from URL parameters
function initFromUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('category')) {
        document.getElementById('categoryFilter').value = urlParams.get('category');
    }
    
    if (urlParams.has('search')) {
        document.getElementById('searchFilter').value = urlParams.get('search');
    }
}

// Allow Enter key in search
document.getElementById('searchFilter').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    initFromUrlParams();
    loadBooks();
});
</script>

<?php
$extraScripts = [];
include '../includes/footer.php';
?>
