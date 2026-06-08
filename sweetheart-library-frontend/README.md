# SweetHeartLibrary - Complete Local Deployment Guide for Lecturers

**Full-Stack Library Management System**  
Vue 3 Frontend + PHP Backend + MySQL Database

**Student:** Angeline Hui Lii Chiu (104383462)  
**University:** Swinburne University of Technology Sarawak Campus

---

## Table of Contents
1. Project Overview & Features
2. Prerequisites (Detailed)
3. Step-by-Step Deployment (XAMPP Recommended)
4. Verifying Everything Works
5. Login Credentials & Creating Test Accounts
6. Recommended Testing Flow for Review
7. Troubleshooting (Common Issues & Solutions)
8. Alternative Setup Methods
9. Project Structure & Technical Notes
10. Contact & Support

---

## 1. Project Overview & Features

SweetHeartLibrary is a modern web application that allows:
- **Users** to browse books, borrow books, book study rooms, view announcements, submit feedback, and manage their profile & booking history.
- **Admins** to manage announcements, view feedback, and access an admin dashboard.

**Main Features (from the code):**
- User registration & login with role-based access (user / admin)
- Book catalog with details and borrowing
- Study room booking system with history
- Announcements (public + personal reminders)
- Events and Feedback modules
- Admin panel for managing content
- Responsive design with elegant beige & soft pink theme

**Technologies Used:**
- **Frontend:** Vue 3 (Composition API) + Vite + Vue Router + Pinia + Axios + Bootstrap 5 + vue3-toastify
- **Backend:** PHP 8+ with PDO + MySQL
- **Database:** MySQL (database name: `SHL`)

---

## 2. Prerequisites (Detailed)

Before starting, ensure you have the following installed on your laptop:

### Required Software
| Software       | Version          | Purpose                          | Download Link                  |
|----------------|------------------|----------------------------------|--------------------------------|
| **XAMPP**      | Latest (8.x)     | Apache + MySQL + phpMyAdmin      | https://www.apachefriends.org  |
| **Node.js**    | v20.19+ or v22+  | Run the Vue 3 frontend           | https://nodejs.org             |
| **Git**        | Any recent       | Clone the repository (optional)  | https://git-scm.com            |
| **Browser**    | Chrome / Edge    | Best developer tools             | -                              |

### System Requirements
- At least **4GB RAM** (8GB recommended)
- Ports **80** (Apache), **3306** (MySQL), and **5173** (Vite) must be free
- If port 80 is occupied (e.g., by Skype, IIS), stop those services or change Apache port in XAMPP

### Important Notes Before Starting
- You **must** use **XAMPP** (or similar) for the PHP backend.
- The frontend runs separately using Vite's development server.
- The backend folder **must** be named exactly `sweetheart-library-backend` inside your `htdocs` folder.

---

## 3. Step-by-Step Deployment (XAMPP Recommended)

### Step 1: Download the Project

**Recommended: Using Git**
```bash
git clone https://github.com/angelinechiu/SweetHeartLibrary.git
cd SweetHeartLibrary
```

**Alternative: Download ZIP**
1. Visit: https://github.com/angelinechiu/SweetHeartLibrary
2. Click the green **Code** button → **Download ZIP**
3. Extract the ZIP to a folder you can easily access (e.g., `Desktop/SweetHeartLibrary`)

> **Note:** The backend PHP code is located inside:  
> `SweetHeartLibrary/sweetheart-library-frontend/sweetheart-library-backend`

### Step 2: Set Up the Backend (PHP + MySQL)

#### 2.1 Start XAMPP
1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache** and **MySQL**
3. Make sure both show green "Running" status

#### 2.2 Copy Backend Folder to htdocs
1. Go to your extracted project folder
2. Navigate to: `sweetheart-library-frontend/sweetheart-library-backend`
3. **Copy** the entire `sweetheart-library-backend` folder
4. Paste it into your XAMPP `htdocs` folder

   **Correct final location example (Windows):**
   ```
   C:\xampp\htdocs\sweetheart-library-backend
   ```

   **macOS example:**
   ```
   /Applications/XAMPP/xamppfiles/htdocs/sweetheart-library-backend
   ```

   **Critical:** The folder **must** be named exactly `sweetheart-library-backend` (no extra text, no capital letters difference).

#### 2.3 Create the Database
1. Open your browser and go to: **http://localhost/phpmyadmin**
2. Click **New** on the left sidebar
3. Create a database named exactly: **`SHL`**
4. Click **Create**

#### 2.4 Import Database Files
You need to import in this order:

