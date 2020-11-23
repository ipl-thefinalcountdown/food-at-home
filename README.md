# Food@Home

## Usage

## First run configuration

Inside php-fpm container:

```bash
composer install
php artisan migrate:fresh
php artisan db:seed
```

### Run

```bash
make up
```

If you want to run the containers in background, use `start` target:

```bash
make start
```

### Stop

```bash
make down
```

or even:

```bash
make stop
```

## Configurations

The web service exports `8080` port and `10000` for database management service.

### Database

- **Root Password:** mardb
- **Default Database:** sql_db1
- **Default User:** db_user
- **User password:** db_user

Use `db.docker.local` host to connect to the database.
