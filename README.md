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

For a local development admin account, insert a user with `role = 'admin'` as shown below. Change the password immediately for any shared or deployed environment.

## XAMPP on Linux

Start Apache and MySQL from the XAMPP manager, or run these commands in a terminal:

```bash
sudo /opt/lampp/lampp startapache
sudo /opt/lampp/lampp startmysql
sudo ln -sfn /home/bharath/Downloads/portfolio-bulider1 /opt/lampp/htdocs/portfolio-builder1
/opt/lampp/bin/mysql -u root < /home/bharath/Downloads/portfolio-bulider1/schema.sql
/opt/lampp/bin/mysql -u root portfolio_builder < /home/bharath/Downloads/portfolio-bulider1/database/seed_templates.sql
```

Open `http://127.0.0.1/portfolio-builder1/`. XAMPP's PHP installation already provides the DOM, PDO MySQL, Fileinfo, and Mbstring extensions required by this project. Use `http://127.0.0.1/phpmyadmin/` to inspect the database.

The admin upload folders must be writable by Apache. On XAMPP Linux, run:

```bash
sudo chown -R daemon:daemon templates/uploads uploads
sudo chmod -R 775 templates/uploads uploads
```

## Features

Users can register, choose from 50 seeded active templates, build portfolios with repeatable education, skill, project, and experience rows, preview, export standalone HTML, and print to PDF. The admin panel provides dashboard counts and template create/edit/delete management. Template selection is restricted to active records and all forms use prepared SQL and CSRF tokens.

## Structure

`auth/` handles sessions, `admin/` handles administration, `includes/renderer.php` provides reusable portfolio rendering, `database/` contains seed data, and `assets/` contains the responsive UI styles.

The `download-pdf.php` route uses Dompdf after `composer install` and returns a server-generated PDF without exposing internal paths.

## Dynamic HTML/CSS Templates

Run `database/migrate_dynamic_templates.sql` once on an existing database. Admins can then upload an HTML file and an optional CSS file from the Add Template panel. Placeholders such as `{{name}}`, `{{gallery}}`, `{{services}}`, `{{skills}}`, and `{{instagram}}` are detected automatically and shown as the user form fields for that template.

Uploaded HTML is sanitized to remove PHP and script execution. Uploaded CSS is stored as data and injected into the generated document; no uploaded server-side code is included or executed. Existing PHP templates remain available as backward-compatible seeded templates, but the new upload flow accepts HTML/CSS only.
