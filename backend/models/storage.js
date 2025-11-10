/**
 * In-Memory Storage with Session Support
 * All data stored in memory - resets on server restart
 */
class BookStoreStorage {
    constructor() {
        // Books Array - Pre-loaded with 8 books
        this.books = [
            {
                id: 1,
                title: "The Great Gatsby",
                author: "F. Scott Fitzgerald",
                price: 12.99,
                category: "Fiction",
                isbn: "978-0-7432-7356-5",
                stock: 15,
                description: "A classic American novel set in the Jazz Age that explores themes of wealth, love, and the American Dream.",
                image: "https://covers.openlibrary.org/b/id/7222246-L.jpg",
                rating: 4.5
            },
            {
                id: 2,
                title: "To Kill a Mockingbird",
                author: "Harper Lee",
                price: 14.99,
                category: "Fiction",
                isbn: "978-0-06-112008-4",
                stock: 20,
                description: "A gripping tale of racial injustice and childhood innocence in the American South.",
                image: "https://covers.openlibrary.org/b/id/8228691-L.jpg",
                rating: 4.8
            },
            {
                id: 3,
                title: "1984",
                author: "George Orwell",
                price: 13.99,
                category: "Science Fiction",
                isbn: "978-0-452-28423-4",
                stock: 25,
                description: "A dystopian social science fiction novel and cautionary tale about totalitarianism.",
                image: "https://covers.openlibrary.org/b/id/7222246-L.jpg",
                rating: 4.7
            },
            {
                id: 4,
                title: "Pride and Prejudice",
                author: "Jane Austen",
                price: 11.99,
                category: "Romance",
                isbn: "978-0-14-143951-8",
                stock: 18,
                description: "A romantic novel of manners that explores themes of love, reputation, and class.",
                image: "https://covers.openlibrary.org/b/id/8235655-L.jpg",
                rating: 4.6
            },
            {
                id: 5,
                title: "The Hobbit",
                author: "J.R.R. Tolkien",
                price: 15.99,
                category: "Fantasy",
                isbn: "978-0-547-92822-7",
                stock: 22,
                description: "A fantasy novel and children's book about the quest of home-loving hobbit Bilbo Baggins.",
                image: "https://covers.openlibrary.org/b/id/8482014-L.jpg",
                rating: 4.9
            },
            {
                id: 6,
                title: "Harry Potter and the Philosopher's Stone",
                author: "J.K. Rowling",
                price: 16.99,
                category: "Fantasy",
                isbn: "978-0-7475-3269-9",
                stock: 30,
                description: "The first novel in the Harry Potter series following a young wizard's journey.",
                image: "https://covers.openlibrary.org/b/id/10521270-L.jpg",
                rating: 4.9
            },
            {
                id: 7,
                title: "The Catcher in the Rye",
                author: "J.D. Salinger",
                price: 12.99,
                category: "Fiction",
                isbn: "978-0-316-76948-0",
                stock: 12,
                description: "A story about teenage rebellion and alienation narrated by Holden Caulfield.",
                image: "https://covers.openlibrary.org/b/id/8228522-L.jpg",
                rating: 4.3
            },
            {
                id: 8,
                title: "The Lord of the Rings",
                author: "J.R.R. Tolkien",
                price: 24.99,
                category: "Fantasy",
                isbn: "978-0-544-00341-5",
                stock: 10,
                description: "An epic high-fantasy novel about the quest to destroy the One Ring.",
                image: "https://covers.openlibrary.org/b/id/8482014-L.jpg",
                rating: 5.0
            }
        ];

        // Categories Array - 6 pre-defined categories
        this.categories = [
            { id: 1, name: "Fiction" },
            { id: 2, name: "Science Fiction" },
            { id: 3, name: "Fantasy" },
            { id: 4, name: "Romance" },
            { id: 5, name: "Mystery" },
            { id: 6, name: "Non-Fiction" }
        ];

        // Session-based carts - Map of sessionId -> cart items
        this.carts = new Map();
    }

    // ============ BOOKS METHODS ============
    
    /**
     * Get all books with optional filters
     * @param {Object} filters - Optional filters (search, category)
     * @returns {Array} Filtered books
     */
    getAllBooks(filters = {}) {
        let books = [...this.books];
        
        // Apply search filter
        if (filters.search) {
            const lowerQuery = filters.search.toLowerCase();
            books = books.filter(book => 
                book.title.toLowerCase().includes(lowerQuery) ||
                book.author.toLowerCase().includes(lowerQuery) ||
                book.category.toLowerCase().includes(lowerQuery)
            );
        }
        
        // Apply category filter
        if (filters.category && filters.category !== 'all') {
            books = books.filter(book => book.category === filters.category);
        }
        
        return books;
    }

    /**
     * Get book by ID
     * @param {number} id - Book ID
     * @returns {Object|null} Book object or null
     */
    getBookById(id) {
        return this.books.find(book => book.id === parseInt(id));
    }

