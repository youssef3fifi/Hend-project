<?php
session_start();
require_once '../../config/config.php';

// Check if admin is logged in
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Admin Dashboard';
include '../../includes/header.php';
?>

<div class="container">
    <div class="admin-header">
        <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        <button class="btn btn-danger" onclick="handleLogout()">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>
    
    <!-- Statistics -->
    <div class="admin-stats" id="statsContainer">
        <div class="stat-card">
            <i class="fas fa-book"></i>
            <h3 id="totalBooks">0</h3>
            <p>Total Books</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-list"></i>
            <h3 id="totalCategories">0</h3>
            <p>Categories</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-box"></i>
            <h3 id="lowStock">0</h3>
            <p>Low Stock Items</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-exclamation-triangle"></i>
            <h3 id="outOfStock">0</h3>
            <p>Out of Stock</p>
        </div>
    </div>
    
    <!-- Book Management -->
    <div class="admin-header">
        <h2>Manage Books</h2>
        <button class="btn btn-primary" onclick="showAddBookModal()">
            <i class="fas fa-plus"></i> Add New Book
        </button>
    </div>
    
    <div class="admin-table">
        <table id="booksTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="booksTableBody">
                <tr>
                    <td colspan="7" style="text-align: center;">
                        <div class="spinner" style="margin: 1rem auto;"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Book Modal -->
<div class="modal" id="bookModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Book</h2>
            <button class="modal-close" onclick="closeBookModal()">&times;</button>
        </div>
        <form id="bookForm" onsubmit="handleBookSubmit(event)">
            <input type="hidden" id="bookId">
            
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" required>
            </div>
            
            <div class="form-group">
                <label for="author">Author *</label>
                <input type="text" id="author" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description"></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price *</label>
                <input type="number" id="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id">
                    <option value="">No Category</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="isbn">ISBN</label>
                <input type="text" id="isbn">
            </div>
            
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" id="stock_quantity" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="url" id="image_url">
            </div>
            
            <div class="form-group">
                <label for="rating">Rating (0-5)</label>
                <input type="number" id="rating" min="0" max="5" step="0.1" value="0">
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeBookModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let allCategories = [];
let currentEditingBookId = null;

// Load statistics
async function loadStats() {
    try {
        const response = await fetch(API_ENDPOINTS.books);
        const data = await response.json();
        
        if (data.success) {
            const books = data.data;
            document.getElementById('totalBooks').textContent = books.length;
            
            const lowStock = books.filter(b => b.stock_quantity > 0 && b.stock_quantity < 10).length;
            document.getElementById('lowStock').textContent = lowStock;
            
            const outOfStock = books.filter(b => b.stock_quantity === 0).length;
            document.getElementById('outOfStock').textContent = outOfStock;
        }
        
        const catResponse = await fetch(API_ENDPOINTS.categories);
        const catData = await catResponse.json();
        if (catData.success) {
            document.getElementById('totalCategories').textContent = catData.data.length;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Load books
async function loadBooks() {
    try {
        const response = await fetch(`${API_ENDPOINTS.books}?limit=100`);
        const data = await response.json();
        
        const tbody = document.getElementById('booksTableBody');
        
        if (data.success && data.data.length > 0) {
            tbody.innerHTML = data.data.map(book => `
                <tr>
                    <td>${book.id}</td>
                    <td>${book.title}</td>
                    <td>${book.author}</td>
                    <td>${book.category_name || 'N/A'}</td>
                    <td>${formatPrice(book.price)}</td>
                    <td>${book.stock_quantity}</td>
                    <td class="actions">
                        <button class="btn btn-primary" onclick="editBook(${book.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteBook(${book.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No books found</td></tr>';
        }
    } catch (error) {
        console.error('Error loading books:', error);
        document.getElementById('booksTableBody').innerHTML = '<tr><td colspan="7">Error loading books</td></tr>';
    }
}

// Load categories for dropdown
async function loadCategories() {
    try {
        const response = await fetch(API_ENDPOINTS.categories);
        const data = await response.json();
        
        if (data.success) {
            allCategories = data.data;
            const select = document.getElementById('category_id');
            select.innerHTML = '<option value="">No Category</option>';
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

// Show add book modal
function showAddBookModal() {
    currentEditingBookId = null;
    document.getElementById('modalTitle').textContent = 'Add New Book';
    document.getElementById('bookForm').reset();
    document.getElementById('bookId').value = '';
    document.getElementById('bookModal').classList.add('show');
}

// Close book modal
function closeBookModal() {
    document.getElementById('bookModal').classList.remove('show');
    document.getElementById('bookForm').reset();
}

// Edit book
async function editBook(id) {
    try {
        const response = await fetch(`${API_ENDPOINTS.books}?id=${id}`);
        const data = await response.json();
        
        if (data.success && data.data) {
            const book = data.data;
            currentEditingBookId = id;
            
            document.getElementById('modalTitle').textContent = 'Edit Book';
            document.getElementById('bookId').value = book.id;
            document.getElementById('title').value = book.title;
            document.getElementById('author').value = book.author;
            document.getElementById('description').value = book.description || '';
            document.getElementById('price').value = book.price;
            document.getElementById('category_id').value = book.category_id || '';
            document.getElementById('isbn').value = book.isbn || '';
            document.getElementById('stock_quantity').value = book.stock_quantity;
            document.getElementById('image_url').value = book.image_url || '';
            document.getElementById('rating').value = book.rating;
            
            document.getElementById('bookModal').classList.add('show');
        }
    } catch (error) {
        console.error('Error loading book:', error);
        showToast('Error loading book', 'error');
    }
}

// Handle book form submit
async function handleBookSubmit(event) {
    event.preventDefault();
    
    const bookData = {
        title: document.getElementById('title').value,
        author: document.getElementById('author').value,
        description: document.getElementById('description').value,
        price: parseFloat(document.getElementById('price').value),
        category_id: document.getElementById('category_id').value || null,
        isbn: document.getElementById('isbn').value,
        stock_quantity: parseInt(document.getElementById('stock_quantity').value),
        image_url: document.getElementById('image_url').value,
        rating: parseFloat(document.getElementById('rating').value)
    };
    
    const bookId = document.getElementById('bookId').value;
    const isEdit = bookId !== '';
    
    if (isEdit) {
        bookData.id = parseInt(bookId);
    }
    
    try {
        const response = await fetch(API_ENDPOINTS.books, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(bookData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(isEdit ? 'Book updated successfully' : 'Book added successfully', 'success');
            closeBookModal();
            loadBooks();
            loadStats();
        } else {
            showToast(data.error || 'Failed to save book', 'error');
        }
    } catch (error) {
        console.error('Error saving book:', error);
        showToast('Failed to save book', 'error');
    }
}

// Delete book
async function deleteBook(id) {
    if (!confirm('Are you sure you want to delete this book?')) return;
    
    try {
        const response = await fetch(`${API_ENDPOINTS.books}?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Book deleted successfully', 'success');
            loadBooks();
            loadStats();
        } else {
            showToast(data.error || 'Failed to delete book', 'error');
        }
    } catch (error) {
        console.error('Error deleting book:', error);
        showToast('Failed to delete book', 'error');
    }
}

// Handle logout
async function handleLogout() {
    try {
        const response = await fetch(API_ENDPOINTS.auth, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'logout'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Logged out successfully', 'success');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1000);
        }
    } catch (error) {
        console.error('Logout error:', error);
        showToast('Logout failed', 'error');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadBooks();
    loadStats();
});
</script>

<?php
include '../../includes/footer.php';
?>
