## Setup Instructions

- Clone the project from main branch
- Execute `composer install` and this will install all the project dependancies
- Rename `.env.example` to `.env`
- Add database and Redis credentials to the `.env` file
- Execute `php artisan migrate --seed`. This will create the tables needed for the
  database and will populate some sample data too.
- Execute `php artisan serve`
- Laravel [sanctum]('https://laravel.com/docs/8.x/sanctum') was used for the SPA Authentication
- After setting up the Front-end vue application, Change the `port` no of the `SANCTUM_STATEFUL_DOMAINS` env key
- Default admin credentials `admin@admin.com` | `secret`
