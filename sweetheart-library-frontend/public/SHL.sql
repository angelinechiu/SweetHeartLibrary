-- =============================================
-- CREATE NEW DATABASE: SHL
-- =============================================

CREATE DATABASE IF NOT EXISTS SHL;
USE SHL;

-- =============================================
-- CREATE TABLES
-- =============================================

-- 1. USERS
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- 2. BOOKS
CREATE TABLE books (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(150) NOT NULL,
    isbn VARCHAR(50) DEFAULT NULL,
    category VARCHAR(50) DEFAULT NULL,
    year INT(11) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    book_cover_image VARCHAR(255) DEFAULT NULL,
    available_copies INT(11) DEFAULT 3,
    total_copies INT(11) DEFAULT 3,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
ALTER TABLE books 
ADD COLUMN is_featured TINYINT(1) DEFAULT 0,
ADD COLUMN is_popular  TINYINT(1) DEFAULT 0;

-- 3. BORROWINGS
CREATE TABLE borrowings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    book_id INT(11) NOT NULL,
    borrowed_date DATE NOT NULL,
    due_date DATE NOT NULL,
    returned_date DATE DEFAULT NULL,
    status ENUM('Borrowed','Returned','Overdue') DEFAULT 'Borrowed',
    renewal_count INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- 4. BORROWED_BOOKS
CREATE TABLE borrowed_books (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) DEFAULT NULL,
    user_name VARCHAR(100) DEFAULT NULL,
    book_id INT(11) DEFAULT NULL,
    book_title VARCHAR(255) DEFAULT NULL,
    author VARCHAR(100) DEFAULT NULL,
    borrow_date DATE DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'Borrowed',
    has_penalty TINYINT(1) DEFAULT 0,
    PRIMARY KEY (id)
);

-- 5. ROOMS
CREATE TABLE rooms (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    capacity INT(11) DEFAULT 4,
    location VARCHAR(100) DEFAULT NULL,
    facilities TEXT DEFAULT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id)
);

-- 6. ROOM_BOOKINGS
CREATE TABLE room_bookings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) DEFAULT NULL,
    user_name VARCHAR(100) DEFAULT NULL,
    room_id INT(11) DEFAULT NULL,
    room_name VARCHAR(100) DEFAULT NULL,
    start_time DATETIME DEFAULT NULL,
    end_time DATETIME DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'Active',
    PRIMARY KEY (id)
);

