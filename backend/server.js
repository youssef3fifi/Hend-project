/**
 * BookStore Backend Server
 * Node.js + Express API server with in-memory storage
 */
const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const path = require('path');

// Import routes
const booksRouter = require('./routes/books');
const cartRouter = require('./routes/cart');
const categoriesRouter = require('./routes/categories');

// Initialize Express app
const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Logging middleware
app.use((req, res, next) => {
    console.log(`${new Date().toISOString()} - ${req.method} ${req.url}`);
    next();
});

// Serve static frontend files
app.use(express.static(path.join(__dirname, '../frontend')));

// API Routes
app.use('/api/books', booksRouter);
app.use('/api/cart', cartRouter);
app.use('/api/categories', categoriesRouter);

// API health check
app.get('/api/health', (req, res) => {
    res.json({
        success: true,
        data: {
            status: 'healthy',
            timestamp: new Date().toISOString(),
            uptime: process.uptime()
        },
        message: 'Server is running'
    });
});

// Catch-all route - serve index.html for SPA
app.get('*', (req, res) => {
    res.sendFile(path.join(__dirname, '../frontend/index.html'));
});

// Error handling middleware
app.use((err, req, res, next) => {
    console.error('Error:', err);
    res.status(500).json({
        success: false,
        data: null,
        message: 'Internal server error: ' + err.message
    });
});

// Start server
app.listen(PORT, () => {
    console.log('========================================');
    console.log('   📚 BookStore Backend Server');
    console.log('========================================');
    console.log(`   Server running on port ${PORT}`);
    console.log(`   Local:    http://localhost:${PORT}`);
    console.log(`   API:      http://localhost:${PORT}/api`);
    console.log(`   Frontend: http://localhost:${PORT}`);
    console.log('========================================');
    console.log('   Press Ctrl+C to stop the server');
    console.log('========================================\n');
});

// Handle graceful shutdown
process.on('SIGINT', () => {
    console.log('\n\nShutting down server...');
    process.exit(0);
});

process.on('SIGTERM', () => {
    console.log('\n\nShutting down server...');
    process.exit(0);
});
