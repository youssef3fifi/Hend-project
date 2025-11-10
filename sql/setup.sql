-- Book Store Database Setup Script
-- Run this script to initialize the database

-- Create database
CREATE DATABASE IF NOT EXISTS bookstore_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookstore_db;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    isbn VARCHAR(13) UNIQUE,
    stock_quantity INT DEFAULT 0,
    image_url VARCHAR(500),
    rating DECIMAL(2, 1) DEFAULT 0.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_title (title),
    INDEX idx_author (author)
) ENGINE=InnoDB;

-- Cart items table (session-based)
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    book_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (session_id, book_id),
    INDEX idx_session (session_id)
) ENGINE=InnoDB;

-- Users table (optional - for admin and future user accounts)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Fiction', 'Novels, short stories, and literary fiction'),
('Non-Fiction', 'Biographies, history, science, and self-help'),
('Science Fiction', 'Futuristic and speculative fiction'),
('Mystery', 'Detective stories, crime fiction, and thrillers'),
('Romance', 'Love stories and romantic fiction'),
('Biography', 'Life stories of notable people'),
('Technology', 'Computing, programming, and tech books'),
('Business', 'Management, entrepreneurship, and finance');

-- Insert sample books
INSERT INTO books (title, author, description, price, category_id, isbn, stock_quantity, image_url, rating) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 'A classic American novel set in the Jazz Age, exploring themes of wealth, love, and the American Dream.', 12.99, 1, '9780743273565', 50, 'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg', 4.5),
('To Kill a Mockingbird', 'Harper Lee', 'A gripping tale of racial injustice and childhood innocence in the American South.', 14.99, 1, '9780061120084', 45, 'https://covers.openlibrary.org/b/isbn/9780061120084-L.jpg', 4.8),
('1984', 'George Orwell', 'A dystopian novel about totalitarianism and surveillance in a grim future society.', 13.99, 3, '9780451524935', 60, 'https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg', 4.7),
('Pride and Prejudice', 'Jane Austen', 'A romantic novel of manners set in Georgian England.', 11.99, 5, '9780141439518', 40, 'https://covers.openlibrary.org/b/isbn/9780141439518-L.jpg', 4.6),
('The Hobbit', 'J.R.R. Tolkien', 'A fantasy adventure of Bilbo Baggins and his quest for treasure.', 15.99, 3, '9780547928227', 55, 'https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg', 4.8),
('Sapiens', 'Yuval Noah Harari', 'A brief history of humankind from the Stone Age to the modern age.', 18.99, 2, '9780062316110', 35, 'https://covers.openlibrary.org/b/isbn/9780062316110-L.jpg', 4.7),
('The Da Vinci Code', 'Dan Brown', 'A mystery thriller involving art, religion, and secret societies.', 16.99, 4, '9780307474278', 30, 'https://covers.openlibrary.org/b/isbn/9780307474278-L.jpg', 4.3),
('Steve Jobs', 'Walter Isaacson', 'The exclusive biography of the Apple founder and tech visionary.', 19.99, 6, '9781451648539', 25, 'https://covers.openlibrary.org/b/isbn/9781451648539-L.jpg', 4.5),
('Clean Code', 'Robert C. Martin', 'A handbook of agile software craftsmanship and best practices.', 42.99, 7, '9780132350884', 20, 'https://covers.openlibrary.org/b/isbn/9780132350884-L.jpg', 4.7),
('Atomic Habits', 'James Clear', 'An easy and proven way to build good habits and break bad ones.', 16.99, 2, '9780735211292', 48, 'https://covers.openlibrary.org/b/isbn/9780735211292-L.jpg', 4.8),
('The Lean Startup', 'Eric Ries', 'How constant innovation creates radically successful businesses.', 17.99, 8, '9780307887894', 22, 'https://covers.openlibrary.org/b/isbn/9780307887894-L.jpg', 4.4),
('Dune', 'Frank Herbert', 'A science fiction epic set on the desert planet Arrakis.', 18.99, 3, '9780441172719', 38, 'https://covers.openlibrary.org/b/isbn/9780441172719-L.jpg', 4.6),
('Gone Girl', 'Gillian Flynn', 'A psychological thriller about a marriage gone terribly wrong.', 14.99, 4, '9780307588371', 42, 'https://covers.openlibrary.org/b/isbn/9780307588371-L.jpg', 4.2),
('The Alchemist', 'Paulo Coelho', 'A philosophical story about following your dreams.', 13.99, 1, '9780062315007', 52, 'https://covers.openlibrary.org/b/isbn/9780062315007-L.jpg', 4.5),
('Educated', 'Tara Westover', 'A memoir about a woman who leaves her survivalist family to pursue education.', 16.99, 6, '9780399590504', 28, 'https://covers.openlibrary.org/b/isbn/9780399590504-L.jpg', 4.7),
('The Pragmatic Programmer', 'Andrew Hunt', 'Your journey to mastery in software development.', 44.99, 7, '9780135957059', 18, 'https://covers.openlibrary.org/b/isbn/9780135957059-L.jpg', 4.8),
('Thinking, Fast and Slow', 'Daniel Kahneman', 'Explores the two systems that drive the way we think.', 19.99, 2, '9780374533557', 31, 'https://covers.openlibrary.org/b/isbn/9780374533557-L.jpg', 4.6),
('The Girl on the Train', 'Paula Hawkins', 'A gripping psychological thriller about memory and deception.', 14.99, 4, '9781594634024', 36, 'https://covers.openlibrary.org/b/isbn/9781594634024-L.jpg', 4.1);

-- Create default admin user (username: admin, password: admin123)
-- Password hash for 'admin123' using PASSWORD_DEFAULT
INSERT INTO users (username, email, password_hash, is_admin) VALUES
('admin', 'admin@bookstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- Grant permissions (adjust as needed for your MySQL user)
-- GRANT ALL PRIVILEGES ON bookstore_db.* TO 'bookstore_user'@'localhost' IDENTIFIED BY 'bookstore_password';
-- FLUSH PRIVILEGES;
