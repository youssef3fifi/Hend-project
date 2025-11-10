<?php
$pageTitle = 'Home';
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Welcome to <?php echo APP_NAME; ?></h1>
        <p>Discover your next great read from our extensive collection</p>
        <div class="search-bar">
            <input type="text" id="homeSearch" placeholder="Search for books, authors, or categories..." aria-label="Search books">
            <button onclick="performSearch()"><i class="fas fa-search"></i> Search</button>
        </div>
    </div>
</section>

<div class="container">
    <!-- Categories Section -->
    <section class="categories">
        <div class="section-title">
            <h2>Browse by Category</h2>
            <p>Explore our diverse collection</p>
        </div>
        <div class="categories-grid" id="categoriesGrid">
            <div class="loading">
                <div class="spinner"></div>
            </div>
        </div>
    </section>
    
    <!-- Featured Books Section -->
    <section class="featured-books">
        <div class="section-title">
            <h2>Featured Books</h2>
            <p>Check out our top picks</p>
        </div>
        <div class="books-grid" id="featuredBooksGrid">
            <div class="loading">
                <div class="spinner"></div>
            </div>
        </div>
    </section>
</div>

<script>
// Load categories
async function loadCategories() {
    try {
        const response = await fetch(API_ENDPOINTS.categories);
        const data = await response.json();
        
        const grid = document.getElementById('categoriesGrid');
        
        if (data.success && data.data.length > 0) {
            grid.innerHTML = data.data.map(category => `
                <div class="category-card" onclick="window.location.href='pages/shop.php?category=${category.id}'">
                    <i class="fas fa-book"></i>
                    <h3>${category.name}</h3>
                    <p class="book-count">${category.book_count} books</p>
                </div>
            `).join('');
        } else {
            showEmptyState(grid, 'No categories available');
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        document.getElementById('categoriesGrid').innerHTML = '<p>Error loading categories</p>';
    }
}

// Load featured books
async function loadFeaturedBooks() {
    try {
        const response = await fetch(`${API_ENDPOINTS.books}?limit=8`);
        const data = await response.json();
        
        const grid = document.getElementById('featuredBooksGrid');
        
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
                            <a href="pages/book-details.php?id=${book.id}">${book.title}</a>
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
        } else {
            showEmptyState(grid, 'No books available');
        }
    } catch (error) {
        console.error('Error loading featured books:', error);
        document.getElementById('featuredBooksGrid').innerHTML = '<p>Error loading books</p>';
    }
}

// Perform search
function performSearch() {
    const searchTerm = document.getElementById('homeSearch').value;
    if (searchTerm.trim()) {
        window.location.href = `pages/shop.php?search=${encodeURIComponent(searchTerm)}`;
    }
}

// Allow Enter key to search
document.getElementById('homeSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadFeaturedBooks();
});
</script>

<?php
$extraScripts = [];
include 'includes/footer.php';
?>
