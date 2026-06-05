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










-- =============================================
-- INSERT FRESH DATA FOR ALL TABLES
-- =============================================

-- 1. USERS
INSERT INTO users (id, name, email, password, role, created_at) VALUES
(1, 'Jeff Liew', 'jeff@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW()),
(2, 'Sarah Tan', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW()),
(3, 'Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW()),
(4, 'Michael Wong', 'michael@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW()),
(5, 'Emily Chen', 'emily@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW());

-- 2. BOOKS (50 Books)
INSERT INTO books (title, author, isbn, category, publication_year, copies, description, availability_status) VALUES
('The Silent Patient', 'Alex Michaelides', '978-1250301697', 'Thriller', 2019, 5, 'A woman shoots her husband and then never speaks another word.', 'Available'),
('Educated', 'Tara Westover', '978-0399590504', 'Memoir', 2018, 4, 'A woman leaves her survivalist family and earns a PhD from Cambridge University.', 'Available'),
('The Midnight Library', 'Matt Haig', '978-0525559474', 'Fiction', 2020, 6, 'Between life and death there is a library where you can live other lives.', 'Available'),
('Atomic Habits', 'James Clear', '978-0735211292', 'Self-Help', 2018, 7, 'Tiny changes, remarkable results. Build good habits and break bad ones.', 'Available'),
('Dune', 'Frank Herbert', '978-0441172719', 'Science Fiction', 1965, 3, 'Paul Atreides unites the Fremen on the desert planet Arrakis.', 'Available'),
('Project Hail Mary', 'Andy Weir', '978-0593135204', 'Science Fiction', 2021, 4, 'A lone astronaut must save Earth from disaster.', 'Available'),
('The Alchemist', 'Paulo Coelho', '978-0062315007', 'Fiction', 1988, 5, 'A philosophical story about following your dreams.', 'Available'),
('The Seven Husbands of Evelyn Hugo', 'Taylor Jenkins Reid', '978-1501161933', 'Romance', 2017, 2, 'A reclusive Hollywood icon finally tells her life story.', 'Available'),
('Pride and Prejudice', 'Jane Austen', '978-0141439518', 'Classic', 1813, 3, 'Elizabeth Bennet and Mr. Darcy in this classic romance.', 'Available'),
('Where the Crawdads Sing', 'Delia Owens', '978-0735219090', 'Fiction', 2018, 5, 'A young woman raised in the marshes becomes a murder suspect.', 'Available'),
('The Housemaid', 'Freida McFadden', '978-1804050000', 'Thriller', 2022, 4, 'A domestic thriller about a housemaid with a dark past.', 'Available'),
('It Ends With Us', 'Colleen Hoover', '978-1501110368', 'Romance', 2016, 6, 'A heartbreaking story about love and difficult choices.', 'Available'),
('The Psychology of Money', 'Morgan Housel', '978-0857197689', 'Self-Help', 2020, 5, 'Timeless lessons on wealth, greed, and happiness.', 'Available'),
('Fourth Wing', 'Rebecca Yarros', '978-1649374042', 'Fantasy', 2023, 3, 'A young woman enters a deadly war college where dragons choose riders.', 'Available'),
('Lessons in Chemistry', 'Bonnie Garmus', '978-0385549400', 'Fiction', 2022, 4, 'A brilliant chemist becomes an unlikely TV cooking show host.', 'Available'),
('The Woman in the Window', 'A.J. Finn', '978-0062678416', 'Thriller', 2018, 3, 'An agoraphobic woman believes she witnessed a murder.', 'Available'),
('Sapiens', 'Yuval Noah Harari', '978-0062316097', 'History', 2011, 5, 'A brief history of humankind.', 'Available'),
('The Great Gatsby', 'F. Scott Fitzgerald', '978-0743273565', 'Classic', 1925, 4, 'The story of the mysterious Jay Gatsby.', 'Available'),
('Verity', 'Colleen Hoover', '978-1538724736', 'Thriller', 2018, 5, 'A struggling writer is hired to finish the books of an injured author.', 'Available'),
('The Song of Achilles', 'Madeline Miller', '978-0062060624', 'Fantasy', 2011, 3, 'A retelling of the Iliad from Patroclus’s perspective.', 'Available'),
('Normal People', 'Sally Rooney', '978-0571334650', 'Fiction', 2018, 4, 'A story of mutual fascination between two teenagers.', 'Available'),
('The 48 Laws of Power', 'Robert Greene', '978-0140280197', 'Self-Help', 1998, 2, 'Understanding and using power in daily life.', 'Available'),
('Dune Messiah', 'Frank Herbert', '978-0593098233', 'Science Fiction', 1969, 3, 'Paul Atreides faces political challenges as emperor.', 'Available'),
('The Love Hypothesis', 'Ali Hazelwood', '978-0593336823', 'Romance', 2021, 5, 'A fake dating romance between a PhD student and a professor.', 'Available'),
('The Mountain Is You', 'Brianna Wiest', '978-1949759228', 'Self-Help', 2020, 4, 'Transforming self-sabotage into self-mastery.', 'Available'),
('The Guest List', 'Lucy Foley', '978-0062868930', 'Thriller', 2020, 3, 'A wedding on a remote island turns deadly.', 'Available'),
('Circe', 'Madeline Miller', '978-0316556347', 'Fantasy', 2018, 4, 'The story of the witch Circe from Greek mythology.', 'Available'),
('The Nightingale', 'Kristin Hannah', '978-1250080400', 'Historical Fiction', 2015, 4, 'Two sisters in France during World War II.', 'Available'),
('The Vanishing Half', 'Brit Bennett', '978-0525536291', 'Fiction', 2020, 3, 'Twin sisters choose very different paths in life.', 'Available'),
('The Push', 'Ashley Audrain', '978-1984881663', 'Thriller', 2021, 2, 'A psychological thriller about motherhood.', 'Available'),
('The Four Agreements', 'Don Miguel Ruiz', '978-1878424310', 'Self-Help', 1997, 6, 'Ancient Toltec wisdom for personal freedom.', 'Available'),
('The Book Thief', 'Markus Zusak', '978-0375842207', 'Historical Fiction', 2005, 4, 'A story narrated by Death during WWII.', 'Available'),
('The House in the Cerulean Sea', 'TJ Klune', '978-1250217288', 'Fantasy', 2020, 3, 'A magical story about an orphanage for magical children.', 'Available'),
('The Lincoln Highway', 'Amor Towles', '978-0735222359', 'Fiction', 2021, 2, 'Three young men on an unexpected road trip in 1950s America.', 'Available'),
('The Maid', 'Nita Prose', '978-0593356159', 'Mystery', 2022, 4, 'A hotel maid becomes the main suspect in a murder.', 'Available'),
('The Paris Apartment', 'Lucy Foley', '978-0063039131', 'Thriller', 2022, 3, 'A woman searches for her brother in Paris.', 'Available'),
('The Dictionary of Lost Words', 'Pip Williams', '978-0593230367', 'Historical Fiction', 2020, 2, 'Women who helped compile the Oxford English Dictionary.', 'Available'),
('The Invisible Life of Addie LaRue', 'V.E. Schwab', '978-0765387561', 'Fantasy', 2020, 5, 'A young woman makes a deal to live forever but is forgotten by everyone.', 'Available'),
('Pachinko', 'Min Jin Lee', '978-1455563920', 'Fiction', 2017, 3, 'A Korean family saga across four generations.', 'Available'),
('The Covenant of Water', 'Abraham Verghese', '978-0802162175', 'Fiction', 2023, 2, 'A sweeping multigenerational family saga in Kerala, India.', 'Available'),
('The 7 Habits of Highly Effective People', 'Stephen R. Covey', '978-1982137274', 'Self-Help', 1989, 5, 'Classic book on personal and professional effectiveness.', 'Available'),
('Atomic Habits (Deluxe Edition)', 'James Clear', '978-0593189641', 'Self-Help', 2018, 0, 'Special collector’s edition.', 'Not Available'),
('The Silent Patient (Collector’s Edition)', 'Alex Michaelides', '978-1250301697', 'Thriller', 2022, 0, 'Limited edition with bonus material.', 'Not Available'),
('Dune (Special Edition)', 'Frank Herbert', '978-0441172719', 'Science Fiction', 1965, 1, 'Last remaining copy.', 'Available'),
('The Midnight Library (Limited)', 'Matt Haig', '978-0525559474', 'Fiction', 2020, 0, 'Limited edition copy.', 'Not Available'),
('It Ends With Us (Special)', 'Colleen Hoover', '978-1501110368', 'Romance', 2016, 2, 'Special edition.', 'Available'),
('The Housemaid (Hardcover)', 'Freida McFadden', '978-1804050000', 'Thriller', 2022, 3, 'Hardcover edition.', 'Available'),
('Fourth Wing (Deluxe)', 'Rebecca Yarros', '978-1649374042', 'Fantasy', 2023, 2, 'Deluxe edition with sprayed edges.', 'Available'),
('The Alchemist (Anniversary)', 'Paulo Coelho', '978-0062315007', 'Fiction', 2014, 4, '25th Anniversary Edition.', 'Available'),
('Sapiens (Illustrated)', 'Yuval Noah Harari', '978-0062316097', 'History', 2022, 3, 'Illustrated edition.', 'Available');

-- 3. BORROWINGS
INSERT INTO borrowings (user_id, book_id, status, due_date, has_penalty) VALUES
(1, 1, 'borrowed', '2026-06-10', 0),
(1, 3, 'borrowed', '2026-06-12', 0),
(2, 5, 'borrowed', '2026-06-05', 1),
(2, 7, 'returned', '2026-05-20', 0),
(3, 2, 'borrowed', '2026-06-15', 0),
(3, 4, 'borrowed', '2026-06-08', 0),
(4, 8, 'returned', '2026-05-28', 0),
(5, 10, 'borrowed', '2026-06-18', 0);

-- 4. BORROWED_BOOKS (if you use this table too)
INSERT INTO borrowed_books (user_id, book_id, status, due_date, has_penalty) VALUES
(1, 1, 'borrowed', '2026-06-10', 0),
(1, 3, 'borrowed', '2026-06-12', 0),
(2, 5, 'borrowed', '2026-06-05', 1),
(2, 7, 'returned', '2026-05-20', 0);

-- 5. ROOMS
INSERT INTO rooms (id, name, capacity, equipment, created_at) VALUES
(1, 'Study Room A', 4, 'Whiteboard, Projector, WiFi', NOW()),
(2, 'Study Room B', 6, 'Whiteboard, TV Screen, WiFi', NOW()),
(3, 'Discussion Room 1', 8, 'Whiteboard, Video Conference, WiFi', NOW()),
(4, 'Quiet Study Pod', 2, 'Desk Lamp, Power Outlets', NOW()),
(5, 'Group Study Room', 10, 'Projector, Whiteboard, WiFi', NOW());

-- 6. ROOM_BOOKINGS
INSERT INTO room_bookings (user_id, room_id, booking_date, start_time, end_time, status) VALUES
(1, 1, '2026-06-07', '10:00:00', '12:00:00', 'confirmed'),
(2, 2, '2026-06-08', '14:00:00', '16:00:00', 'confirmed'),
(3, 3, '2026-06-09', '09:00:00', '11:00:00', 'pending'),
(1, 4, '2026-06-10', '15:00:00', '17:00:00', 'confirmed');

-- 7. ANNOUNCEMENTS
INSERT INTO announcements (title, content, created_at) VALUES
('Library Extended Hours', 'The library will be open until 10 PM during exam week (10 - 20 June 2026).', NOW()),
('New Book Arrival', 'We have added 50 new titles including "Fourth Wing" and "The Covenant of Water".', NOW()),
('Maintenance Notice', 'Study Room C will be closed for maintenance on 8 June 2026.', NOW());

-- 8. EVENTS
INSERT INTO events (title, description, event_date, location, created_at) VALUES
('Book Club: The Silent Patient', 'Monthly book club discussion. All members are welcome!', '2026-06-15', 'Discussion Room 1', NOW()),
('Author Talk: Local Writers Series', 'Meet local authors and discuss their latest works.', '2026-06-22', 'Main Hall', NOW()),
('Study Skills Workshop', 'Learn effective study techniques for exams.', '2026-06-12', 'Study Room B', NOW());

-- 9. FEEDBACK
INSERT INTO feedback (user_id, subject, message, rating, created_at) VALUES
(1, 'Great Service', 'The new book collection is amazing! Very satisfied.', 5, NOW()),
(2, 'Room Booking Issue', 'Had trouble booking a room through the app.', 3, NOW()),
(3, 'Suggestion', 'Would love to see more self-help books.', 4, NOW());