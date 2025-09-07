---
layout: doc
---

# Troubleshooting

Nobody is perfect and things sometimes go wrong. It's not the end of the world, here are some common problems and their solutions:

## Getting Started

When an error occurs, you should first look at the logs under `storage/logs/laravel.log`. Often you can already see what exactly went wrong or where the error occurs.
The error messages are also important if a [ticket](https://github.com/pascalkleindienst/fiov/issues) needs to be created.

::: danger Always check the log first
The first step when encountering an error should always be to look at the log file `storage/logs/laravel.log`
:::

Next, the browser console should be checked for JavaScript errors. The "Network" tab in the developer console can also be checked for failed requests.

Another step can be to clear the cache, reinstall dependencies and recompile the frontend assets.
Below are some commands that can help with this:

```bash
# Delete vendor directory and reinstall dependencies
rm -rf vendor && composer install

# Recompile frontend assets
rm -rf node_modules && npm install && npm run build

# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear
```

### Check Fiov Status

With the command `php artisan fiov:status` you can check the status of Fiov. This will output all relevant information that can help identify the problem.

```bash
php artisan fiov:status
```

![Fiov Status](../assets/images/fiov-status.png)


## Common Problems

::: details Permissions
Make sure the web server has the necessary permissions to access important folders like `storage`,
`bootstrap/cache` and `public` *recursively*.
Also remember to execute Artisan commands as the web server user (e.g. `www-data` or `nginx`), and never as `root`,
since these commands can create files that the web server user must have access to.
:::


## Asking for Help

If you can't figure it out on your own, you can create a ticket on [Github](https://github.com/pascalkleindienst/fiov/issues). Remember to be polite and patient.
