
Add 

"guesl/laravel-admin": "^1.1"

"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/guesl/laravel-admin.git",
        "no-api": true
    }
]
    
composer install/update

php artisan guesl:install

php artisan guesl:generate User --module=User --force

yarn install
yarn run dev