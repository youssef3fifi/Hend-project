# 📚 BookStore - Full-Stack Web Application

A professional, full-featured online bookstore built with **Node.js + Express** backend and vanilla JavaScript frontend. Features a REST API with in-memory storage and session-based cart management.

## 🌟 Features

### Backend Features
- ✅ **RESTful API** - Clean and well-documented endpoints
- ✅ **Express.js** - Fast and minimalist web framework
- ✅ **In-Memory Storage** - No database setup required
- ✅ **Session-Based Cart** - Persistent shopping cart per session
- ✅ **CORS Enabled** - Cross-origin resource sharing
- ✅ **Error Handling** - Comprehensive error handling
- ✅ **JSON Responses** - Consistent response structure

### Frontend Features
- ✅ **Modern, Responsive Design** - Mobile-first approach
- ✅ **Complete Book Management** - Browse, search, filter
- ✅ **Shopping Cart** - API-based cart with quantity management
- ✅ **Admin Dashboard** - Book inventory management
- ✅ **Category System** - Organized book categorization
- ✅ **Search & Filter** - Advanced search capabilities
- ✅ **Real-time Updates** - Cart count updates automatically

## 🛠 Tech Stack

### Backend
- **Runtime**: Node.js
- **Framework**: Express.js 4.18.2
- **Middleware**: 
  - cors 2.8.5
  - body-parser 1.20.2
- **Dev Tools**: nodemon 3.0.1

### Frontend
- **Languages**: HTML5, CSS3, JavaScript (ES6+)
- **Icons**: Font Awesome 6.4.0
- **Architecture**: SPA with API integration

## 📋 Requirements

- Node.js 14+ and npm
- Any modern web browser (Chrome, Firefox, Safari, Edge)

## 🚀 Quick Start

### 1. Install Dependencies

```bash
# Navigate to backend directory
cd backend

# Install all dependencies
npm install
```

### 2. Start the Server

```bash
# Development mode (with auto-restart)
npm run dev

# Production mode
npm start
```

### 3. Access the Application

Open your browser to: **http://localhost:3000**

- Frontend automatically served
- API available at http://localhost:3000/api

## 📁 Project Structure

```
bookstore/
├── backend/                    # Node.js + Express backend
│   ├── models/
│   │   └── storage.js         # In-memory storage with session support
│   ├── routes/
│   │   ├── books.js           # Book CRUD endpoints
│   │   ├── cart.js            # Cart management endpoints
│   │   └── categories.js      # Category endpoints
│   ├── server.js              # Main Express server
│   ├── package.json           # Dependencies and scripts
│   └── node_modules/          # Installed packages (gitignored)
│
├── frontend/                   # Frontend application
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css      # Main stylesheet
│   │   └── js/
│   │       ├── main.js        # Main JavaScript with API calls
│   │       └── storage.js     # Legacy (kept for reference)
│   ├── pages/
│   │   ├── admin/
│   │   │   └── index.html     # Admin dashboard
│   │   ├── book-details.html  # Book details page
│   │   ├── cart.html          # Shopping cart
│   │   └── shop.html          # Shop/catalog page
│   ├── config.js              # API configuration
│   └── index.html             # Home page
│
├── HOW_TO_RUN.md              # Detailed setup instructions
├── README.md                  # This file
└── .gitignore                 # Git ignore rules
```

## 🔌 API Documentation

### Response Format

All API responses follow this structure:
```json
{
  "success": true,
  "data": { /* response data */ },
  "message": "Success message"
}
```

### Books API

#### Get All Books
```
GET /api/books
Query Params: ?search=query&category=Fiction
```

#### Get Book by ID
```
GET /api/books/:id
```

#### Create Book (Admin)
```
POST /api/books
Body: {
  "title": "Book Title",
  "author": "Author Name",
  "price": 19.99,
  "category": "Fiction",
  "isbn": "978-1-234-56789-0",
  "stock": 10,
  "description": "Book description",
  "image": "https://example.com/image.jpg"
}
```

#### Update Book (Admin)
```
PUT /api/books/:id
Body: { /* fields to update */ }
```

#### Delete Book (Admin)
```
DELETE /api/books/:id
```

### Cart API

All cart endpoints require `X-Session-ID` header.

#### Get Cart
```
GET /api/cart
Headers: X-Session-ID: your-session-id
```

#### Add to Cart
```
POST /api/cart/add
Headers: X-Session-ID: your-session-id
Body: {
  "bookId": 1,
  "quantity": 2
}
```

#### Update Cart Item
```
PUT /api/cart/update
Headers: X-Session-ID: your-session-id
Body: {
  "bookId": 1,
  "quantity": 3
}
```

#### Remove from Cart
```
DELETE /api/cart/remove/:bookId
Headers: X-Session-ID: your-session-id
```

#### Clear Cart
```
DELETE /api/cart/clear
Headers: X-Session-ID: your-session-id
```

### Categories API

#### Get All Categories
```
GET /api/categories
```

