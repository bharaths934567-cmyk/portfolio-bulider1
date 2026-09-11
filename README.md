# Portfolio Builder

PHP 8+, MySQL, PDO, HTML, CSS, and vanilla JavaScript portfolio builder.

## Requirements

PHP 8.0+, MySQL 8 or MariaDB, Apache with `mod_rewrite`, and Composer. Enable `pdo_mysql`, `fileinfo`, and `mbstring`.

## Setup

1. Create a database by importing `schema.sql`, then import `database/seed_templates.sql`.
2. Run `composer install` to install Dompdf.
3. Set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` environment variables, or edit the local development defaults in `config.php`.
4. Point Apache's document root at this directory and enable `AllowOverride All` for `.htaccess`.
5. Register a user, then create an admin with a PHP-generated `password_hash()` value and `role = 'admin'`:

```bash
php -r "echo password_hash('change-this-password', PASSWORD_DEFAULT), PHP_EOL;"
```

6. Visit `/admin/login.php` to manage templates.

## Features

Users can register, choose from 50 seeded active templates, build portfolios with repeatable education, skill, project, and experience rows, preview, export standalone HTML, and print to PDF. The admin panel provides dashboard counts and template create/edit/delete management. Template selection is restricted to active records and all forms use prepared SQL and CSRF tokens.

## Structure

`auth/` handles sessions, `admin/` handles administration, `includes/renderer.php` provides reusable portfolio rendering, `database/` contains seed data, and `assets/` contains the responsive UI styles.

The `download-pdf.php` route uses Dompdf after `composer install` and returns a server-generated PDF without exposing internal paths.
