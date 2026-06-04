-- ============================================
-- SWEETHEART LIBRARY - COMPLETE DATABASE SETUP
-- ============================================

DROP DATABASE IF EXISTS sweetheart_library;
CREATE DATABASE sweetheart_library;
USE sweetheart_library;

-- ========================
-- USERS TABLE
-- ========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    student_id VARCHAR(50),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================
-- BOOKS TABLE
-- ========================
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100),
    year YEAR,
    description TEXT,
    cover_image VARCHAR(255),
    available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================
-- ROOMS TABLE
-- ========================
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT,
    equipment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================
-- BOOKINGS TABLE (Updated with time range)
-- ========================
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    booking_date DATE,
    start_time TIME,
    end_time TIME,
    purpose TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================
-- EVENTS TABLE
-- ========================
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    event_date DATE,
    event_time TIME,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    type ENUM('Suggestion', 'Complaint', 'Praise', 'Other') DEFAULT 'Other',
    message TEXT NOT NULL,
    rating DECIMAL(2,1) DEFAULT 5.0,
    status VARCHAR(20) DEFAULT 'Reviewed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    INDEX (email),
    INDEX (token)
);

-- ============================================
-- DUMMY DATA (Lots of sample data)
-- ============================================
-- =============================================
-- DEMO LOGIN CREDENTIALS
-- Password for ALL accounts (Admin + Users): password
-- =============================================

-- Admin Login:
-- Email: admin@library.com
-- Password: password

-- User Login (Example):
-- Email: angeline@example.com
-- Password: password

