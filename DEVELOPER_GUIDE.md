# Developer Guide - BookStore Application

## Quick Start for Development

### Local Development Setup

1. **Install XAMPP/WAMP/MAMP**
   - Download from respective websites
   - Start Apache and MySQL services

2. **Clone Repository**
   ```bash
   cd /path/to/htdocs  # or /path/to/www
   git clone https://github.com/youssef3fifi/Hend-project.git bookstore
   cd bookstore
   ```

3. **Configure Database**
   - Edit `config/database.php` with your local credentials
   - Default: localhost, root, no password

4. **Import Database**
   ```bash
   mysql -u root -p bookstore_db < sql/setup.sql
   # or use phpMyAdmin to import sql/setup.sql
   ```

5. **Access Application**
   - Open browser: `http://localhost/bookstore`

## Code Structure

### Backend (PHP)

#### API Endpoints
All API endpoints return JSON responses:

```json
{
  "success": true/false,
  "data": {},  // or []
  "error": "error message"  // if success is false
}
```

**Books API** (`api/books.php`)
- GET: List books with pagination/filters
- POST: Create book (admin only)
- PUT: Update book (admin only)
- DELETE: Delete book (admin only)

**Categories API** (`api/categories.php`)
- GET: List all categories

**Cart API** (`api/cart.php`)
- GET: Get cart items
- POST: Add to cart
- PUT: Update quantity
- DELETE: Remove item

**Auth API** (`api/auth.php`)
- POST: Login/logout

#### Configuration Files
- `config/database.php`: Database connection
- `config/config.php`: App settings, security functions

#### Shared Templates
- `includes/header.php`: Header with navigation
- `includes/footer.php`: Footer with scripts

### Frontend (HTML/CSS/JS)

#### JavaScript Modules
- `assets/js/config.js`: API endpoint configuration
- `assets/js/main.js`: Common functions (cart, toast, etc.)

#### Styling
- `assets/css/style.css`: All styles with CSS variables

#### Pages
- `index.php`: Home page
- `pages/shop.php`: Product catalog
- `pages/book-details.php`: Single book view
- `pages/cart.php`: Shopping cart
- `pages/admin/login.php`: Admin login
- `pages/admin/dashboard.php`: Admin panel

## Database Schema

### Books Table
```sql
id, title, author, description, price, category_id, 
isbn, stock_quantity, image_url, rating, created_at, updated_at
```

### Categories Table
```sql
id, name, description, created_at
```

### Cart Items Table
```sql
id, session_id, book_id, quantity, added_at
```

### Users Table
```sql
id, username, email, password_hash, is_admin, created_at
```

## Security Best Practices

### Implemented Security Features

1. **SQL Injection Prevention**
   ```php
   // Always use prepared statements
   $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
   $stmt->bind_param("i", $id);
   ```

2. **XSS Prevention**
   ```php
   // Sanitize all outputs
   echo sanitizeOutput($data);
   ```

3. **CSRF Protection**
   ```php
   // Token generated in config.php
   getCsrfToken();
   verifyCsrfToken($token);
   ```

4. **Password Hashing**
   ```php
   // Use bcrypt
   password_hash($password, PASSWORD_DEFAULT);
   password_verify($password, $hash);
   ```

### Adding New Features

#### Add New API Endpoint

1. Create new file in `api/` directory
2. Include required files:
   ```php
   require_once '../config/database.php';
   require_once '../config/config.php';
   ```
3. Set headers:
   ```php
   header('Content-Type: application/json');
   header('Access-Control-Allow-Origin: *');
   ```
4. Handle different HTTP methods
5. Return JSON response

#### Add New Page

1. Create PHP file in `pages/` directory
2. Include header:
   ```php
   $pageTitle = 'Page Name';
   include '../includes/header.php';
   ```
3. Add your content
4. Include footer:
   ```php
   include '../includes/footer.php';
   ```

#### Add New Database Table

1. Add CREATE TABLE to `sql/setup.sql`
2. Add sample data (INSERT statements)
3. Create API endpoint for CRUD operations
4. Update relevant pages

## Common Tasks

### Update Book Price
```sql
UPDATE books SET price = 19.99 WHERE id = 1;
```

### Add New Category
```sql
INSERT INTO categories (name, description) VALUES ('Horror', 'Scary books');
```

### Reset Admin Password
```sql
-- Password: newpassword123
UPDATE users SET password_hash = '$2y$10$...' WHERE username = 'admin';
```

Or use PHP:
```php
echo password_hash('newpassword123', PASSWORD_DEFAULT);
```

### Clear All Cart Items
```sql
DELETE FROM cart_items;
```

## Debugging

### Enable Error Display
In `config/config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check PHP Errors
```bash
tail -f /var/log/apache2/error.log
```

### Check MySQL Errors
```bash
tail -f /var/log/mysql/error.log
```

### Browser Console
- Open DevTools (F12)
- Check Console tab for JavaScript errors
- Check Network tab for API responses

## Testing

### Manual Testing Checklist

**Public Features:**
- [ ] Home page loads and displays books
- [ ] Categories are clickable
- [ ] Search works
- [ ] Shop page filters work
- [ ] Pagination works
- [ ] Book details page loads
- [ ] Add to cart works
- [ ] Cart displays correctly
- [ ] Update quantity works
- [ ] Remove from cart works
- [ ] Mobile responsive

**Admin Features:**
- [ ] Admin login works
- [ ] Dashboard displays stats
- [ ] Add new book works
- [ ] Edit book works
- [ ] Delete book works
- [ ] Logout works

**Security:**
- [ ] SQL injection attempts fail
- [ ] XSS attempts are sanitized
- [ ] Admin pages require login
- [ ] Passwords are hashed

## Performance Optimization

### Enable Caching
```apache
# In .htaccess (already configured)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
</IfModule>
```

### Optimize Images
- Use appropriate image sizes
- Consider WebP format
- Compress images before upload

### Database Indexing
```sql
-- Indexes already in setup.sql
CREATE INDEX idx_category ON books(category_id);
CREATE INDEX idx_title ON books(title);
```

### PHP OPcache
Enable in php.ini:
```ini
opcache.enable=1
opcache.memory_consumption=128
```

## Deployment Checklist

Before deploying to production:

- [ ] Update database credentials
- [ ] Disable error display
- [ ] Change admin password
- [ ] Enable HTTPS (SSL certificate)
- [ ] Set up automatic backups
- [ ] Configure firewall
- [ ] Set file permissions correctly
- [ ] Test all features
- [ ] Set up monitoring/logs
- [ ] Create backup script

## Troubleshooting

### "Database connection failed"
- Check MySQL is running
- Verify credentials in `config/database.php`
- Check MySQL user has proper permissions

### "404 Not Found" for API calls
- Check .htaccess is present
- Verify mod_rewrite is enabled
- Check Apache configuration

### "Cannot add to cart"
- Check session is started
- Verify cart_items table exists
- Check book_id is valid

### Images not loading
- Check image URLs are valid
- Verify image files exist
- Check file permissions

## Contributing

1. Fork the repository
2. Create feature branch
3. Make changes
4. Test thoroughly
5. Submit pull request

## Resources

- PHP Manual: https://www.php.net/manual/
- MySQL Documentation: https://dev.mysql.com/doc/
- MDN Web Docs: https://developer.mozilla.org/
- Font Awesome Icons: https://fontawesome.com/icons

## Support

For issues or questions:
- Check error logs
- Review this guide
- Check README.md for deployment help
