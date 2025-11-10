/**
 * Cart API Routes
 * Handles session-based shopping cart operations
 */
const express = require('express');
const router = express.Router();
const storage = require('../models/storage');

/**
 * Get session ID from request header or create new one
 */
function getSessionId(req) {
    return req.headers['x-session-id'] || 'default-session';
}

/**
 * GET /api/cart
 * Get cart contents for current session
 */
router.get('/', (req, res) => {
    try {
        const sessionId = getSessionId(req);
        const cart = storage.getCart(sessionId);
        const total = storage.getCartTotal(sessionId);
        const count = storage.getCartCount(sessionId);
        
        res.json({
            success: true,
            data: {
                items: cart,
                total: total,
                count: count
            },
            message: 'Cart retrieved successfully'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error retrieving cart: ' + error.message
        });
    }
});

/**
 * POST /api/cart/add
 * Add item to cart
 * Body: { bookId, quantity }
 */
router.post('/add', (req, res) => {
    try {
        const sessionId = getSessionId(req);
        const { bookId, quantity } = req.body;
        
        if (!bookId) {
            return res.status(400).json({
                success: false,
                data: null,
                message: 'Missing required field: bookId'
            });
        }
        
        const qty = parseInt(quantity) || 1;
        const result = storage.addToCart(sessionId, parseInt(bookId), qty);
        
        if (!result.success) {
            return res.status(400).json({
                success: false,
                data: null,
                message: result.message
            });
        }
        
        const total = storage.getCartTotal(sessionId);
        const count = storage.getCartCount(sessionId);
        
        res.json({
            success: true,
            data: {
                items: result.cart,
                total: total,
                count: count
            },
            message: result.message
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error adding to cart: ' + error.message
        });
    }
});

/**
 * PUT /api/cart/update
 * Update cart item quantity
 * Body: { bookId, quantity }
 */
router.put('/update', (req, res) => {
    try {
        const sessionId = getSessionId(req);
        const { bookId, quantity } = req.body;
        
        if (!bookId || quantity === undefined) {
            return res.status(400).json({
                success: false,
                data: null,
                message: 'Missing required fields: bookId, quantity'
            });
        }
        
        const result = storage.updateCartItem(sessionId, parseInt(bookId), parseInt(quantity));
        
        if (!result.success) {
            return res.status(400).json({
                success: false,
                data: null,
                message: result.message
            });
        }
        
        const cart = storage.getCart(sessionId);
        const total = storage.getCartTotal(sessionId);
        const count = storage.getCartCount(sessionId);
        
        res.json({
            success: true,
            data: {
                items: cart,
                total: total,
                count: count
            },
            message: result.message
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error updating cart: ' + error.message
        });
    }
});

/**
 * DELETE /api/cart/remove/:bookId
 * Remove item from cart
 */
router.delete('/remove/:bookId', (req, res) => {
    try {
        const sessionId = getSessionId(req);
        const bookId = parseInt(req.params.bookId);
        
        const result = storage.removeFromCart(sessionId, bookId);
        
        if (!result.success) {
            return res.status(404).json({
                success: false,
                data: null,
                message: result.message
            });
        }
        
        const cart = storage.getCart(sessionId);
        const total = storage.getCartTotal(sessionId);
        const count = storage.getCartCount(sessionId);
        
        res.json({
            success: true,
            data: {
                items: cart,
                total: total,
                count: count
            },
            message: result.message
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error removing from cart: ' + error.message
        });
    }
});

/**
 * DELETE /api/cart/clear
 * Clear entire cart
 */
router.delete('/clear', (req, res) => {
    try {
        const sessionId = getSessionId(req);
        const result = storage.clearCart(sessionId);
        
        res.json({
            success: true,
            data: {
                items: [],
                total: 0,
                count: 0
            },
            message: result.message
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error clearing cart: ' + error.message
        });
    }
});

module.exports = router;