-- USERS
INSERT INTO users (name, email, password, role) VALUES
('Angeline Hui Lii CHIU', 'angeline@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Admin Library', 'admin@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('John Tan', 'john.tan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Sarah Lim', 'sarah.lim@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('David Wong', 'david.wong@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- BOOKS (20 books)
INSERT INTO books (title, author, year, description, cover_image) VALUES
('Pride and Prejudice', 'Jane Austen', 1813, 'A classic romance novel about manners and marriage.', 'https://picsum.photos/id/201/400/300'),
('The Great Gatsby', 'F. Scott Fitzgerald', 1925, 'A story of wealth, love, and the American Dream.', 'https://picsum.photos/id/237/400/300'),
('To Kill a Mockingbird', 'Harper Lee', 1960, 'A powerful novel about racial injustice.', 'https://picsum.photos/id/180/400/300'),
('Jane Eyre', 'Charlotte Brontë', 1847, 'A gothic romance about love and independence.', 'https://picsum.photos/id/160/400/300'),
('Wuthering Heights', 'Emily Brontë', 1847, 'A dark tale of love and revenge.', 'https://picsum.photos/id/251/400/300'),
('The Catcher in the Rye', 'J.D. Salinger', 1951, 'A coming-of-age story of teenage rebellion.', 'https://picsum.photos/id/1005/400/300'),
('Little Women', 'Louisa May Alcott', 1868, 'The story of four sisters during the Civil War.', 'https://picsum.photos/id/1011/400/300'),
('The Alchemist', 'Paulo Coelho', 1988, 'A philosophical novel about following your dreams.', 'https://picsum.photos/id/133/400/300'),
('The Kite Runner', 'Khaled Hosseini', 2003, 'A story of friendship, betrayal, and redemption.', 'https://picsum.photos/id/160/400/300'),
('The Book Thief', 'Markus Zusak', 2005, 'A story narrated by Death during WWII.', 'https://picsum.photos/id/201/400/300'),
('The Night Circus', 'Erin Morgenstern', 2011, 'A magical competition between two illusionists.', 'https://picsum.photos/id/251/400/300'),
('The Silent Patient', 'Alex Michaelides', 2019, 'A psychological thriller about a woman who never speaks.', 'https://picsum.photos/id/180/400/300'),
('Where the Crawdads Sing', 'Delia Owens', 2018, 'A murder mystery set in the marshes of North Carolina.', 'https://picsum.photos/id/106/400/300'),
('The Seven Husbands of Evelyn Hugo', 'Taylor Jenkins Reid', 2017, 'A reclusive Hollywood icon tells her life story.', 'https://picsum.photos/id/1005/400/300'),
('Dune', 'Frank Herbert', 1965, 'A science fiction epic about politics and destiny.', 'https://picsum.photos/id/133/400/300');

-- ROOMS
INSERT INTO rooms (name, capacity, equipment) VALUES
('Rose Room', 4, 'Whiteboard, Projector, HDMI'),
('Lily Room', 6, 'TV, Speakers, Whiteboard'),
('Orchid Room', 8, 'Projector, Coffee Machine, Whiteboard'),
('Jasmine Room', 3, 'Whiteboard, Natural Light'),
('Lavender Room', 10, 'TV, Projector, Conference Table');

-- EVENTS
INSERT INTO events (title, event_date, event_time, description) VALUES
('Book Reading: Pride and Prejudice', '2026-06-15', '14:00:00', 'Join us for a relaxed reading and discussion.'),
('Creative Writing Workshop', '2026-06-20', '10:00:00', 'Learn techniques to improve your creative writing.'),
('Poetry Night', '2026-06-25', '19:00:00', 'An evening of poetry reading and open mic.'),
('Author Talk: Modern Romance', '2026-07-02', '16:00:00', 'Meet local authors and discuss contemporary romance.');

-- BOOKINGS (with time range)
INSERT INTO bookings (user_id, booking_date, start_time, end_time, purpose, status) VALUES
(1, '2026-06-10', '10:00:00', '12:00:00', 'Group study for literature assignment', 'confirmed'),
(1, '2026-06-12', '14:00:00', '16:00:00', 'Quiet reading session', 'confirmed'),
(1, '2026-06-18', '09:00:00', '11:00:00', 'Meeting with study group', 'confirmed'),
(3, '2026-06-11', '15:00:00', '17:00:00', 'Final year project discussion', 'confirmed'),
(4, '2026-06-13', '10:30:00', '12:30:00', 'Online class preparation', 'confirmed'),
(5, '2026-06-19', '19:00:00', '21:00:00', 'Group assignment meeting', 'confirmed');

-- ==================== BORROWED BOOKS TABLE ====================
CREATE TABLE IF NOT EXISTS `borrowed_books` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `user_name` VARCHAR(150) NOT NULL,
  `book_id` INT NOT NULL,
  `book_title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `borrow_date` DATE NOT NULL,
  `due_date` DATE GENERATED ALWAYS AS (DATE_ADD(`borrow_date`, INTERVAL 14 DAY)) STORED,
  `status` ENUM('On Time', 'Overdue', 'Returned') DEFAULT 'On Time',
  `has_penalty` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==================== ROOM BOOKINGS TABLE ====================
CREATE TABLE IF NOT EXISTS `room_bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `user_name` VARCHAR(150) NOT NULL,
  `room_id` INT NOT NULL,
  `room_name` VARCHAR(100) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `status` ENUM('Active', 'Completed') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Data
INSERT INTO borrowed_books (user_id, user_name, book_id, book_title, author, borrow_date, status) 
VALUES 
(1, 'Ali Ahmad', 5, 'The Great Gatsby', 'F. Scott Fitzgerald', '2026-05-20', 'Overdue'),
(2, 'Siti Nur', 12, 'Clean Code', 'Robert C. Martin', '2026-05-28', 'On Time');

INSERT INTO room_bookings (user_id, user_name, room_id, room_name, start_time, end_time) 
VALUES 
(3, 'John Doe', 1, 'Study Room A', '2026-06-04 10:00:00', '2026-06-04 12:00:00'),
(4, 'Aisyah Lee', 2, 'Study Room B', '2026-06-03 08:00:00', '2026-06-03 10:00:00');

INSERT INTO feedback (name, email, type, message, rating) VALUES
('Aisyah Rahman', 'aisyah.rahman@student.edu.my', 'Praise', 'The library website is very clean and easy to use. I love the booking system!', 5.0),
('Muhammad Faris', 'faris.muhammad@gmail.com', 'Suggestion', 'It would be great if we can see the availability of rooms in real-time on the calendar.', 4.0),
('Nur Aina', 'nuraina98@yahoo.com', 'Complaint', 'Sometimes the system is slow when I try to book a room during peak hours.', 2.5),
('Daniel Tan', 'daniel.tan@outlook.com', 'Praise', 'The book catalog search is very fast and accurate. Good job!', 5.0),
('Siti Aminah', 'siti.aminah@student.edu.my', 'Suggestion', 'Please add a dark mode option. It will be easier on the eyes during night study.', 4.5),
('Ahmad Zulkifli', 'zulkifli.ahmad@gmail.com', 'Other', 'The library should consider extending the operating hours during exam week.', 3.5),
('Lim Wei Ling', 'weiling.lim@student.edu.my', 'Praise', 'I really like the modern design and the pink theme. It feels premium!', 5.0),
('Ravi Kumar', 'ravi.kumar@hotmail.com', 'Complaint', 'I faced an error when trying to submit feedback. It kept loading forever.', 2.0),
('Fatimah Zahra', 'fatimah.zahra@gmail.com', 'Suggestion', 'Can we have a feature to save favorite books or create reading lists?', 4.0),
('Joshua Lim', 'joshua.lim@student.edu.my', 'Praise', 'Admin dashboard looks professional. Everything is well organized.', 4.5);