<?php
$pageTitle = 'Book Details';
include '../includes/header.php';

// Get book ID from URL
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bookId === 0) {
    echo '<div class="container"><p>Invalid book ID</p></div>';
    include '../includes/footer.php';
    exit;
}
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: 2rem;">
        <ol style="list-style: none; display: flex; gap: 0.5rem; color: var(--text-light);">
            <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
            <li>/</li>
            <li><a href="<?php echo BASE_URL; ?>/pages/shop.php">Shop</a></li>
            <li>/</li>
            <li id="breadcrumbTitle">Book Details</li>
        </ol>
    </nav>
    
    <!-- Book Details -->
    <div id="bookDetails">
        <div class="loading">
            <div class="spinner"></div>
            <p>Loading book details...</p>
        </div>
    </div>
    
    <!-- Related Books -->
    <div style="margin-top: 3rem;">
        <div class="section-title">
            <h2>Related Books</h2>
            <p>You might also like</p>
        </div>
        <div class="books-grid" id="relatedBooks"></div>
    </div>
</div>

<script>
const bookId = <?php echo $bookId; ?>;

// Load book details
async function loadBookDetails() {
    try {
        const response = await fetch(`${API_ENDPOINTS.books}?id=${bookId}`);
        const data = await response.json();
        
        const container = document.getElementById('bookDetails');
        
        if (data.success && data.data) {
            const book = data.data;
            
            // Update breadcrumb
            document.getElementById('breadcrumbTitle').textContent = book.title;
            
            container.innerHTML = `
                <div class="book-details">
                    <div>
                        <img src="${book.image_url || 'https://via.placeholder.com/400x600?text=No+Image'}" 
                             alt="${book.title}" 
                             class="book-details-image"
                             onerror="this.src='https://via.placeholder.com/400x600?text=No+Image'">
                    </div>
                    <div class="book-details-info">
                        <h1>${book.title}</h1>
                        <div class="book-details-meta">
                            <p><strong>Author:</strong> ${book.author}</p>
                            <p><strong>Category:</strong> ${book.category_name || 'Uncategorized'}</p>
                            <p><strong>ISBN:</strong> ${book.isbn || 'N/A'}</p>
                            <p><strong>Rating:</strong> 
                                <span class="stars">${generateStars(book.rating)}</span>
                                ${book.rating}/5.0
                            </p>
                            <p><strong>Availability:</strong> ${getStockBadge(book.stock_quantity)}</p>
                        </div>
                        <div class="book-details-price">${formatPrice(book.price)}</div>
                        <div class="book-details-description">
                            <h3>Description</h3>
                            <p>${book.description || 'No description available.'}</p>
                        </div>
                        <div class="book-details-actions">
                            <button class="btn btn-success" 
                                    onclick="addToCart(${book.id})"
                                    ${book.stock_quantity === 0 ? 'disabled' : ''}>
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button class="btn btn-outline" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Load related books from the same category
            if (book.category_id) {
                loadRelatedBooks(book.category_id, book.id);
            }
        } else {
            container.innerHTML = '<div class="empty-state"><h3>Book not found</h3></div>';
        }
    } catch (error) {
        console.error('Error loading book details:', error);
        document.getElementById('bookDetails').innerHTML = '<p>Error loading book details</p>';
    }
}

// Load related books
async function loadRelatedBooks(categoryId, excludeBookId) {
    try {
        const response = await fetch(`${API_ENDPOINTS.books}?category=${categoryId}&limit=4`);
        const data = await response.json();
        
        const container = document.getElementById('relatedBooks');
        
        if (data.success && data.data.length > 0) {
            // Filter out current book
            const books = data.data.filter(book => book.id !== excludeBookId);
            
            if (books.length > 0) {
                container.innerHTML = books.map(book => `
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
            }
        }
    } catch (error) {
        console.error('Error loading related books:', error);
    }
}

// Load on page load
document.addEventListener('DOMContentLoaded', function() {
    loadBookDetails();
});
</script>

<?php
$extraScripts = [];
include '../includes/footer.php';
?>
