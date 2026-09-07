# POSApp

POSApp is a Laravel and Inertia point-of-sale application for managing products,
categories, customers, employees, orders, and checkout activity from one
dashboard.

## Stack

- Laravel 13
- PHP 8.3
- Inertia.js with Vue 3
- Vite
- Tailwind CSS

## Main Features

- Dashboard metrics for daily sales, daily orders, customers, products, and low
  stock items
- POS checkout screen for creating orders
- Product and category management
- Customer management
- Employee management with status toggling
- Order list and order detail screens

## Local Setup

Install PHP and JavaScript dependencies:

```bash
composer install
npm install
```

Create the environment file and application key:

```bash
cp .env.example .env
php artisan key:generate
```

Run migrations and seeders:

```bash
php artisan migrate --seed
```

Start the development servers:

```bash
composer run dev
```

## Testing

Run the PHP test suite:

```bash
composer test
```
