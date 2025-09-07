---
layout: doc
---

# Local Development

Fiov is built with Laravel and Livewire, and as such, it requires a PHP environment to run.
There are multiple ways to set up a PHP development environment, but if you're on macOS or Windows, the easiest way is probably to use [Laravel Herd](https://herd.laravel.com/). 
If you are on Linux you probably know what you are doing and can set up your own environment 😉

You will also need [Node.js and npm](https://nodejs.org/) to build the frontend assets. 

## Running Locally
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
   
7. **Start the development server:**

    This starts a development server under `http://localhost:8000`
   ```bash
   $ composer dev
   
   > Composer\Config::disableProcessTimeout
   [vite] 
   [vite] > dev
   [vite] > vite
   [vite]
   [logs]
   [logs]    INFO  Tailing application logs.                        Press Ctrl+C to exit  
   [logs]                                                Use -v|-vv to show more details  
   [queue]
   [queue]    INFO  Processing jobs from the [default] queue.  
   [queue]
   [vite]
   [vite]   VITE v6.2.0  ready in 310 ms
   [vite]
   [vite]   ➜  Local:   http://localhost:5173/
   [vite]   ➜  Network: use --host to expose
   [vite]
   [vite]   LARAVEL v12.28.1  plugin v1.2.0
   [vite]
   [vite]   ➜  APP_URL: http://localhost:8000
   [server]
   [server]    INFO  Server running on [http://127.0.0.1:8000].  
   [server]
   [server]   Press Ctrl+C to stop the server
   [server]
    ```
    
## Testing, Linting, etc
There are some scripts available to help you with testing, linting, etc. These are all part of the `composer.json` file. If you want to know more, just look at the `scripts` section in the `composer.json` file.

```bash
composer test               # Run the whole test suite (ie everything with test:*)
composer test:lint          # Run linting (Pint)
composer test:rector        # Run rector with --dry-run, ie check if there are any changes that need to be made
composer test:type-coverage # Run type coverage
composer test:types         # Run static analysis (PHPStan)
composer test:unit          # Run unit/feature tests

composer format             # Fix all issues the linter found
composer rector             # Fix all issues rector found
```

## Docs
If you want to contribute to the docs, you can find them in the `docs` directory. They are written in Markdown and use [vitepress](https://vitepress.vuejs.org/).
To start the VitePress instance, you can use the following command:

```bash
npm run docs:dev

> docs:dev
> vitepress dev docs

  vitepress v1.6.4

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
  ➜  press h to show help
```
