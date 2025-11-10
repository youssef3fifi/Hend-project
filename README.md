# BookStore - Professional Full-Stack Web Application

A professional, full-featured online bookstore built with PHP, MySQL, and modern web technologies. Designed for deployment on AWS EC2.

## Features

- **Modern, Responsive Design**: Mobile-first approach with professional UI/UX
- **Complete Book Management**: Browse, search, filter, and purchase books
- **Shopping Cart**: Session-based cart system with quantity management
- **Admin Dashboard**: Full CRUD operations for book inventory management
- **Category System**: Organized book categorization
- **Search & Filter**: Advanced search and filtering capabilities
- **Secure**: SQL injection prevention, XSS protection, CSRF tokens
- **EC2-Ready**: Configured for AWS EC2 deployment with dynamic base URLs

## Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Server**: Apache with mod_rewrite
- **Icons**: Font Awesome 6.4.0

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache 2.4+ with mod_rewrite enabled
- PHP Extensions: mysqli, session

## Installation Instructions

### 1. AWS EC2 Setup

#### Launch EC2 Instance
```bash
# Recommended: Ubuntu 22.04 LTS or Amazon Linux 2
# Instance type: t2.micro or higher
# Security group: Allow HTTP (port 80) and SSH (port 22)
```

#### Connect to EC2
```bash
ssh -i your-key.pem ec2-user@your-ec2-public-ip
# or for Ubuntu
ssh -i your-key.pem ubuntu@your-ec2-public-ip
```

### 2. Install LAMP Stack

#### For Amazon Linux 2:
```bash
# Update system
sudo yum update -y

# Install Apache
sudo yum install httpd -y
sudo systemctl start httpd
sudo systemctl enable httpd

# Install PHP 7.4+
sudo amazon-linux-extras install php7.4 -y
sudo yum install php-mysqli php-json php-mbstring -y

# Install MySQL
sudo yum install mysql-server -y
sudo systemctl start mysqld
sudo systemctl enable mysqld
```

#### For Ubuntu:
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y
sudo systemctl start apache2
sudo systemctl enable apache2

# Install PHP 7.4+
sudo apt install php php-mysqli php-json php-mbstring -y

# Install MySQL
sudo apt install mysql-server -y
sudo systemctl start mysql
sudo systemctl enable mysql
```

### 3. Configure MySQL

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Login to MySQL
sudo mysql -u root -p

# Run the following SQL commands:
```

```sql
-- Create database user
CREATE USER 'bookstore_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON bookstore_db.* TO 'bookstore_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Deploy Application

```bash
# Navigate to web root
cd /var/www/html

# Clone or upload your application
sudo git clone https://github.com/youssef3fifi/Hend-project.git bookstore
cd bookstore

# Set proper permissions
sudo chown -R apache:apache /var/www/html/bookstore  # Amazon Linux
# or
sudo chown -R www-data:www-data /var/www/html/bookstore  # Ubuntu

sudo chmod -R 755 /var/www/html/bookstore
sudo chmod -R 775 /var/www/html/bookstore/assets
```

### 5. Configure Database Connection

```bash
# Edit database configuration
sudo nano config/database.php
```

Update the database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'bookstore_user');
define('DB_PASS', 'your_secure_password');
define('DB_NAME', 'bookstore_db');
```

### 6. Initialize Database

```bash
# Import database schema and sample data
mysql -u bookstore_user -p bookstore_db < sql/setup.sql
```

### 7. Configure Apache

#### Enable mod_rewrite:
```bash
# Amazon Linux
sudo nano /etc/httpd/conf/httpd.conf
# Change "AllowOverride None" to "AllowOverride All" in <Directory "/var/www/html">

# Ubuntu
sudo a2enmod rewrite
```

#### Configure Virtual Host (Optional):
```bash
sudo nano /etc/httpd/conf.d/bookstore.conf  # Amazon Linux
# or
sudo nano /etc/apache2/sites-available/bookstore.conf  # Ubuntu
```

```apache
<VirtualHost *:80>
    ServerAdmin admin@bookstore.com
    DocumentRoot /var/www/html/bookstore
    
    <Directory /var/www/html/bookstore>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/bookstore-error.log
    CustomLog /var/log/httpd/bookstore-access.log combined
</VirtualHost>
```

For Ubuntu, enable the site:
```bash
sudo a2ensite bookstore.conf
```

### 8. Restart Apache

```bash
# Amazon Linux
sudo systemctl restart httpd

# Ubuntu
sudo systemctl restart apache2
```

### 9. Configure Firewall (if applicable)

```bash
# Amazon Linux / RHEL
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload

# Ubuntu (UFW)
sudo ufw allow 80/tcp
sudo ufw allow 22/tcp
sudo ufw enable
```