#### Get Category by ID
```
GET /api/categories/:id
```

### Health Check
```
GET /api/health
```

## 💾 Data Storage

### In-Memory Storage
- All data stored in Node.js memory
- **Data resets on server restart**
- Pre-loaded with 8 sample books
- 6 pre-defined categories

### Pre-loaded Books
1. The Great Gatsby - F. Scott Fitzgerald
2. To Kill a Mockingbird - Harper Lee
3. 1984 - George Orwell
4. Pride and Prejudice - Jane Austen
5. The Hobbit - J.R.R. Tolkien
6. Harry Potter and the Philosopher's Stone - J.K. Rowling
7. The Catcher in the Rye - J.D. Salinger
8. The Lord of the Rings - J.R.R. Tolkien

### Categories
- Fiction
- Science Fiction
- Fantasy
- Romance
- Mystery
- Non-Fiction

### Session Management

- Session ID automatically generated on first visit
- Stored in browser's localStorage
- Sent with each API request via `X-Session-ID` header
- Cart persists per session until server restart

## 🚀 AWS EC2 Deployment

### Prerequisites
1. EC2 instance running (Amazon Linux 2 or Ubuntu)
2. Security group with port 3000 open
3. SSH access to instance

### Deployment Steps

```bash
# 1. Connect to EC2
ssh -i your-key.pem ec2-user@your-ec2-ip

# 2. Install Node.js
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
sudo yum install -y nodejs

# 3. Clone and setup
git clone your-repo-url
cd Hend-project/backend
npm install

# 4. Install PM2 (process manager)
sudo npm install -g pm2

# 5. Start application
pm2 start server.js --name bookstore
pm2 startup
pm2 save
```

### Security Group Configuration

Add inbound rule:
- **Type**: Custom TCP
- **Port**: 3000
- **Source**: 0.0.0.0/0 (or specific IPs)

### Access Application
```
http://YOUR_EC2_PUBLIC_IP:3000
```

The frontend automatically detects the server IP and connects to the API.

## 🔧 Configuration

### Environment Variables

```bash
# Set custom port
PORT=8080 npm start

# Or create .env file (requires dotenv package)
PORT=3000
```

### Frontend API Configuration

Edit `frontend/config.js`:
```javascript
const API_CONFIG = {
    BASE_URL: 'http://your-server-ip:3000/api',
    // ...
};
```

## 🧪 Testing API Endpoints

### Using curl

```bash
# Test health
curl http://localhost:3000/api/health

# Get all books
curl http://localhost:3000/api/books

# Get categories
curl http://localhost:3000/api/categories

# Add to cart
curl -X POST http://localhost:3000/api/cart/add \
  -H "Content-Type: application/json" \
  -H "X-Session-ID: test-session" \
  -d '{"bookId": 1, "quantity": 2}'

# Get cart
curl http://localhost:3000/api/cart \
  -H "X-Session-ID: test-session"
```

## 🎯 Features Overview

### 🏠 Home Page
- Hero section with search
- Category browsing cards
- Featured books showcase
- Responsive grid layout

### 📚 Shop Page
- Complete book catalog
- Advanced filtering (category, price, search)
- Real-time search
- Add to cart functionality

### 📖 Book Details Page
- Comprehensive book information
- Stock availability
- Related books suggestions
- Add to cart with quantity

### 🛒 Shopping Cart
- View all cart items
- Update quantities
- Remove items
- Order summary
- Real-time total calculation

### 👨‍💼 Admin Dashboard
- View inventory statistics
- Book management interface
- CRUD operations on books
- Responsive data table

## 🔒 Security Considerations

- No authentication implemented (add for production)
- Admin endpoints accessible to all (secure for production)
- CORS enabled for all origins (restrict for production)
- Input validation on API endpoints
- SQL injection not applicable (in-memory storage)
- XSS protection via proper HTML escaping

## 📈 Performance

- In-memory storage for fast access
- No database queries
- Session-based cart (no localStorage limitations)
- Static file serving optimized by Express
- Minimal dependencies

## 🐛 Troubleshooting

### Server Won't Start
```bash
# Check if port is in use
lsof -i :3000

# Kill process on port 3000
kill -9 <PID>
```

### API Not Responding
- Verify server is running: `curl http://localhost:3000/api/health`
- Check console for errors
- Verify correct API URL in `frontend/config.js`

### Cart Not Working
- Check browser console for errors
- Verify `X-Session-ID` header is being sent
- Check localStorage for session ID

## 🤝 Contributing

This is an educational project. Feel free to:
- Add database integration
- Implement authentication
- Add payment processing
- Enhance UI/UX
- Add tests

## 📄 License

This project is for educational purposes.

## 🙏 Acknowledgments

Built with ❤️ using:
- Express.js
- Node.js
- Vanilla JavaScript
- Font Awesome

---

**For detailed instructions, see [HOW_TO_RUN.md](HOW_TO_RUN.md)**
