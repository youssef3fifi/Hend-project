// In-Memory Storage - البيانات في الذاكرة
class BookStoreStorage {
    constructor() {
        // Books Array - مخزون الكتب
        this.books = [
            {
                id: 1,
                title: "The Great Gatsby",
                author: "F. Scott Fitzgerald",
                price: 12.99,
                category: "Fiction",
                isbn: "978-0-7432-7356-5",
                stock: 15,
                description: "A classic American novel set in the Jazz Age...",
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
                description: "A gripping tale of racial injustice...",
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
                description: "A dystopian social science fiction novel...",
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
                description: "A romantic novel of manners...",
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
                description: "A fantasy novel and children's book...",
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
                description: "The first novel in the Harry Potter series...",
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
                description: "A story about teenage rebellion...",
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
                description: "An epic high-fantasy novel...",
                image: "https://covers.openlibrary.org/b/id/8482014-L.jpg",
                rating: 5.0
            }
        ];

        // Categories
        this.categories = [
            { id: 1, name: "Fiction" },
            { id: 2, name: "Science Fiction" },
            { id: 3, name: "Fantasy" },
            { id: 4, name: "Romance" },
            { id: 5, name: "Mystery" },
            { id: 6, name: "Non-Fiction" }
        ];

        // Cart Items - Shopping Cart
        this.cart = [];

        // Load cart from localStorage if exists
        this.loadCart();
    }

    // ============ BOOKS METHODS ============
    
    getAllBooks() {
        return this.books;
    }

    getBookById(id) {
        return this.books.find(book => book.id === parseInt(id));
    }

    searchBooks(query) {
        const lowerQuery = query.toLowerCase();
        return this.books.filter(book => 
            book.title.toLowerCase().includes(lowerQuery) ||
            book.author.toLowerCase().includes(lowerQuery) ||
            book.category.toLowerCase().includes(lowerQuery)
        );
    }

    filterByCategory(category) {
        if (!category || category === 'all') return this.books;
        return this.books.filter(book => book.category === category);
    }

    addBook(bookData) {
        const newBook = {
            id: this.books.length > 0 ? Math.max(...this.books.map(b => b.id)) + 1 : 1,
            ...bookData,
            rating: 0
        };
        this.books.push(newBook);
        return newBook;
    }

    updateBook(id, bookData) {
        const index = this.books.findIndex(book => book.id === parseInt(id));
        if (index !== -1) {
            this.books[index] = { ...this.books[index], ...bookData };
            return this.books[index];
        }
        return null;
    }

    deleteBook(id) {
        const index = this.books.findIndex(book => book.id === parseInt(id));
        if (index !== -1) {
            this.books.splice(index, 1);
            return true;
        }
        return false;
    }

    // ============ CART METHODS ============
    
    getCart() {
        return this.cart;
    }

    addToCart(bookId, quantity = 1) {
        const book = this.getBookById(bookId);
        if (!book || book.stock < quantity) {
            return { success: false, message: 'Book not available or insufficient stock' };
        }

        const existingItem = this.cart.find(item => item.bookId === bookId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.push({
                bookId: bookId,
                quantity: quantity,
                book: book
            });
        }

        this.saveCart();
        return { success: true, message: 'Added to cart successfully' };
    }

    updateCartItem(bookId, quantity) {
        const item = this.cart.find(item => item.bookId === bookId);
        if (item) {
            if (quantity <= 0) {
                this.removeFromCart(bookId);
            } else {
                item.quantity = quantity;
                this.saveCart();
            }
            return true;
        }
        return false;
    }

    removeFromCart(bookId) {
        const index = this.cart.findIndex(item => item.bookId === bookId);
        if (index !== -1) {
            this.cart.splice(index, 1);
            this.saveCart();
            return true;
        }
        return false;
    }

    clearCart() {
        this.cart = [];
        this.saveCart();
    }

    getCartTotal() {
        return this.cart.reduce((total, item) => {
            return total + (item.book.price * item.quantity);
        }, 0);
    }

    getCartCount() {
        return this.cart.reduce((count, item) => count + item.quantity, 0);
    }

    // ============ CATEGORIES METHODS ============
    
    getAllCategories() {
        return this.categories;
    }

    // ============ PERSISTENCE (LocalStorage) ============
    
    saveCart() {
        localStorage.setItem('bookstore_cart', JSON.stringify(this.cart));
    }

    loadCart() {
        const savedCart = localStorage.getItem('bookstore_cart');
        if (savedCart) {
            this.cart = JSON.parse(savedCart);
            // Update book references
            this.cart.forEach(item => {
                item.book = this.getBookById(item.bookId);
            });
        }
    }
}

// Create global storage instance
const storage = new BookStoreStorage();