## Access the Application

Open your browser and navigate to:
```
http://your-ec2-public-ip/
```

Or if you set up a virtual host at the root:
```
http://your-ec2-public-ip/
```

## Default Admin Credentials

- **Username**: admin
- **Password**: admin123

⚠️ **IMPORTANT**: Change these credentials immediately after first login in a production environment!

## Application Structure

```
bookstore/
├── api/                    # API endpoints
│   ├── auth.php           # Authentication
│   ├── books.php          # Books CRUD
│   ├── cart.php           # Shopping cart
│   └── categories.php     # Categories
├── assets/                 # Static assets
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   ├── js/
│   │   ├── config.js      # API configuration
│   │   └── main.js        # Main JavaScript
│   └── images/            # Images
├── config/                 # Configuration files
│   ├── config.php         # App configuration
│   └── database.php       # Database connection
├── includes/               # Shared components
│   ├── header.php         # Header template
│   └── footer.php         # Footer template
├── pages/                  # Application pages
│   ├── admin/
│   │   ├── dashboard.php  # Admin dashboard
│   │   └── login.php      # Admin login
│   ├── book-details.php   # Book details page
│   ├── cart.php           # Shopping cart
│   └── shop.php           # Shop/catalog page
├── sql/
│   └── setup.sql          # Database schema
├── .htaccess              # Apache configuration
├── index.php              # Home page
└── README.md              # This file
```

## Environment Variables

For enhanced security, you can use environment variables:

```bash
# Add to /etc/environment or .env file
export DB_HOST="localhost"
export DB_USER="bookstore_user"
export DB_PASS="your_secure_password"
export DB_NAME="bookstore_db"
```

## Security Features

- ✅ Prepared SQL statements (SQL injection prevention)
- ✅ XSS protection with output sanitization
- ✅ CSRF token protection
- ✅ Secure session handling
- ✅ Input validation (client and server-side)
- ✅ Password hashing (bcrypt)
- ✅ Admin authentication
- ✅ Security headers (.htaccess)

## API Endpoints

### Books API (`/api/books.php`)
- `GET` - List all books (with pagination and filters)
- `GET ?id={id}` - Get single book
- `POST` - Create new book (admin only)
- `PUT` - Update book (admin only)
- `DELETE ?id={id}` - Delete book (admin only)

### Categories API (`/api/categories.php`)
- `GET` - List all categories

### Cart API (`/api/cart.php`)
- `GET` - Get cart items
- `POST` - Add item to cart
- `PUT` - Update cart item quantity
- `DELETE ?book_id={id}` - Remove item from cart
- `DELETE ?clear=true` - Clear cart

### Auth API (`/api/auth.php`)
- `POST {action: 'login'}` - Admin login
- `POST {action: 'logout'}` - Admin logout

## Troubleshooting

### Apache won't start
```bash
# Check Apache error logs
sudo tail -f /var/log/httpd/error_log  # Amazon Linux
sudo tail -f /var/log/apache2/error.log  # Ubuntu
```

### Database connection issues
```bash
# Verify MySQL is running
sudo systemctl status mysqld  # Amazon Linux
sudo systemctl status mysql   # Ubuntu

# Test database connection
mysql -u bookstore_user -p bookstore_db
```

### Permission issues
```bash
# Reset permissions
sudo chown -R apache:apache /var/www/html/bookstore  # Amazon Linux
sudo chown -R www-data:www-data /var/www/html/bookstore  # Ubuntu
sudo chmod -R 755 /var/www/html/bookstore
```

### PHP errors not showing
Edit `config/config.php` and temporarily enable:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

⚠️ Disable this in production!

## Performance Optimization

### Enable OPcache
```bash
sudo yum install php-opcache -y  # Amazon Linux
sudo apt install php-opcache -y  # Ubuntu
sudo systemctl restart httpd/apache2
```

### MySQL Optimization
```sql
-- Add indexes for better query performance (already in setup.sql)
CREATE INDEX idx_category ON books(category_id);
CREATE INDEX idx_title ON books(title);
CREATE INDEX idx_author ON books(author);
```

## Backup

### Database Backup
```bash
mysqldump -u bookstore_user -p bookstore_db > backup_$(date +%Y%m%d).sql
```

### Full Application Backup
```bash
tar -czf bookstore_backup_$(date +%Y%m%d).tar.gz /var/www/html/bookstore
```

## Support

For issues and questions:
- Check logs: `/var/log/httpd/` or `/var/log/apache2/`
- Verify PHP version: `php -v`
- Check Apache status: `sudo systemctl status httpd/apache2`

## License

This project is created for educational purposes.

## Credits

Developed with ❤️ using modern web technologies.