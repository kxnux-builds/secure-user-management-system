# Secure User Management System

Secure User Management System — a simple PHP + MySQL project that demonstrates a minimal, secure user authentication and profile management flow. It includes user registration, login, role-based access (Admin/User), profile display, session management, and basic hardening such as password hashing and prepared statements.

---

## Features

- User registration with email and password
- Login with session handling and session ID regeneration
- Role-based access (Admin / User)
- Profile page with basic profile data
- Secure password storage using PHP's `password_hash` / `password_verify`
- SQL prepared statements to help prevent SQL injection
- Minimal, responsive UI using Tailwind CSS CDN

---

## Tech stack

- PHP (vanilla)
- MySQL / MariaDB
- HTML/CSS (Tailwind CSS via CDN)
- SQL (schema and seed in `setup.sql`)

---

## Repository structure

- `setup.sql` — database schema and seed data (roles)
- `db.php` — database connection
- `register.php` — registration form and handler
- `login.php` — login form and handler
- `logout.php` — session termination and redirect
- `dashboard.php` — protected landing page after login
- `profile.php` — user profile page
- `LICENSE` — project license

Files in this repository:
- [setup.sql](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/setup.sql)
- [db.php](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/db.php)
- [register.php](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/register.php)
- [login.php](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/login.php)
- [logout.php](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/logout.php)
- [dashboard.php](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/dashboard.php)
- [profile.php](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/profile.php)
- [LICENSE](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/LICENSE)

---

## Quickstart / Installation

Prerequisites:
- PHP 7.4+ (or compatible)
- MySQL / MariaDB
- A web server such as Apache or built-in PHP dev server

1. Clone the repository
   - git clone https://github.com/kxnux-builds/secure-user-management-system.git

2. Create the database and tables
   - From your MySQL client or shell run:
     ```
     mysql -u <db_user> -p < setup.sql
     ```
     Or inside mysql:
     ```
     SOURCE setup.sql;
     ```

   The `setup.sql` creates a `user_management` database with `roles` and `users` tables and inserts default roles `Admin` and `User`.

   Reference: [setup.sql](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/setup.sql)

3. Configure the database connection
   - Edit `db.php` and set your DB credentials (host, user, password).
   - Example connection file included in the repo:

```php name=db.php url=https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/db.php
<?php
$host = 'localhost';
$db_user = 'root'; // Change to your DB user
$db_pass = '';     // Change to your DB password
$db_name = 'user_management';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

4. Serve the application
   - Using PHP built-in server (for development):
     ```
     php -S localhost:8000
     ```
   - Then open: http://localhost:8000/register.php to create an account, or http://localhost:8000/login.php to sign in.

---

## Usage

- Registration: `register.php`
  - User provides username, email, password.
  - Passwords are hashed with `password_hash()` before storage.

- Login: `login.php`
  - Uses prepared statement to fetch user by email and `password_verify()` to validate.
  - On successful login, `session_regenerate_id(true)` is called and user data is stored in `$_SESSION`.

- Logout: `logout.php`
  - Calls `session_unset()` and `session_destroy()` then redirects to login.

- Dashboard & Profile
  - `dashboard.php` and `profile.php` are intended to be protected pages that check `$_SESSION['user_id']` / `$_SESSION['role_id']`.

Example pages:
- Registration flow: [register.php](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/register.php)
- Login flow: [login.php](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/login.php)
- Logout: [logout.php](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/logout.php)

---

## Security notes & recommendations

The project demonstrates several secure practices, but for production use apply additional hardening:

- Password storage:
  - Uses `password_hash()` and `password_verify()` (good).
  - Consider enforcing stronger password policies and rate-limiting authentication attempts.

- Database access:
  - Uses prepared statements to avoid SQL injection — keep using prepared statements everywhere you access DB.
  - Do not use the `root` database account for the web application — create a dedicated DB user with least privileges.

- Sessions & cookies:
  - The code regenerates the session ID on login; also consider:
    - Setting session cookie flags: `session_set_cookie_params(['httponly' => true, 'secure' => true, 'samesite' => 'Strict']);`
    - Using HTTPS in production and marking cookies as `secure`.

- Input validation & output escaping:
  - Sanitize and validate all user inputs (the code uses basic email sanitization).
  - Escape output in templates to avoid XSS; use `htmlspecialchars()` or an output-encoding strategy.

- File uploads:
  - If you add profile picture uploads, ensure you validate file types, scan for malicious files, and store them outside the web root or with restricted permissions.

- CSRF protection:
  - Add CSRF tokens to forms to mitigate CSRF attacks.

---

## Database schema (summary)

The `setup.sql` file creates:
- `user_management` database
- `roles` table (role_id, role_name) and inserts `Admin`, `User`
- `users` table (user_id, role_id, username, email, password_hash, profile_picture, created_at)

See the full SQL here: [setup.sql](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/setup.sql)

Snippet from repository:
```sql name=setup.sql url=https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/setup.sql
CREATE DATABASE IF NOT EXISTS user_management;
USE user_management;

CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert default roles
INSERT INTO roles (role_name) VALUES ('Admin'), ('User');

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL DEFAULT 2, -- Default to 'User'
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE
);
```

---

## File descriptions

- `setup.sql` — DB schema + seed roles.
- `db.php` — DB connection settings, modify for your environment.
- `register.php` — registers new users (uses prepared statements).
- `login.php` — authenticates users and sets session vars (regenerates session ID).
- `logout.php` — logs user out.
- `dashboard.php` — shows protected area (ensure session checks).
- `profile.php` — displays and allows updating of user profile (if implemented).

---

## Development & Contribution

This is a simple demonstration repo. If you want to extend or improve it, consider:

- Implementing CSRF tokens for all POST forms
- Adding input validation and richer error handling
- Implementing email verification and password reset flows
- Adding role management UI (Admin)
- Using environment variables for configuration (e.g., with `.env`) instead of committing credentials
- Adding unit or integration tests

If you open a PR, please:
- Describe the change clearly
- Include instructions to test locally
- Add tests for any new logic if possible

---

## License

See the `LICENSE` file in the repo for license details: [LICENSE](https://github.com/kxnux-builds/secure-user-management-system/blob/994a02c9d9b2e22b27184dd8ac68120d060b37e3/LICENSE)

---

## Credits & Links

- Author: Kishanu Mondal
- GitHub: https://github.com/kxnux-builds
- LinkedIn: https://www.linkedin.com/in/kishanu-mondal/
- X (Twitter): https://x.com/Kxnux_Dev

---