-- 7. ANNOUNCEMENTS
CREATE TABLE announcements (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('Notice','Overdue Reminder','Event','Maintenance') DEFAULT 'Notice',
    due_date DATE DEFAULT NULL,
    is_published TINYINT(1) DEFAULT 1,
    created_by INT(11) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- 8. EVENTS
CREATE TABLE events (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    event_date DATE DEFAULT NULL,
    event_time TIME DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- 9. FEEDBACK
CREATE TABLE feedback (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) DEFAULT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    type ENUM('Suggestion','Complaint','Praise','Other') DEFAULT 'Other',
    message TEXT NOT NULL,
    rating DECIMAL(2,1) DEFAULT 5.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE `password_resets` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `email` (`email`),
    INDEX `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- INSERT DATA
-- =============================================

-- USERS
INSERT INTO users (name, email, password, role) VALUES
('Angeline Chiu', 'angeline@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Sarah Tan', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Michael Wong', 'michael@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Emily Chen', 'emily@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- BOOKS
INSERT INTO books (title, author, isbn, category, year, description, available_copies, total_copies) VALUES
('The Silent Patient', 'Alex Michaelides', '978-1250301697', 'Thriller', 2019, 'A woman shoots her husband and then never speaks another word.', 5, 5),
('Educated', 'Tara Westover', '978-0399590504', 'Memoir', 2018, 'A woman leaves her survivalist family and earns a PhD from Cambridge University.', 4, 4),
('The Midnight Library', 'Matt Haig', '978-0525559474', 'Fiction', 2020, 'Between life and death there is a library where you can live other lives.', 6, 6),
('Atomic Habits', 'James Clear', '978-0735211292', 'Self-Help', 2018, 'Tiny changes, remarkable results. Build good habits and break bad ones.', 7, 7),
('Dune', 'Frank Herbert', '978-0441172719', 'Science Fiction', 1965, 'Paul Atreides unites the Fremen on the desert planet Arrakis.', 3, 3),
('Project Hail Mary', 'Andy Weir', '978-0593135204', 'Science Fiction', 2021, 'A lone astronaut must save Earth from disaster.', 4, 4),
('The Alchemist', 'Paulo Coelho', '978-0062315007', 'Fiction', 1988, 'A philosophical story about following your dreams.', 5, 5),
('The Seven Husbands of Evelyn Hugo', 'Taylor Jenkins Reid', '978-1501161933', 'Romance', 2017, 'A reclusive Hollywood icon finally tells her life story.', 2, 2),
('Pride and Prejudice', 'Jane Austen', '978-0141439518', 'Classic', 1813, 'Elizabeth Bennet and Mr. Darcy in this classic romance.', 3, 3),
('Where the Crawdads Sing', 'Delia Owens', '978-0735219090', 'Fiction', 2018, 'A young woman raised in the marshes becomes a murder suspect.', 5, 5),

('The Housemaid', 'Freida McFadden', '978-1804050000', 'Thriller', 2022, 'A domestic thriller about a housemaid with a dark past working for a wealthy family.', 4, 4),
('It Ends With Us', 'Colleen Hoover', '978-1501110368', 'Romance', 2016, 'A heartbreaking story about love, abuse, and difficult choices we make.', 6, 6),
('The Psychology of Money', 'Morgan Housel', '978-0857197689', 'Self-Help', 2020, 'Timeless lessons on wealth, greed, and happiness from one of the great financial writers.', 5, 5),
('Fourth Wing', 'Rebecca Yarros', '978-1649374042', 'Fantasy', 2023, 'A young woman enters a deadly war college where dragons choose their riders.', 3, 3),
('Lessons in Chemistry', 'Bonnie Garmus', '978-0385549400', 'Fiction', 2022, 'A brilliant chemist in the 1960s becomes an unlikely TV cooking show host.', 4, 4),
('The Woman in the Window', 'A.J. Finn', '978-0062678416', 'Thriller', 2018, 'An agoraphobic woman believes she has witnessed a murder across the street.', 3, 3),
('Sapiens', 'Yuval Noah Harari', '978-0062316097', 'History', 2011, 'A brief history of humankind from the Stone Age to the modern era.', 5, 5),
('The Great Gatsby', 'F. Scott Fitzgerald', '978-0743273565', 'Classic', 1925, 'A tragic story of the wealthy and mysterious Jay Gatsby.', 4, 4),
('Verity', 'Colleen Hoover', '978-1538724736', 'Thriller', 2018, 'A struggling writer is hired to complete the remaining books of a famous author.', 5, 5),
('The Song of Achilles', 'Madeline Miller', '978-0062060624', 'Fantasy', 2011, 'A retelling of the Iliad from the perspective of Patroclus.', 3, 3),

('Normal People', 'Sally Rooney', '978-0571334650', 'Fiction', 2018, 'A story of mutual fascination and love between two teenagers from different backgrounds.', 4, 4),
('The 48 Laws of Power', 'Robert Greene', '978-0140280197', 'Self-Help', 1998, 'A guide to understanding and using power in daily life and business.', 2, 2),
('Dune Messiah', 'Frank Herbert', '978-0593098233', 'Science Fiction', 1969, 'Paul Atreides faces political and religious challenges after becoming emperor.', 3, 3),
('The Love Hypothesis', 'Ali Hazelwood', '978-0593336823', 'Romance', 2021, 'A fake dating romance between a biology PhD student and a professor.', 5, 5),
('The Mountain Is You', 'Brianna Wiest', '978-1949759228', 'Self-Help', 2020, 'Transforming self-sabotage into self-mastery and personal growth.', 4, 4),
('The Guest List', 'Lucy Foley', '978-0062868930', 'Thriller', 2020, 'A wedding on a remote island turns deadly when a guest is murdered.', 3, 3),
('Circe', 'Madeline Miller', '978-0316556347', 'Fantasy', 2018, 'The story of Circe, the witch from Homer’s Odyssey, in her own voice.', 4, 4),
('The Nightingale', 'Kristin Hannah', '978-1250080400', 'Historical Fiction', 2015, 'Two sisters in France during World War II face impossible choices.', 4, 4),
('The Vanishing Half', 'Brit Bennett', '978-0525536291', 'Fiction', 2020, 'A story about twin sisters who choose very different paths in life.', 3, 3),
('The Push', 'Ashley Audrain', '978-1984881663', 'Thriller', 2021, 'A psychological thriller about motherhood and the dark side of family.', 2, 2),

('The Four Agreements', 'Don Miguel Ruiz', '978-1878424310', 'Self-Help', 1997, 'A practical guide to personal freedom based on ancient Toltec wisdom.', 6, 6),
('The Book Thief', 'Markus Zusak', '978-0375842207', 'Historical Fiction', 2005, 'A story narrated by Death about a girl who steals books during WWII.', 4, 4),
('The House in the Cerulean Sea', 'TJ Klune', '978-1250217288', 'Fantasy', 2020, 'A magical story about a caseworker who visits an orphanage for magical children.', 3, 3),
('The Lincoln Highway', 'Amor Towles', '978-0735222359', 'Fiction', 2021, 'A journey across 1950s America by three young men on an unexpected road trip.', 2, 2),
('The Maid', 'Nita Prose', '978-0593356159', 'Mystery', 2022, 'A hotel maid becomes the main suspect in a murder investigation.', 4, 4),
('The Paris Apartment', 'Lucy Foley', '978-0063039131', 'Thriller', 2022, 'A woman searching for her brother in Paris uncovers dark family secrets.', 3, 3),
('The Dictionary of Lost Words', 'Pip Williams', '978-0593230367', 'Historical Fiction', 2020, 'The story of the women who helped compile the Oxford English Dictionary.', 2, 2),
('The Invisible Life of Addie LaRue', 'V.E. Schwab', '978-0765387561', 'Fantasy', 2020, 'A young woman makes a Faustian bargain to live forever but is forgotten by everyone.', 5, 5),
('Pachinko', 'Min Jin Lee', '978-1455563920', 'Fiction', 2017, 'A sweeping saga following a Korean family through four generations.', 3, 3),
('The Covenant of Water', 'Abraham Verghese', '978-0802162175', 'Fiction', 2023, 'A sweeping multigenerational family saga set in Kerala, India.', 2, 2),

('The 7 Habits of Highly Effective People', 'Stephen R. Covey', '978-1982137274', 'Self-Help', 1989, 'A classic self-improvement book about personal and professional effectiveness.', 5, 5),
('Atomic Habits (Deluxe Edition)', 'James Clear', '978-0593189641', 'Self-Help', 2018, 'Special collector’s edition of the bestselling habit book.', 0, 0),
('The Silent Patient (Collector’s Edition)', 'Alex Michaelides', '978-1250301697', 'Thriller', 2022, 'Limited collector’s edition with bonus material.', 0, 0),
('Dune (Special Edition)', 'Frank Herbert', '978-0441172719', 'Science Fiction', 1965, 'Special edition of the classic science fiction novel.', 1, 1),
('The Midnight Library (Limited)', 'Matt Haig', '978-0525559474', 'Fiction', 2020, 'Limited edition with exclusive content.', 0, 0),
('It Ends With Us (Special Edition)', 'Colleen Hoover', '978-1501110368', 'Romance', 2016, 'Special edition of the popular romance novel.', 2, 2),
('The Housemaid (Hardcover)', 'Freida McFadden', '978-1804050000', 'Thriller', 2022, 'Hardcover edition of the bestselling thriller.', 3, 3),
('Fourth Wing (Deluxe Edition)', 'Rebecca Yarros', '978-1649374042', 'Fantasy', 2023, 'Deluxe edition with sprayed edges and bonus content.', 2, 2),
('The Alchemist (25th Anniversary)', 'Paulo Coelho', '978-0062315007', 'Fiction', 2014, '25th Anniversary Edition with new foreword.', 4, 4),
('Sapiens (Illustrated Edition)', 'Yuval Noah Harari', '978-0062316097', 'History', 2022, 'Illustrated edition with beautiful visuals.', 3, 3);

-- Set 10 books as FEATURED
UPDATE books SET is_featured = 1 
WHERE title IN (
  'Dune', 
  'Atomic Habits', 
  'The Silent Patient', 
  'Educated', 
  'Project Hail Mary',
  'The Midnight Library',
  'Sapiens',
  'The Alchemist',
  'The Psychology of Money',
  'Thinking, Fast and Slow'
);

-- Set 10 books as POPULAR
UPDATE books SET is_popular = 1 
WHERE title IN (
  'The Seven Husbands of Evelyn Hugo', 
  'It Ends With Us', 
  'The Song of Achilles', 
  'Circe', 
  'Normal People',
  'The Vanishing Half',
  'Pachinko',
  'The House of the Spirits',
  'The Night Circus',
  'Where the Crawdads Sing'
);
-- BORROWINGS
INSERT INTO borrowings (user_id, book_id, borrowed_date, due_date, status, renewal_count) VALUES
(1, 1, '2026-05-20', '2026-06-03', 'Borrowed', 0),
(1, 3, '2026-05-22', '2026-06-05', 'Borrowed', 0),
(2, 5, '2026-05-15', '2026-05-29', 'Overdue', 1),
(2, 7, '2026-05-10', '2026-05-24', 'Returned', 0);

-- BORROWED_BOOKS
INSERT INTO borrowed_books (user_id, user_name, book_id, book_title, author, borrow_date, due_date, status, has_penalty) VALUES
(1, 'Angeline Chiu', 1, 'The Silent Patient', 'Alex Michaelides', '2026-05-20', '2026-06-03', 'Borrowed', 0),
(1, 'Angeline Chiu', 3, 'The Midnight Library', 'Matt Haig', '2026-05-22', '2026-06-05', 'Borrowed', 0),
(2, 'Sarah Tan', 5, 'Dune', 'Frank Herbert', '2026-05-15', '2026-05-29', 'Overdue', 1);

-- ROOMS (Updated with your design)
INSERT INTO rooms (name, capacity, location, facilities, is_featured) VALUES
('The Rose Study', 4, 'Level 2', 'WiFi, Whiteboard, AC', 0),
('The Garden Room', 6, 'Level 2', 'Projector, Printer, AC', 0),
('The Silent Room', 2, 'Level 2', 'WiFi, Power Outlets', 0),
('The Focus Pod', 3, 'Level 2', 'Charging Station, AC', 0),
('Quiet Corner', 2, 'Level 3 (Quiet Zone)', 'Noise-Cancelling, Desk Lamp', 1),
('The Study Pod', 3, 'Level 3 (Quiet Zone)', 'Charging Station, AC', 0),
('The Creative Corner', 4, 'Level 3 (Quiet Zone)', 'WiFi, Whiteboard, Projector', 0);

-- ROOM_BOOKINGS
INSERT INTO room_bookings (user_id, user_name, room_id, room_name, start_time, end_time, status) VALUES
(1, 'Angeline Chiu', 1, 'The Rose Study', '2026-06-07 10:00:00', '2026-06-07 12:00:00', 'Active'),
(2, 'Sarah Tan', 2, 'The Garden Room', '2026-06-08 14:00:00', '2026-06-08 16:00:00', 'Active'),
(3, 'Michael Wong', 5, 'Quiet Corner', '2026-06-09 09:00:00', '2026-06-09 11:00:00', 'Active');

-- ANNOUNCEMENTS
INSERT INTO announcements (title, message, type, due_date, is_published, created_by) VALUES
('Library Extended Hours', 'The library will be open until 10 PM during exam week (10 - 20 June 2026).', 'Notice', '2026-06-20', 1, 3),
('New Book Arrival', 'We have added many new titles including "Fourth Wing".', 'Notice', NULL, 1, 3);

-- EVENTS
INSERT INTO events (title, event_date, event_time, description) VALUES
('Book Club: The Silent Patient', '2026-06-15', '14:00:00', 'Monthly book club discussion.'),
('Study Skills Workshop', '2026-06-12', '15:00:00', 'Learn effective study techniques for exams.');

-- FEEDBACK
INSERT INTO feedback (user_id, name, email, type, message, rating) VALUES
(1, 'Angeline Chiu', 'angeline@example.com', 'Praise', 'The new book collection is amazing!', 5.0),
(2, 'Sarah Tan', 'sarah@example.com', 'Complaint', 'Had trouble booking a room.', 3.0);


-- ==================== SWEETHEART LIBRARY - FULL TEST DATA ====================

-- Admin User (password: 123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin Test', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Normal User (password: 123)
INSERT INTO users (name, email, password, role) VALUES 
('John Doe', 'user@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Books
INSERT INTO books (title, author, isbn, category, year, available_copies, total_copies, is_featured, is_popular) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 'Classic', 1925, 4, 5, 1, 1),
('To Kill a Mockingbird', 'Harper Lee', '9780061120084', 'Classic', 1960, 3, 3, 1, 0),
('1984', 'George Orwell', '9780451524935', 'Dystopian', 1949, 5, 5, 0, 1),
('Atomic Habits', 'James Clear', '9780735211292', 'Self-Help', 2018, 2, 2, 1, 1);

-- Rooms
INSERT INTO rooms (name, capacity, facilities) VALUES
('Meeting Room A', 8, 'Projector, Whiteboard, Air Conditioner'),
('Study Room 1', 4, 'Whiteboard, WiFi, Power Socket'),
('Discussion Room', 10, 'Projector, Conference Table, AC');

-- Events
INSERT INTO events (title, event_date, event_time, description) VALUES
('Library Orientation 2026', '2026-06-18', '10:00:00', 'Learn how to use the library effectively.'),
('Monthly Book Club', '2026-06-25', '15:00:00', 'Discussion on "Atomic Habits" by James Clear.');

-- Announcements
INSERT INTO announcements (title, message, type) VALUES
('System Maintenance', 'The system will be under maintenance on 15 June 2026 from 2AM - 4AM.', 'Maintenance'),
('New Books Arrived', 'We have added 30 new books this month. Check them out!', 'Notice');

-- ==================== OVERDUE + SEND REMINDER TEST DATA ====================

-- Borrowed book that is OVERDUE (for Send Reminder test)
INSERT INTO borrowed_books (user_id, book_id, borrow_date, due_date, status) VALUES
(2, 1, '2026-05-20', '2026-06-03', 'Overdue');   -- Overdue book

-- Normal borrowed book (not overdue)
INSERT INTO borrowed_books (user_id, book_id, borrow_date, due_date, status) VALUES
(2, 2, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Borrowed');

-- Room Booking (Active)
INSERT INTO room_bookings (user_id, room_name, start_time, end_time, status) VALUES
(2, 'Meeting Room A', '2026-06-12 10:00:00', '2026-06-12 12:00:00', 'Active');

-- Feedback (for admin to view)
INSERT INTO feedback (name, email, type, message, rating, user_id) VALUES
('John Doe', 'user@test.com', 'Suggestion', 'Please add more self-help books.', 4, 2);