1. **Full Schema (CREATE TABLE statements)** — 
   - This creates all necessary tables: `users`, `books`, `rooms`, `announcements`, `room_bookings`, `borrowed_books`, `feedback`, etc.

2. **Sample Data**
   - Import the file:
     ```
     sweetheart-library-frontend/sweetheart-library-backend/sql/SHL.sql
     ```
   - This adds sample room bookings and borrowed books linked to `angeline@example.com`

#### 2.5 Configure Database Connection (if needed)
1. Open the file:
   ```
   C:\xampp\htdocs\sweetheart-library-backend\config\db.php
   ```
2. By default it uses:
   ```php
   $host = 'localhost';
   $dbname = 'SHL';
   $username = 'root';
   $password = '';          // Empty for default XAMPP
   ```
3. Only change `$password` if you have set a password for the `root` user in XAMPP.



### Step 3: Set Up the Frontend (Vue 3 + Vite)

1. Open **Terminal**, **Command Prompt**, or **PowerShell**

2. Navigate to the frontend folder:
   ```bash
   cd path\to\SweetHeartLibrary\sweetheart-library-frontend
   ```
   Example:
   ```bash
   cd C:\Users\YourName\Desktop\SweetHeartLibrary\sweetheart-library-frontend
   ```

3. Install all required packages:
   ```bash
   npm install
   ```
   (This may take 1–3 minutes the first time)

4. Start the development server:
   ```bash
   npm run dev
   ```

5. You will see output like:
   ```
   VITE v5.x.x  ready in  xxx ms

   ➜  Local:   http://localhost:5173/
   ➜  Network: use --host to expose
   ```
   **Open the link shown** (usually `http://localhost:5173`)

> **Do not close the terminal** while using the app. Keep it running.

---

### Step 4: Access and Use the Application

1. Make sure **XAMPP Apache and MySQL are running**
2. Make sure the **frontend terminal** shows `npm run dev` is active
3. Open your browser and go to: **http://localhost:5173**
4. You should see the SweetHeartLibrary homepage with navigation bar

**Do not** try to open the `index.html` file directly from the folder — it must run through the Vite dev server.

---

## 4. Verifying Everything Works

### Quick Verification Checklist

| Component       | How to Verify                                      | Expected Result                     |
|-----------------|----------------------------------------------------|-------------------------------------|
| Apache          | http://localhost                                   | XAMPP dashboard or blank page       |
| phpMyAdmin      | http://localhost/phpmyadmin                        | Login page (no password usually)    |
| Database `SHL`  | phpMyAdmin → Databases                             | `SHL` database exists               |
| Frontend        | http://localhost:5173                              | Beautiful homepage loads            |
| API Connection  | Open browser DevTools (F12) → Console              | No CORS or network errors           |

---

## 5. Login Credentials & Creating Test Accounts

### Method 1: Create Accounts via the Website (Recommended)

1. On the website, click **Register**
2. Fill in name, email, and password
3. After successful registration, you can log in immediately

### Method 2: Create an Admin Account

1. Register a normal account through the website
2. Go to **phpMyAdmin** → Database `SHL` → Table `users`
3. Find the row of the user you just created
4. Change the value in the `role` column from `user` to `admin`
5. Save the change
6. Log out and log back in on the website — you will now have admin privileges

