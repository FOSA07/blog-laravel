# Blog Laravel Project

This is a Laravel project using a PostgreSQL database hosted on Render.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running on your machine.
- [Composer](https://getcomposer.org/) (optional, if you have PHP locally) or you can use the sail composer image.

## Getting Started for Team Members

### 1. Clone the repository
```bash
git clone <repository-url>
cd blog-laravel
```

### 2. Install Dependencies
If you have PHP and Composer installed locally, you can run:
```bash
composer install
```
*If you do not have PHP/Composer locally, you can use a small Docker container to install the dependencies:*
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Setup Environment Variables
Copy the example environment file to create your own local copy:
```bash
cp .env.example .env
```

### 4. Configure the Shared Database
Open your `.env` file. We are using our shared PostgreSQL database hosted on Render. Replace the default database section in your `.env` with the following:

```env
DB_CONNECTION=pgsql
DB_URL="postgresql://user:password@host.render.com/database"
```
*(Contact the team lead to get the actual `DB_URL` connection string if you do not have it).*

Also, to avoid port conflicts (like port 80 collisions), ensure your `.env` has:
```env
APP_PORT=8000
```

### 5. Start the App and Generate Key
Start Laravel Sail (Docker containers) in the background:
```bash
./vendor/bin/sail up -d
```
Then generate the application key:
```bash
./vendor/bin/sail artisan key:generate
```

### 6. Run Migrations (Optional)
To ensure the database is up to date, run migrations. **Note:** Since we share the Render database, if someone else already ran the latest migrations, you don't necessarily have to run them again, but it's safe to run:
```bash
./vendor/bin/sail artisan migrate
```

### 7. Access the Application
The application will be accessible at:
[http://localhost:8000](http://localhost:8000)

---
### Helper Sail Commands
Instead of typing `php artisan`, use the sail prefix:
- `./vendor/bin/sail artisan make:model Post -m`
- `./vendor/bin/sail npm install`
- `./vendor/bin/sail npm run dev`
