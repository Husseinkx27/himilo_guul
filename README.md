# HimiloGuul — Project Specification

> Short description: HimiloGuul allows sellers to list running businesses for sale and potential buyers to contact them. The entire site is PHP + MySQL-based and reads all contents from the database. ✅

## Project goals

- Implement a full PHP/MySQL project demonstrating CRUD operations (SELECT, INSERT, UPDATE, DELETE).
- Use cookies and sessions for authentication and access control.
- Validate forms (client + server side) and practice secure coding (prepared statements, password hashing, CSRF protection).
- Create a well-styled site using an external stylesheet.

## High-level pages & features 📄

1. **Public Home (index.php)**
   - Header, horizontal navigation, left sidebar, main content area, footer.
   - Public listings of businesses (read from DB) and basic search/filter.
2. **External stylesheet** (`assets/css/style.css`) for consistent styling.
3. **Database connection** (`connection.php`) using PDO and a single config file for DB credentials.
4. **User registration page** (`register.php`)
   - Fields: Auto ID (auto-increment), first name, last name, sex, username, password, phone, email, profile picture upload, role (Buyer, Seller, Admin), status (active, not active), reset & submit.
   - Validate fields; enforce unique username/email.
   - Store password securely with `password_hash()`.
5. **Dashboard / Control Panel (dashboard/)**
   - Access control: session + cookie-based (only for authenticated users). Admins manage all content & users.
   - CRUD management for Users, Businesses, Contacts.
   - Upload manager for business images.
6. **Login (`login.php`)**
   - Fields: username, password, remember me checkbox, sign-up link, forgot password link.
   - Use cookies for "remember me" and sessions for authentication.
7. **Logout (`logout.php`)**
   - Properly destroy session and cookies.
8. **Session timeout**
   - Auto-expire sessions after **5 minutes** of inactivity; show an expiry message and require re-login.
9. **Contact page / Contact model**
   - Users/buyers can request contact with a seller about a business; stored in `contacts` table.

## Database schema (MySQL) 🗄️

Database: `himiloGuul`

Tables and suggested fields (add indexes and constraints as needed):

### `users`

- `id` INT PRIMARY KEY AUTO_INCREMENT
- `first_name` VARCHAR(100) NOT NULL
- `last_name` VARCHAR(100) NOT NULL
- `sex` ENUM('Male','Female','Other') DEFAULT 'Other'
- `username` VARCHAR(50) NOT NULL UNIQUE
- `email` VARCHAR(150) NOT NULL UNIQUE
- `password` VARCHAR(255) NOT NULL -- store hashed password
- `phone` VARCHAR(30) NULL
- `profile_picture` VARCHAR(255) NULL -- path to file
- `role` ENUM('Buyer','Seller','Admin') DEFAULT 'Buyer'
- `status` ENUM('active','not_active') DEFAULT 'active'
- `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

### `businesses`

- `id` INT PRIMARY KEY AUTO_INCREMENT
- `owner_id` INT NOT NULL -- FK to `users(id)`
- `business_name` VARCHAR(255) NOT NULL
- `address` VARCHAR(255) NULL
- `price` DECIMAL(12,2) NOT NULL
- `description` TEXT NULL
- `images` JSON NULL -- array of image paths or reference to images table
- `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `status` ENUM('available','sold','inactive') DEFAULT 'available'

### `contacts`

