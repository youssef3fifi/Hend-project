# BookStore - Professional Frontend Web Application

A professional, full-featured online bookstore built with pure HTML, CSS, and JavaScript using In-Memory Storage. No backend or database required!

## Features

- **Modern, Responsive Design**: Mobile-first approach with professional UI/UX
- **Complete Book Management**: Browse, search, filter, and purchase books
- **Shopping Cart**: LocalStorage-based cart system with quantity management
- **Admin Dashboard**: Simple inventory management interface
- **Category System**: Organized book categorization
- **Search & Filter**: Advanced search and filtering capabilities
- **In-Memory Storage**: All data stored in JavaScript arrays with localStorage persistence for cart
- **No Setup Required**: Just open index.html and start browsing!

## Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Storage**: In-Memory Arrays + LocalStorage for cart persistence
- **Icons**: Font Awesome 6.4.0
- **No Backend**: Pure client-side application

## Requirements

- Any modern web browser (Chrome, Firefox, Safari, Edge)
- A local web server (optional but recommended)
  - VS Code Live Server
  - Python's http.server
  - PHP's built-in server
  - Node.js http-server

## Quick Start

### Method 1: VS Code Live Server (Recommended)

1. Install the "Live Server" extension in VS Code
2. Open the project folder in VS Code
3. Right-click on `index.html` and select "Open with Live Server"
4. Your browser will automatically open to `http://localhost:5500`

### Method 2: Python HTTP Server

```bash
# Navigate to the project directory
cd /path/to/Hend-project

# Start the server
python -m http.server 8000

# Open your browser to:
# http://localhost:8000
```

### Method 3: PHP Built-in Server

```bash
# Navigate to the project directory
cd /path/to/Hend-project

# Start the server
php -S localhost:8000

# Open your browser to:
# http://localhost:8000
```

### Method 4: Node.js HTTP Server

```bash
# Navigate to the project directory
cd /path/to/Hend-project

# Start the server (install if needed)
npx http-server -p 8000

# Open your browser to:
# http://localhost:8000
```

### Method 5: Direct File Access

Simply double-click `index.html` to open it in your browser. Some features may work better with a local server.

## No Setup Required! ✅

- ✅ No database installation
- ✅ No backend configuration
- ✅ No server setup
- ✅ Just open and run!

See [HOW_TO_RUN.md](HOW_TO_RUN.md) for detailed instructions in Arabic.

## Application Structure

```
bookstore/
├── assets/                 # Static assets
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   └── js/
│       ├── storage.js     # In-Memory storage system
│       └── main.js        # Main JavaScript functions
├── pages/                  # Application pages
│   ├── admin/
│   │   └── index.html     # Admin dashboard
│   ├── book-details.html  # Book details page
│   ├── cart.html          # Shopping cart
│   └── shop.html          # Shop/catalog page
├── index.html             # Home page
├── HOW_TO_RUN.md          # Running instructions (Arabic)
└── README.md              # This file
```

## Features Overview

### 🏠 Home Page
- Hero section with search functionality
- Category browsing
- Featured books showcase

### 📚 Shop Page
- Complete book catalog
- Advanced filtering (category, price range, search)
- Pagination support
- Responsive grid layout

### 📖 Book Details Page
- Comprehensive book information
- Stock availability
- Related books suggestions
- Add to cart functionality

### 🛒 Shopping Cart
- View all cart items
- Update quantities
- Remove items
- Order summary
- LocalStorage persistence

### 👨‍💼 Admin Dashboard
- View inventory statistics
- Book management interface
- Simple CRUD operations
- Responsive data table

## Data Storage

All book data is stored in JavaScript arrays in `assets/js/storage.js`. The shopping cart is persisted to localStorage for convenience.

### Modifying Books

To add, edit, or remove books, edit the `books` array in `assets/js/storage.js`:

```javascript
this.books = [
    {
        id: 1,
        title: "Your Book Title",
        author: "Author Name",
        price: 19.99,
        category: "Fiction",
        isbn: "978-1-234-56789-0",
        stock: 10,
        description: "Book description...",
        image: "https://example.com/image.jpg",
        rating: 4.5
    }
    // Add more books...
];
```

## Browser Compatibility

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Opera 76+

## Advantages

- **Zero Setup**: No installation or configuration required
- **Fast**: No database queries or server-side processing
- **Portable**: Works on any platform with a web browser
- **Simple**: Easy to understand and modify
- **Lightweight**: Minimal dependencies

## Support

For issues and questions:
- Check logs: `/var/log/httpd/` or `/var/log/apache2/`
- Verify PHP version: `php -v`
- Check Apache status: `sudo systemctl status httpd/apache2`

## License

This project is created for educational purposes.

## Credits

Developed with ❤️ using modern web technologies.