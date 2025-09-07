<picture>
    <source media="(prefers-color-scheme: dark)" srcset=".github/assets/banner-dark.png">
    <img alt="Logo for pokio" src=".github/assets/banner-light.png">
</picture>

# Fiov
[![tests](https://github.com/PascalKleindienst/fiov/actions/workflows/tests.yml/badge.svg)](https://github.com/PascalKleindienst/fiov/actions/workflows/tests.yml)
[![PHPStan](https://github.com/PascalKleindienst/fiov/actions/workflows/phpstan.yml/badge.svg)](https://github.com/PascalKleindienst/fiov/actions/workflows/phpstan.yml)


This is a Laravel application designed for financial management, offering features like budgeting, wallet management, and transaction tracking.
## Features

- 📊 **Budgeting:** Create and manage budgets with different statuses and types.
- 💳️ **Wallet Management:** Organize your finances with multiple wallets.
- 💵 **Transaction Tracking:** Record and categorize your income and expenses, including recurring transactions.
- 🏷️ **Category Management:** Define custom categories for better financial organization.

## Installation

To get this project up and running on your local machine, follow these steps:

### Prerequisites

Ensure you have the following installed:

- [All requirements by Laravel 12](https://laravel.com/docs/12.x#server-requirements) – PHP >= 8.3 with required extensions
- One of the databases supported by Laravel. 
- If you're building Fiov from source, make sure to have Composer, Git, and Node.js >= 18 with npm.

### Setup Steps
1.  **Clone the repository:**

    ```bash
    git clone https://github.com/PascalKleindienst/fiov.git
    cd fiov
    ```

2.  **Install PHP dependencies:**

    ```bash
    composer install
    ```
3.  **Install Node.js dependencies:**

    ```bash
    npm install
    ```

4. **Run the installer:**

   Run and follow the installer. This will setup the database, create a new  admin user and a default wallet, and build the frontend assets.

   ```bash
    php artisan fiov:init
    ```
5. **(Optional) Seed the database:**

   If you want to populate your database with some demo data, run:

    ```bash
     php artisan db:seed --class=Database\\Seeders\\DemoDataSeeder
    ```
   
6.  **Start the development server:**

    ```bash
    php artisan serve
    ```

    The application will typically be available at `http://127.0.0.1:8000`.

## Testing

To run the automated tests for this project, use Pest:

```bash
composer test
```

## Contributing

We welcome contributions to this project! If you'd like to contribute, please follow these steps:

1.  Fork the repository.
2.  Create a new branch (`git checkout -b feature/your-feature-name`).
3.  Make your changes and ensure tests pass.
4.  Commit your changes (`git commit -m 'Add new feature'`).
5.  Push to the branch (`git push origin feature/your-feature-name`).
6.  Open a Pull Request.

Please ensure your code adheres to the existing coding standards and includes appropriate tests.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