### Sample / Pre-loaded Data
- Email: `angeline@example.com` has sample room bookings and borrowed books already in the database.
- You can register with this email (the system will create the user if it doesn't exist, or you can manually add the user first).

### Suggested Test Accounts

| Role   | Email                     | Password      | How to Create                     |
|--------|---------------------------|---------------|-----------------------------------|
| Admin  | `admin@example.com`       | `password`    | Register normally                 |
| User   | `angeline@example.com`    | `password`    | Register normally                 |
| Sample | `angeline@example.com`    | `password`    | Register normally                 |

> **Security Note:** All passwords are hashed using PHP's `password_hash()`. Never store plain text passwords.

---

## 6. Recommended Testing Flow for Review (Detailed)

This section provides **detailed explanations** of what you should see and test on each page. Use this as a checklist to evaluate the quality of the implementation.

### Homepage (Landing Page) – First Impression

When you first visit **http://localhost:5173**, you land on the **Home page**.

**Expected UI & Content:**
- Clean, elegant hero/welcome section with the app name “SweetHeartLibrary” and a short tagline about the digital library experience.
- Prominent call-to-action buttons (e.g., “Browse Books”, “Book a Room”, “Get Started”, or “Login/Register”).
- Quick feature highlights or cards showing the main functionalities (Book Borrowing, Room Booking, Announcements, etc.).
- Navigation bar (Header) should be visible and consistent across all pages. It typically includes:
  - Logo
  - Links to: Home, Books, Rooms, Announcements, Events, Feedback
  - Login / Register buttons (when logged out)
  - User avatar / Profile / Logout (when logged in)
- Footer with basic information and links.

**What to Check:**
- Visual design quality and consistency with the soft beige + dusty rose theme.
- Responsiveness (test on mobile view using browser DevTools).
- Whether the header/footer are properly shared across pages (they come from `AppHeader.vue` and `AppFooter.vue`).
- Any dynamic content (e.g., personalized greeting if already logged in).
- Overall first impression and ease of navigation.

**Tip:** The Homepage sets the tone for the entire application. A good homepage should feel welcoming, professional, and clearly communicate the purpose of the system.

---

### Normal User Flow (Detailed)

#### 1. Register a New Account
- Go to the **Register** page (usually linked from the header or login page).
- Fill in the form: Full Name, Email, Password, and Confirm Password.
- Submit the form.
- **Expected behavior**: 
  - Success message or toast notification appears.
  - User is automatically logged in or redirected to the Login page.
  - Data is saved securely in the database (password should be hashed).
- **What to check**: Form validation (empty fields, password mismatch, invalid email), error messages, and successful registration.

#### 2. Login
- Go to the **Login** page.
- Enter email and password of a registered account.
- Click Login.
- **Expected behavior**:
  - Successful login redirects to Dashboard or Home.
  - Navigation bar updates to show user-specific links (Profile, My Bookings, Logout).
  - Token and role are stored (visible in browser DevTools → Application → Local Storage).
- **What to check**: Error handling for wrong credentials, loading state, and redirect after login.

#### Forgot Password & Reset Password Flow
- From the **Login** page, click on the **"Forgot Password?"** link.
- **Forgot Password page**:
  - Simple form asking for the user’s registered email address.
  - Submit button.
- After submitting:
  - A success message should appear (e.g., “If an account exists with this email, a reset link has been sent”).
  - In a real system, an email with a reset token/link would be sent. In this local setup, the backend may simulate this or return a token directly.
- **Reset Password page** (usually accessed via a link containing a token):
  - Form with two fields: **New Password** and **Confirm New Password**.
  - Submit button.
- **Expected behavior**:
  - Password is updated securely in the database (hashed).
  - User can log in with the new password.
  - Proper validation (password strength, matching confirmation).
- **What to check**:
  - Form validation and error messages.
  - Success handling and redirect to Login page after reset.
  - Security (old password no longer works).

#### 3. Books → Browse Catalog → View Details → Borrow a Book → Confirmation
- Navigate to **Books** (Book Catalog).
- **Expected UI**: Grid or list of books with cover images, title, author, and availability status. Search or filter functionality if implemented.
- Click on any book card.
- **Book Details page**:
  - Shows full book information (description, ISBN, category, availability).
  - "Borrow" or "Reserve" button should be visible if available.
- Click **Borrow**.
- **Expected behavior**:
  - Confirmation modal or page appears.
  - Borrowing record is created in the database.
  - User sees success message and is redirected to **Booking Confirmation** page.
- **What to check**: 
  - Book availability updates after borrowing.
  - Confirmation page shows booking details (book title, borrow date, due date).
  - Proper error handling if the book is already borrowed.

#### 4. Rooms → Book a Study Room
- Go to **Rooms** (Room Booking page).
- **Expected UI**: Calendar or list of available rooms with time slots, capacity, and features.
- Select a room, date, and time slot.
- Fill in any additional details (purpose, number of people).
- Submit the booking.
- **Expected behavior**:
  - Booking is saved.
  - Success notification appears.
  - User is redirected or can view the booking in **My Bookings**.
- **What to check**: 
  - Conflict detection (cannot book overlapping times).
  - Form validation.
  - Clear display of available vs booked slots.

#### 5. My Bookings or History → Verify Bookings Appear
- Go to **My Bookings** or **History**.
- **Expected UI**: List or cards showing all current and past bookings (both book borrowing and room bookings).
- Each item should show: Item name, date/time, status (Active, Completed, Cancelled), and action buttons (Cancel if applicable).
- **What to check**:
  - Data consistency with what was just booked.
  - Proper status badges/colors.
  - Ability to cancel upcoming bookings.
  - Pagination or infinite scroll if there are many records.

#### 6. Announcements → View Current Announcements
- Go to **Announcements**.
- **Expected UI** (based on current code):
  - Clean card layout with title, message, type badge (Important, General, etc.), creation date, and optional due date.
  - Personal reminders may show a special badge if targeted to the logged-in user.
  - Loading spinner while fetching data.
  - Empty state message if no announcements.
- **Admin-only feature**: "Manage" button visible only when logged in as admin.
- **What to check**:
  - Announcements load correctly from the backend.
  - Responsive design on mobile.
  - Badge colors are appropriate for announcement types.

#### 7. Feedback → Submit Feedback
- Go to **Feedback** page.
- Fill in the feedback form (rating, comments, category if available).
- Submit.
- **Expected behavior**:
  - Success toast/notification.
  - Feedback is saved to the database.
  - Form resets or shows thank-you message.
- **What to check**: Form validation, loading state during submission, and whether feedback appears in the Admin Feedback section later.

#### 8. Profile → View or Update Information
- Go to **Profile**.
- **Expected UI**: Display current user information (name, email, role, joined date).
- Option to edit and update details (name, password change, etc.).
- **What to check**:
  - Data is pre-filled correctly from the logged-in user.
  - Update functionality works and persists after refresh.
  - Password change (if implemented) follows security best practices.

#### 9. Events → View Current / Upcoming Events
- Go to **Events** page.
- **Expected UI**: List or calendar view of events with title, date/time, location/description, and status.
- **What to check**:
  - Events load from the backend.
  - Good visual presentation (cards, dates highlighted).
  - Any registration or "Interested" functionality if available.

---

### Admin Flow (Detailed)

#### 1. Create an Admin Account
- Follow the same registration process as a normal user.
- After registration, go to phpMyAdmin → `SHL` database → `users` table.
- Change the `role` value of the new user from `'user'` to `'admin'`.
- Log out and log back in.

#### 2. Login as Admin
- After logging in as admin, the navigation or dashboard should reflect admin privileges (extra menu items or different dashboard).

#### 3. Access Admin Dashboard (`/admin`)
- Navigate to **Admin Dashboard**.
- **Expected content**:
  - Overview statistics (total users, total books, active bookings, pending feedback, etc.).
  - Quick links to manage different sections.
  - Possibly charts or recent activity.
- **What to check**: Data accuracy, clean layout, and useful summary information for an administrator.

#### 4. Go to Admin Feedback
- Navigate to **Admin Feedback** (usually at `/admin/feedback`).
- **Expected UI**: Table or list of all feedback submitted by users.
- Columns may include: User name/email, feedback content, rating, date submitted, and status.
- **What to check**:
  - All feedback from normal users appears here.
  - Ability to mark as read, reply, or delete (if implemented).
  - Good filtering or search functionality.

#### 5. From Announcements Page, Click "Manage"
- Go to the public **Announcements** page while logged in as admin.
- A **"Manage"** button should appear (this is role-protected).
- Clicking it should redirect to the Admin area for creating/editing/deleting announcements.
- **What to check**:
  - The button is only visible to admins (hidden for normal users).
  - Admin can create new announcements with type, title, message, due date, and target audience (all users or specific user).
  - Changes reflect immediately on the public Announcements page.

---

## 7. Troubleshooting (Common Issues & Solutions)

### Issue: "Database connection failed"
**Solutions:**
- Make sure MySQL is running in XAMPP
- Confirm database `SHL` exists
- Check `db.php` credentials (especially password)
- Restart Apache and MySQL after changes

### Issue: 404 Not Found on API calls
**Solutions:**
- Confirm the backend folder is named **exactly** `sweetheart-library-backend` in `htdocs`
- Restart Apache after copying the folder
- Check that you are accessing via `http://localhost/sweetheart-library-backend/api/...` (not `https`)

### Issue: Frontend shows blank page or errors
**Solutions:**
- Make sure `npm run dev` is still running in the terminal
- Check the terminal for error messages (red text)
- Clear browser cache (Ctrl + Shift + R)
- Confirm you are visiting `http://localhost:5173` (not a file path)

### Issue: CORS errors in browser console
**Solutions:**
- The `db.php` file already includes CORS headers
- Restart Apache
- Make sure you are not accessing the frontend via `file://`

### Issue: Port 5173 already in use
**Solution:**
Vite will automatically suggest another port. Use the new URL it provides.

### Issue: "This site can’t be reached" / localhost refused to connect
**Solutions:**
- XAMPP Apache is not running
- Another program is using port 80 (stop Skype, IIS, or change Apache port)
- Try accessing `http://127.0.0.1` instead of `localhost`

---

## 8. Alternative Setup Methods

### Option A: PHP Built-in Server (No XAMPP)
Advanced users can run:
```bash
cd sweetheart-library-backend
php -S localhost:8000 -t .
```
Then update the `baseURL` in `frontend/src/services/api.js` to `http://localhost:8000/api`


## 9. Project Structure & Technical Notes

```
SweetHeartLibrary/
├── sweetheart-library-frontend/          ← Main frontend folder (Vue 3 + Vite)
│   └── ... (see detailed structure below)
│
├── .git/                                 ← Git version control
├── .gitignore
├── README.md                             ← Main project README (to be updated with lecturer deployment guide)
└── (other root config files if any)
```

---

## Frontend Structure (`sweetheart-library-frontend/`)

```
sweetheart-library-frontend/
├── .vscode/
│   ├── extensions.json
│   └── settings.json
│
├── dist/                                 ← Production build output (generated by `npm run build`)
├── node_modules/                         ← Installed dependencies (generated by `npm install`)
│
├── public/
│   ├── dummydata.sql
│   ├── favicon.ico
│   
│
├── src/
│   ├── assets/
│   │   └── images/
│   │       ├── logo.png
│   │       └── profile-avatar.png
│   │
│   ├── components/
│   │   ├── AppFooter.vue
│   │   ├── AppHeader.vue
│   │   ├── BookCard.vue
│   │   └── LoadingSpinner.vue
│   │
│   ├── router/
│   │   └── index.js                      ← Vue Router configuration + navigation guards
│   │
│   ├── services/
│   │   └── api.js                        ← Centralized Axios instance + API base URL
│   │
│   ├── stores/                           ← Pinia state management
│   │   └── auth.js                       ← Authentication store (user, token, role)
│   │
│   ├── views/                            ← All page components (lazy-loaded)
│   │   ├── AdminDashboardView.vue
│   │   ├── AdminFeedbackView.vue
│   │   ├── AnnouncementsView.vue
│   │   ├── BookCatalogView.vue
│   │   ├── BookDetailsView.vue
│   │   ├── BookingConfirmationView.vue
│   │   ├── BookingFormView.vue
│   │   ├── DashboardView.vue
│   │   ├── EventsView.vue
│   │   ├── FeedbackView.vue
│   │   ├── ForgotPasswordView.vue
│   │   ├── HistoryView.vue
│   │   ├── HomeView.vue
│   │   ├── LoginView.vue
│   │   ├── MyBookingsView.vue
│   │   ├── ProfileView.vue
│   │   ├── RegisterView.vue
│   │   ├── ResetPasswordView.vue
│   │   ├── RoomBookingView.vue
│   │   └── RoomHistoryView.vue
│   │
│   ├── App.vue                           ← Root component (Header + Router View + Footer + global styles)
│   ├── main.js                           ← App entry point (Vue, Pinia, Router, Bootstrap, Toast)
│   └── style.css                         ← Global styles
│
├── sweetheart-library-backend/           ← PHP Backend (nested inside frontend for convenience)
│   └── ... (see backend structure below)
│
├── index.html
├── jsconfig.json
├── package.json
├── package-lock.json
├── vite.config.js
├── eslint.config.js
├── .oxlintrc.json
├── .prettierrc.json
├── .editorconfig
├── .gitattributes
└── .gitignore
```

---

## Backend Structure (`sweetheart-library-frontend/sweetheart-library-backend/`)

```
sweetheart-library-backend/
├── api/                                  ← All PHP API endpoints
│   ├── admin.php
│   ├── announcements.php
│   ├── auth.php
│   ├── bookings.php
│   ├── books.php
│   ├── borrowings.php
│   ├── events.php
│   ├── feedback.php
│   ├── room-bookings.php
│   ├── rooms.php
│   └── users.php
│
├── config/
│   └── db.php                            ← Database connection (PDO) + CORS headers
│
├── sql/
│   ├── dummydata.sql
│   └── SHL.sql                           ← Database schema + sample data

```

---


**Key Technical Points:**
- The frontend calls the backend using Axios with this base URL:
  ```js
  baseURL: 'http://localhost/sweetheart-library-backend/api'
  ```
- All API responses are JSON.
- Authentication uses Bearer tokens stored in `localStorage`.
- Role-based access is enforced both on frontend (router guards) and backend.

---

the developer:

**Angeline Hui Lii Chiu**  
Student ID: **104383462**  
Swinburne University of Technology Sarawak Campus  

---

*README prepared for lecturer review – June 2026*
