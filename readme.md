
# [Laravel 5.8 News MongoDB](https://github.com/lukisanjaya/laravel-restapi-crud-mongodb-redis-caching-)

Laravel 5.8 REST API (MongoDB + Redis) with JWT, Repository Pattern.

## Installation

1. Install vendors composer :

```bash
composer install
```

2. Copy File .env.example to .env :

```bash
cp .env.example .env
```

3. Generate key :

```bash
php artisan key:generate
```

4. Running DB Seed and cache
```bash
sh cache.sh
```

5. Run Your Program
```bash
php artisan serve
```

6. Open Your Browser [http://127.0.0.1:8000/](http://127.0.0.1:8000/), and you will see the news management api documentation.


## Login API

```text
Admin
email : admin@gmail.com
password : 1245678


Redaktur
email : redaktur@gmail.com
password : 1245678


Reporter
email : reporter@gmail.com
password : 1245678
```
