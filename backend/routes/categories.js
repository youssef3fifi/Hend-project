/**
 * Categories API Routes
 * Handles category-related endpoints
 */
const express = require('express');
const router = express.Router();
const storage = require('../models/storage');

/**
 * GET /api/categories
 * Get all categories
 */
router.get('/', (req, res) => {
    try {
        const categories = storage.getAllCategories();
        
        res.json({
            success: true,
            data: categories,
            message: 'Categories retrieved successfully'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error retrieving categories: ' + error.message
        });
    }
});

/**
 * GET /api/categories/:id
 * Get a specific category by ID
 */
router.get('/:id', (req, res) => {
    try {
        const categoryId = parseInt(req.params.id);
        const category = storage.getCategoryById(categoryId);
        
        if (!category) {
            return res.status(404).json({
                success: false,
                data: null,
                message: 'Category not found'
            });
        }
        
        res.json({
            success: true,
            data: category,
            message: 'Category retrieved successfully'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            data: null,
            message: 'Error retrieving category: ' + error.message
        });
    }
});

module.exports = router;