    /**
     * Add a new book
     * @param {Object} bookData - Book data
     * @returns {Object} Created book
     */
    addBook(bookData) {
        const newBook = {
            id: this.books.length > 0 ? Math.max(...this.books.map(b => b.id)) + 1 : 1,
            ...bookData,
            rating: bookData.rating || 0
        };
        this.books.push(newBook);
        return newBook;
    }

    /**
     * Update a book
     * @param {number} id - Book ID
     * @param {Object} bookData - Updated book data
     * @returns {Object|null} Updated book or null
     */
    updateBook(id, bookData) {
        const index = this.books.findIndex(book => book.id === parseInt(id));
        if (index !== -1) {
            this.books[index] = { ...this.books[index], ...bookData };
            return this.books[index];
        }
        return null;
    }

    /**
     * Delete a book
     * @param {number} id - Book ID
     * @returns {boolean} Success status
     */
    deleteBook(id) {
        const index = this.books.findIndex(book => book.id === parseInt(id));
        if (index !== -1) {
            this.books.splice(index, 1);
            return true;
        }
        return false;
    }

    // ============ CART METHODS ============
    
    /**
     * Get cart for session
     * @param {string} sessionId - Session ID
     * @returns {Array} Cart items
     */
    getCart(sessionId) {
        if (!this.carts.has(sessionId)) {
            this.carts.set(sessionId, []);
        }
        return this.carts.get(sessionId);
    }

    /**
     * Add item to cart
     * @param {string} sessionId - Session ID
     * @param {number} bookId - Book ID
     * @param {number} quantity - Quantity to add
     * @returns {Object} Result object
     */
    addToCart(sessionId, bookId, quantity = 1) {
        const book = this.getBookById(bookId);
        if (!book) {
            return { success: false, message: 'Book not found' };
        }
        
        if (book.stock < quantity) {
            return { success: false, message: 'Insufficient stock' };
        }

        const cart = this.getCart(sessionId);
        const existingItem = cart.find(item => item.bookId === bookId);
        
        if (existingItem) {
            const newQuantity = existingItem.quantity + quantity;
            if (book.stock < newQuantity) {
                return { success: false, message: 'Insufficient stock' };
            }
            existingItem.quantity = newQuantity;
        } else {
            cart.push({
                bookId: bookId,
                quantity: quantity,
                book: book
            });
        }

        return { success: true, message: 'Added to cart successfully', cart };
    }

    /**
     * Update cart item quantity
     * @param {string} sessionId - Session ID
     * @param {number} bookId - Book ID
     * @param {number} quantity - New quantity
     * @returns {Object} Result object
     */
    updateCartItem(sessionId, bookId, quantity) {
        const cart = this.getCart(sessionId);
        const item = cart.find(item => item.bookId === bookId);
        
        if (!item) {
            return { success: false, message: 'Item not found in cart' };
        }
        
        const book = this.getBookById(bookId);
        if (quantity > book.stock) {
            return { success: false, message: 'Insufficient stock' };
        }
        
        if (quantity <= 0) {
            return this.removeFromCart(sessionId, bookId);
        }
        
        item.quantity = quantity;
        return { success: true, message: 'Cart updated successfully', cart };
    }

    /**
     * Remove item from cart
     * @param {string} sessionId - Session ID
     * @param {number} bookId - Book ID
     * @returns {Object} Result object
     */
    removeFromCart(sessionId, bookId) {
        const cart = this.getCart(sessionId);
        const index = cart.findIndex(item => item.bookId === bookId);
        
        if (index !== -1) {
            cart.splice(index, 1);
            return { success: true, message: 'Item removed from cart', cart };
        }
        
        return { success: false, message: 'Item not found in cart' };
    }

    /**
     * Clear cart
     * @param {string} sessionId - Session ID
     * @returns {Object} Result object
     */
    clearCart(sessionId) {
        this.carts.set(sessionId, []);
        return { success: true, message: 'Cart cleared successfully', cart: [] };
    }

    /**
     * Get cart total
     * @param {string} sessionId - Session ID
     * @returns {number} Total price
     */
    getCartTotal(sessionId) {
        const cart = this.getCart(sessionId);
        return cart.reduce((total, item) => {
            return total + (item.book.price * item.quantity);
        }, 0);
    }

    /**
     * Get cart item count
     * @param {string} sessionId - Session ID
     * @returns {number} Total item count
     */
    getCartCount(sessionId) {
        const cart = this.getCart(sessionId);
        return cart.reduce((count, item) => count + item.quantity, 0);
    }

    // ============ CATEGORIES METHODS ============
    
    /**
     * Get all categories
     * @returns {Array} Categories
     */
    getAllCategories() {
        return this.categories;
    }

    /**
     * Get category by ID
     * @param {number} id - Category ID
     * @returns {Object|null} Category object or null
     */
    getCategoryById(id) {
        return this.categories.find(cat => cat.id === parseInt(id));
    }
}

// Export singleton instance
module.exports = new BookStoreStorage();
