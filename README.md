# Felix Construction 🏗️

A full-stack company website for a fictional construction company, built with **PHP and MySQL** as a project for the PHP1 course at Akademija ICT Beograd.

## Features

**Public site**
- Home page with animated counters and video background
- Project gallery loaded from the database, filtered by category
- Contact form with client-side (JavaScript) and server-side validation; messages are saved to the database
- Poll that logged-in users can vote in
- About the author page
- Dynamic navigation menu stored in the database

**User accounts**
- Registration with password hashing (`password_hash`)
- Account activation via email link (PHPMailer + Gmail SMTP)
- Login / logout with sessions and role-based access (`admin` / `user`)

**Admin panel** (`/admin`)
- Dashboard with overview statistics
- Manage projects (create, edit, delete, image upload)
- Manage navigation links
- Read contact messages
- Manage polls and view results

## Tech stack

- PHP 8.2 (PDO, prepared statements)
- MySQL / MariaDB
- Bootstrap 5, HTML5, CSS3, JavaScript
- PHPMailer (via Composer)
- XAMPP for local development

## Getting started (XAMPP)

1. **Clone** the repository into XAMPP's `htdocs` folder, inside a folder named `WEB_SAJTOVI_PHP`:
   ```
   C:\xampp\htdocs\WEB_SAJTOVI_PHP\felix_php
   ```
   (The site currently uses absolute paths starting with `/WEB_SAJTOVI_PHP/felix_php/`.)

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Create the database:** open `http://localhost/phpmyadmin`, create a database named `felix-construction-php`, and import `database/felix-construction-php.sql`.

4. **Configure:** copy `includes/config.example.php` to `includes/config.php` and fill in your database details. For activation emails, add a Gmail address and a [Gmail App Password](https://support.google.com/accounts/answer/185833).

5. Start Apache and MySQL in XAMPP and open:
   ```
   http://localhost/WEB_SAJTOVI_PHP/felix_php/
   ```

## Demo accounts

| Role  | Email             | Password    |
|-------|-------------------|-------------|
| Admin | admin@felix.com   | `Admin123!` |
| User  | user@felix.com    | `User123!`  |

## Project structure

```
├── admin/              # Admin panel (projects, navigation, messages, polls)
├── css/                # Styles
├── database/           # SQL dump with structure and demo data
├── images/, video/     # Media assets
├── includes/           # DB connection, config, header & footer
├── js/                 # Contact form validation, counters
├── uploads/            # Uploaded project images
└── *.php               # Public pages
```

## Author

**Milena Zujović** — [LinkedIn](https://www.linkedin.com/in/milena-zujovic-dev/) · [GitHub](https://github.com/LufiLufiLufi7)
