/**
 * Books API Routes
 * Handles all book-related endpoints
 */
const express = require('express');
const router = express.Router();
const storage = require('../models/storage');

/**
 * GET /api/books
 * Get all books with optional filters
 * Query params: search, category
 */
router.get('/', (req, res) => {
    try {
        const filters = {
            search: req.query.search,
            category: req.query.category
        };
        
        const books = storage.getAllBooks(filters);
        
        res.json({
            success: true,
            data: books,
            message: 'Books retrieved successfully'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error retrieving books: ' + error.message
        });
    }
});

/**
 * GET /api/books/:id
 * Get a specific book by ID
 */
router.get('/:id', (req, res) => {
    try {
        const bookId = parseInt(req.params.id);
        const book = storage.getBookById(bookId);
        
        if (!book) {
            return res.status(404).json({
                success: false,
                data: null,
                message: 'Book not found'
            });
        }
        
        res.json({
            success: true,
            data: book,
            message: 'Book retrieved successfully'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error retrieving book: ' + error.message
        });
    }
});

/**
 * POST /api/books
 * Create a new book (Admin)
 */
router.post('/', (req, res) => {
    try {
        const { title, author, price, category, isbn, stock, description, image } = req.body;
        
        // Validate required fields
        if (!title || !author || !price || !category) {
            return res.status(400).json({
                success: false,
                data: null,
                message: 'Missing required fields: title, author, price, category'
            });
        }
        
        const bookData = {
            title,
            author,
            price: parseFloat(price),
            category,
            isbn: isbn || '',
            stock: parseInt(stock) || 0,
            description: description || '',
            image: image || 'https://via.placeholder.com/300x400',
            rating: 0
        };
        
        const newBook = storage.addBook(bookData);
        
        res.status(201).json({
            success: true,
            data: newBook,
            message: 'Book created successfully'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error creating book: ' + error.message
        });
    }
});

/**
 * PUT /api/books/:id
 * Update a book (Admin)
 */
router.put('/:id', (req, res) => {
    try {
        const bookId = parseInt(req.params.id);
        const { title, author, price, category, isbn, stock, description, image, rating } = req.body;
        
        const bookData = {};
        if (title !== undefined) bookData.title = title;
        if (author !== undefined) bookData.author = author;
        if (price !== undefined) bookData.price = parseFloat(price);
        if (category !== undefined) bookData.category = category;
        if (isbn !== undefined) bookData.isbn = isbn;
        if (stock !== undefined) bookData.stock = parseInt(stock);
        if (description !== undefined) bookData.description = description;
        if (image !== undefined) bookData.image = image;
        if (rating !== undefined) bookData.rating = parseFloat(rating);
        
        const updatedBook = storage.updateBook(bookId, bookData);
        
        if (!updatedBook) {
            return res.status(404).json({
                success: false,
                data: null,
                message: 'Book not found'
            });
        }
        
        res.json({
            success: true,
            data: updatedBook,
            message: 'Book updated successfully'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error updating book: ' + error.message
        });
    }
});

/**
 * DELETE /api/books/:id
 * Delete a book (Admin)
 */
router.delete('/:id', (req, res) => {
    try {
        const bookId = parseInt(req.params.id);
        const success = storage.deleteBook(bookId);
        
        if (!success) {
            return res.status(404).json({
                success: false,
                data: null,
                message: 'Book not found'
            });
        }
        
        res.json({
            success: true,
            data: null,
            message: 'Book deleted successfully'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error deleting book: ' + error.message
        });
    }
});

module.exports = router;
