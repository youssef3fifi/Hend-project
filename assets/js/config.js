/**
 * Application Configuration
 * Dynamic API base URL for EC2 deployment
 */

const API_BASE_URL = window.location.origin;
const API_ENDPOINTS = {
    books: `${API_BASE_URL}/api/books.php`,
    categories: `${API_BASE_URL}/api/categories.php`,
    cart: `${API_BASE_URL}/api/cart.php`,
    auth: `${API_BASE_URL}/api/auth.php`
};