- `id` INT PRIMARY KEY AUTO_INCREMENT
- `business_id` INT NOT NULL -- FK to `businesses(id)`
- `from_user_id` INT NULL
- `name` VARCHAR(150) NOT NULL
- `email` VARCHAR(150) NOT NULL
- `phone` VARCHAR(30) NULL
- `message` TEXT NULL
- `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `status` ENUM('new','seen','resolved') DEFAULT 'new'

> Note: Use foreign key constraints and ON DELETE behavior appropriate for the app.

## File / Folder structure 🔧

- `index.php` (public home)
- `assets/css/style.css` (external stylesheet)
- `assets/js/` (client-side validation / UI scripts)
- `connection.php` (DB connection)
- `config.php` (site constants, session config)
- `register.php`, `login.php`, `logout.php`, `forgot_password.php`
- `dashboard/index.php` (control panel home)
- `dashboard/users.php`, `dashboard/businesses.php`, `dashboard/contacts.php` (CRUD pages)
- `uploads/` (store images) with .htaccess to restrict execution
- `inc/header.php`, `inc/footer.php`, `inc/sidebar.php` (shared layout includes)
- `lib/auth.php` (authentication helpers)
- `lib/validator.php` (server-side validation helpers)
- `sql/initial_schema.sql` (schema & seed data)

## Authentication & Sessions 🔒

- Use PHP sessions for authenticated areas; call `session_start()` in a central place.
- Regenerate session ID after login to prevent fixation.
- Implement **Remember Me** with a secure cookie (token stored hashed in DB + cookie with token id). Cookie should be flagged `HttpOnly`, `Secure` (if HTTPS), and have reasonable expiration (e.g., 30 days).
- Session inactivity timeout: 5 minutes (300s). Store `last_activity` timestamp in session; auto-logout when expired.
- On logout: unset session vars, destroy session, and invalidate remember-me cookie and DB token.

## Validation & Security ✅

- Use prepared statements (PDO) to prevent SQL injection.
- Sanitize output with `htmlspecialchars()` to prevent XSS.
- CSRF protection: include tokens in forms and validate on submit.
- Passwords: use `password_hash()` / `password_verify()`.
- Validate file uploads: check mime type, size limits (e.g., 2–5MB per image), and store files outside web root or protect upload folder.
- Input validation server-side (and client-side for UX) — required fields, formats (email, phone, price), and length checks.

## Images & Uploads 🖼️

- Store image files in `uploads/` with unique filenames (prefix with user id + timestamp or UUID).
- Optionally store image metadata in DB (`images` JSON or separate `business_images` table with `business_id`, `file_path`, `alt_text`).
- Resize images on upload (create thumbnails) to optimize delivery.

## UI / UX notes ✨

- Consistent header/navigation across pages; include Login/Register for guests and user menu for logged-in users.
- Dashboard should be responsive and clearly show actions: Add business, Edit, Delete, View contacts.
- Show clear validation messages and success/error alerts.

## Implementation notes / best practices 🧭

- Use includes (`inc/header.php`, `inc/footer.php`) to avoid duplication.
- Keep logic and presentation reasonably separated (even if not full MVC).
- Document functions and files with comments.
- Seed the DB with sample users and businesses for demo/testing.

## Testing & Acceptance Criteria ✅

- All CRUD operations for Users, Businesses, Contacts work from the dashboard.
- Public pages show businesses read from the DB.
- Login with session & remember-me works; logout clears session & cookies.
- Session expires after 5 minutes inactivity and prompts re-login.
- Input validation blocks invalid data; file uploads are validated.

## Milestones & Deliverables 📅

1. Create DB schema and seed data (`sql/initial_schema.sql`).
2. Basic public pages and listing (index + DB read operations).
3. User registration + login (session + remember cookie).
4. Dashboard CRUD for businesses and users.
5. Contact form integration and admin view for contacts.
6. Final polish (styling, validation, security hardening, docs).

## Extra notes / instructor alignment

- Ensure the project demonstrates PHP and MySQL concepts clearly.
- All dynamic contents must come from the DB (no hard-coded listings).
- Use comments and proper naming conventions for code readability.

---

## Implementation flow & testing 🔁

### Flow overview

- Registration: Users register via `register.php`. Data is validated server-side (and minimally client-side) and the password is stored with `password_hash()`.
- Login & session: Users authenticate via `login.php`. On success the app regenerates the session ID, stores basic user info in `$_SESSION['user']`, and optionally sets a secure "remember me" cookie (selector:validator token) recorded in the `auth_tokens` table.
- Public listings: `index.php` reads `businesses` and `business_images` to show available listings. `business.php?id=...` shows details and a contact form protected with CSRF tokens.
- Dashboard CRUD: Authenticated users (and Admins) use the dashboard to Create/Read/Update/Delete businesses, users, and contacts. File uploads (images) are validated, stored in `uploads/`, and referenced in `business_images`.
- Contact flow: Contact requests are saved in `contacts` and visible to the seller/admin from the dashboard.
- Security/sessions: Sessions auto-expire after 5 minutes of inactivity; CSRF tokens protect forms; prepared statements avoid SQL injection.

### How to run the seed and smoke tests (local dev)

1. Configure DB credentials in `config.php` (DB_HOST, DB_NAME, DB_USER, DB_PASS). In XAMPP, the default MySQL user might not be empty — set the correct password or create a dedicated DB user with permissions.
2. Generate demo password hashes (optional): `php sql/prepare_seed.php` — this creates `sql/initial_schema_filled.sql` with hashed demo passwords (`admin123`, `seller123`, `buyer123`).
3. Import the seed SQL to create the database and demo data: `php sql/run_seed.php` (this will DROP and recreate the `himiloGuul` DB; do not run on production).
4. Run a basic smoke test script that checks DB connectivity, counts rows, and verifies the demo admin password: `php tests/smoke.php` (script included in the repo).
5. Manually test web flows using your local server (e.g., via XAMPP or `php -S localhost:8000 -t .`): register, login (try "Remember me"), add/edit a business, upload images, and submit contact form.

### Troubleshooting notes

- If `php sql/run_seed.php` fails with "Access denied for user 'root'@'localhost'", update `config.php` with correct DB credentials (user/password) or create a MySQL user matching the current `config.php` settings. The project attempts to connect using credentials from `config.php`.

---

If you'd like, I can now run the seed and smoke tests once you confirm DB credentials, or I can proceed to finish the dashboard features and client-side validation. 💡

### Quick smoke test run (2025-12-26)

- Result: DB connectivity OK. Found **3 users** and **1 business** in the database.
- Admin credential verification using password "admin123": **FAILED** (the admin user exists but the stored hash does not match the expected demo password).
- Next steps: If you want the demo passwords to match, run `php sql/prepare_seed.php` and then import the generated `sql/initial_schema_filled.sql` (or run `php sql/run_seed.php` after configuring DB credentials). Alternatively I can update the admin password directly to a known hash if you prefer.

> Note: During my first attempt to run `php sql/run_seed.php`, it failed with a MySQL access error (Access denied). That means `config.php` requires correct DB credentials before the seed script can be executed from the CLI environment.
