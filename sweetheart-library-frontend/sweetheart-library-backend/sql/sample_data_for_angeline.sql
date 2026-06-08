







SET @user_email = 'angeline@example.com';


SELECT id, name, email FROM users WHERE email = @user_email;





INSERT INTO room_bookings (user_id, room_name, start_time, end_time, status, location) VALUES
((SELECT id FROM users WHERE email = @user_email LIMIT 1), 'The Rose Study', '2026-06-05 14:00:00', '2026-06-05 16:00:00', 'confirmed', '2nd Floor'),
((SELECT id FROM users WHERE email = @user_email LIMIT 1), 'The Garden Room', '2026-06-03 10:00:00', '2026-06-03 12:00:00', 'completed', 'Ground Floor'),
((SELECT id FROM users WHERE email = @user_email LIMIT 1), 'Quiet Corner', '2026-06-07 09:00:00', '2026-06-07 11:00:00', 'confirmed', '3rd Floor');





INSERT INTO borrowed_books (user_id, book_title, author, borrow_date, due_date, status) VALUES
((SELECT id FROM users WHERE email = @user_email LIMIT 1), 'The Silent Patient', 'Alex Michaelides', '2026-05-20', '2026-06-10', 'Borrowed'),
((SELECT id FROM users WHERE email = @user_email LIMIT 1), 'Educated', 'Tara Westover', '2026-05-25', '2026-06-08', 'Borrowed'),
((SELECT id FROM users WHERE email = @user_email LIMIT 1), 'Dune', 'Frank Herbert', '2026-05-10', '2026-05-28', 'Overdue');





SELECT 'Room Bookings for angeline@example.com:' AS info;
SELECT * FROM room_bookings 
WHERE user_id = (SELECT id FROM users WHERE email = @user_email LIMIT 1)
ORDER BY start_time DESC;

SELECT 'Borrowed Books for angeline@example.com:' AS info;
SELECT * FROM borrowed_books 
WHERE user_id = (SELECT id FROM users WHERE email = @user_email LIMIT 1)
ORDER BY due_date ASC;

