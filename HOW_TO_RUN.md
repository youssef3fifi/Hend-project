# 🚀 How to Run the BookStore Application

This is a full-stack Node.js + Express application with a frontend served by the backend.

## Prerequisites

- Node.js (version 14 or higher)
- npm (comes with Node.js)

### Installing Node.js

#### On Windows:
1. Download from [nodejs.org](https://nodejs.org/)
2. Run the installer
3. Verify installation: `node --version` and `npm --version`

#### On macOS:
```bash
# Using Homebrew
brew install node

# Verify installation
node --version
npm --version
```

#### On Linux (Ubuntu/Debian):
```bash
# Update package index
sudo apt update

# Install Node.js
sudo apt install nodejs npm

# Verify installation
node --version
npm --version
```

## Installation & Running

### 1. Install Dependencies

```bash
# Navigate to the backend directory
cd backend

# Install dependencies
npm install
```

### 2. Start the Server

```bash
# Development mode (auto-restart on changes)
npm run dev

# Production mode
npm start
```

The server will start on **port 3000** by default.

### 3. Access the Application

Open your browser and navigate to:
- **Frontend**: http://localhost:3000
- **API Documentation**: See below

## API Endpoints

### Books

- `GET /api/books` - Get all books
  - Query params: `search`, `category`
- `GET /api/books/:id` - Get book by ID
- `POST /api/books` - Create book (Admin)
- `PUT /api/books/:id` - Update book (Admin)
- `DELETE /api/books/:id` - Delete book (Admin)

### Cart

- `GET /api/cart` - Get cart contents
- `POST /api/cart/add` - Add item to cart
  - Body: `{ bookId, quantity }`
- `PUT /api/cart/update` - Update cart item
  - Body: `{ bookId, quantity }`
- `DELETE /api/cart/remove/:bookId` - Remove item from cart
- `DELETE /api/cart/clear` - Clear cart

### Categories

- `GET /api/categories` - Get all categories
- `GET /api/categories/:id` - Get category by ID

### Health Check

- `GET /api/health` - Server health status

## Session Management

The cart is session-based. The session ID is automatically generated and stored in localStorage on the client side. It's sent with each request via the `X-Session-ID` header.

## Features

✅ **Backend Features:**
- Express.js REST API
- In-memory storage (data resets on server restart)
- Session-based cart management
- CORS enabled for cross-origin requests
- JSON responses with consistent structure
- Error handling

✅ **Frontend Features:**
- Responsive design
- Real-time cart updates
- Search and filter functionality
- Admin dashboard
- Category browsing

## AWS EC2 Deployment

### 1. Prepare EC2 Instance

```bash
# Connect to your EC2 instance
ssh -i your-key.pem ec2-user@your-ec2-ip

# Update system
sudo yum update -y  # Amazon Linux
# or
sudo apt update && sudo apt upgrade -y  # Ubuntu

# Install Node.js
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -  # Amazon Linux
sudo yum install -y nodejs  # Amazon Linux
# or
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -  # Ubuntu
sudo apt-get install -y nodejs  # Ubuntu
```

### 2. Configure Security Group

In AWS Console:
1. Go to EC2 > Security Groups
2. Select your instance's security group
3. Add inbound rule:
   - Type: Custom TCP
   - Port Range: 3000
   - Source: 0.0.0.0/0 (or specific IPs for security)

### 3. Deploy Application

```bash
# Clone repository
git clone your-repo-url
cd Hend-project

# Install dependencies
cd backend
npm install

# Install PM2 for process management
sudo npm install -g pm2

# Start application with PM2
pm2 start server.js --name bookstore

# Make PM2 start on system boot
pm2 startup
pm2 save
```

### 4. Access Application

- Open browser: `http://YOUR_EC2_PUBLIC_IP:3000`
- The frontend will automatically connect to the correct backend URL

### PM2 Commands

```bash
# View logs
pm2 logs bookstore

# Restart application
pm2 restart bookstore

# Stop application
pm2 stop bookstore

# View status
pm2 status

# Monitor
pm2 monit
```

## Environment Variables

You can configure the application using environment variables:

```bash
# Set custom port
PORT=8080 npm start

# For EC2, you can create a .env file or set environment variables
export PORT=3000
```

## Troubleshooting

### Port Already in Use
```bash
# Find process using port 3000
lsof -i :3000  # macOS/Linux
netstat -ano | findstr :3000  # Windows

# Kill the process
kill -9 PID  # macOS/Linux
taskkill /PID PID /F  # Windows
```

### Cannot Connect to Server
- Check if server is running: `curl http://localhost:3000/api/health`
- Check firewall settings
- Verify port 3000 is not blocked
- For EC2: Verify security group rules

### API Errors
- Check server logs in console
- Verify request format (JSON, headers)
- Check session ID is being sent

## Data Management

**Important**: All data is stored in memory and will be lost when the server restarts. The application comes with 8 pre-loaded books and 6 categories.

To persist data across restarts, you would need to:
1. Add a database (MongoDB, PostgreSQL, etc.)
2. Implement data persistence in the storage model
3. Add environment configuration

## Development Tips

```bash
# Run in development mode with auto-restart
npm run dev

# The server will automatically restart when you make changes to:
# - server.js
# - routes/*.js
# - models/*.js
```

## File Structure

```
Hend-project/
├── backend/
│   ├── models/
│   │   └── storage.js       # In-memory storage
│   ├── routes/
│   │   ├── books.js         # Book endpoints
│   │   ├── cart.js          # Cart endpoints
│   │   └── categories.js    # Category endpoints
│   ├── server.js            # Main server file
│   └── package.json         # Dependencies
├── frontend/
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   ├── pages/
│   ├── config.js            # API configuration
│   └── index.html           # Home page
└── README.md
```

## Support

For issues:
1. Check server logs
2. Verify Node.js and npm versions
3. Ensure all dependencies are installed
4. Check API endpoint responses

## License

This project is for educational purposes.
