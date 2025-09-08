---
layout: doc
---

# Getting Started

## Requirements

Fiov has the following requirements:

- [All Laravel 12 requirements](https://laravel.com/docs/12.x#server-requirements) – PHP >= 8.3 with required extensions
- One of the databases supported by Laravel.
- If you are building Fiov from source code, make sure you have Composer, Git and Node.js >= 20 with npm installed.

## Installation
### Using the pre-compiled archive

::: warning TODO
Installation from a pre-compiled archive is currently not supported. We are working on improving this in the future.
:::

### Building from source code

```bash
cd <FIOV_ROOT_DIR>
git clone https://github.com/PascalKleindienst/fiov.git .
composer install --optimize-autoloader --no-dev
npm install
php artisan fiov:init 

# Follow the installer instructions.
# This will set up the database, create a new admin user
# and a default wallet, and build the frontend assets.

php artisan serve
```

The application is now available at `http://127.0.0.1:8000`.

::: tip Note
With `php artisan serve` you can run the application locally at `http://127.0.0.1:8000`. However, this is only suitable for development purposes.
For performance reasons, we recommend running the application with a web server like Nginx or Apache and pointing to the `public` directory of the application.
:::

## Configuration
Fiov's configuration is stored in the `.env` file at the root of the project, which is created during the installation process from the `.env.example` file.
The values can be adjusted to your own environment at any time.

### Configuring emails
Although Fiov can work without a mailer, it is recommended to configure a mailer as some features like user invitations require email sending.

The mailer settings are stored in the `.env` file at the root of the project and start with `MAIL_`. An example configuration for Gmail might look like this:

```ini
MAIL_MAILER=smtp                            # Type of mailer, default: smtp
MAIL_FROM_ADDRESS="hello@example.com"       # The sender address
MAIL_FROM_NAME="${APP_NAME}"                # The sender name, here the value from the APP_NAME variable is used
MAIL_HOST=smtp.gmail.com                    # The mail host/server name
MAIL_PORT=587                               # The SMTP port
MAIL_USERNAME=youremail@googlemail.com      # The SMTP username
MAIL_PASSWORD=test                          # A generated app password
```

If email-dependent features are not needed, the `MAIL_MAILER` value can be set to `log` or `array` and the rest of the mailer-related values can be left empty.

## Update
::: danger Back up database
Before any Fiov version update, a database backup should be created.
:::

### Updating a source code installation
If Fiov was installed from source code, the update can be performed as follows:

```bash
git pull origin main
composer install --optimize-autoloader --no-dev
npm install
php artisan fiov:init
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

## Troubleshooting
During the installation and operation of Fiov, various technical problems may occur that require a systematic approach.
A detailed guide for general troubleshooting can be found under [Troubleshooting](./troubleshooting.md).
The following describes some of the most common installation problems and their solution approaches:

### Permission Issues
It must be ensured that the `storage` and `bootstrap/cache` directories are writable. These folders are used by Laravel to store cache files, session data, compiled templates, and log files. Without appropriate write permissions, various application functions cannot be executed properly.
```bash
chmod -R 775 storage bootstrap/cache
```

Additionally, it should be verified that the web server user (usually `www-data`, `apache`, or `nginx`) owns these directories:
```bash
chown -R www-data:www-data storage bootstrap/cache
```

### Composer Memory Limit
If memory limit issues occur during the installation of PHP dependencies, the memory limit can be temporarily lifted. This is particularly necessary for larger projects or on servers with limited RAM. The problem usually manifests through error messages like "Fatal error: Allowed memory size exhausted".
```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```
Alternatively, the PHP memory limit can also be permanently increased in the `php.ini`:
```ini
memory_limit = 512M
```

### Node.js Version
It must be ensured that a compatible Node.js version (20.x or higher) is used. Older versions can lead to compilation errors with frontend assets or may not support modern JavaScript features. The currently installed version can be checked with the following command:
```bash
node --version
npm --version
```
For managing different Node.js versions, using nvm (Node Version Manager) is recommended, which allows easy switching between different Node.js versions:
```bash
# Install nvm (on Linux/macOS)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash

# Install and use latest LTS version
nvm install --lts
nvm use --lts
```
