# Inventory Service

A lightweight inventory management application built with **Laravel**, **Vue 3**, **Inertia.js**, and **Tailwind CSS**.

The application allows users to:

* View available products and stock levels
* Create orders by selecting products and quantities
* Validate product stock availability
* Automatically calculate order totals
* Reduce product inventory when an order is placed
* View created orders and their details
* Expose RESTful API endpoints for products and orders

---

## Tech Stack

### Backend

* PHP
* Laravel
* Laravel Eloquent ORM
* SQLite / MySQL / PostgreSQL

### Frontend

* Vue 3
* Inertia.js
* Tailwind CSS
* Vite
* Axios

### API

* RESTful JSON API
* Laravel API Resources
* JSON responses for API endpoints

---

## Features

### Product Management

The product listing displays:

* Product name
* SKU
* Category
* Price
* Current stock quantity

Products are loaded from the API:

```text
GET /api/products
```

---

### Order Creation

Users can create an order by:

1. Opening the Products page
2. Selecting **Create Order**
3. Selecting product quantities
4. Reviewing the calculated order total
5. Clicking **Place Order**

The frontend prevents users from selecting more items than are currently available in stock.

Orders are created through:

```text
POST /api/orders
```

Example request:

```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 3
        },
        {
            "product_id": 2,
            "quantity": 1
        }
    ]
}
```

---

### Stock Validation

When an order is created, the backend validates that sufficient stock is available.

If there is not enough stock, the API returns a `409 Conflict` response.

The frontend displays an appropriate error message and updates the available stock for the affected product.

---

### Order Totals

The total order value is calculated from:

```text
product price × requested quantity
```

The backend calculates the final order total when the order is created.

Each order item stores:

* Product
* Quantity
* Unit price
* Subtotal

This preserves the price at the time the order was created.

---

## Project Structure

```text
app/
├── Http/
│   └── Controllers/
│       ├── OrderController.php
│       └── ProductController.php
│
├── Models/
│   ├── Order.php
│   ├── OrderItem.php
│   └── Product.php

database/
├── factories/
│   └── ProductFactory.php
│
├── migrations/
│   ├── create_products_table.php
│   ├── create_orders_table.php
│   └── create_order_items_table.php
│
└── seeders/
    └── DatabaseSeeder.php

resources/
├── js/
│   ├── Pages/
│   │   ├── Products/
│   │   │   └── Index.vue
│   │   │
│   │   └── Orders/
│   │       ├── Create.vue
│   │       └── Show.vue
│   │
│   └── app.js
│
└── css/
    └── app.css

routes/
├── api.php
└── web.php
```

---

# Requirements

Before installing the project, make sure you have:

* PHP 8.2+
* Composer
* Node.js 18+
* npm
* A supported database

The application can be configured to use:

* SQLite
* MySQL
* PostgreSQL

---

# Installation

Clone the repository:

```bash
git clone <repository-url>
```

Navigate into the project directory:

```bash
cd inventory-service
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

---

# Environment Configuration

Create the environment file:

```bash
cp .env.example .env
```

On Windows, you can copy the file manually:

```text
.env.example → .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

---

# Database Configuration

Configure the database in `.env`.

## SQLite

For SQLite, create the database file:

```bash
touch database/database.sqlite
```

Or create an empty file manually at:

```text
database/database.sqlite
```

Then configure:

```env
DB_CONNECTION=sqlite
```

Remove or comment out the other `DB_*` settings if necessary.

---

## MySQL

Example configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory
DB_USERNAME=root
DB_PASSWORD=
```

Make sure the `inventory` database exists before running migrations.

---

## PostgreSQL

Example configuration:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=inventory
DB_USERNAME=postgres
DB_PASSWORD=
```

Make sure PostgreSQL is running and the `inventory` database exists.

---

# Run Database Migrations

Run:

```bash
php artisan migrate
```

To reset the database and run all migrations from scratch:

```bash
php artisan migrate:fresh
```

---

# Seed the Database

The project includes product seed data for development and testing.

Run:

```bash
php artisan db:seed
```

To reset the database and seed it:

```bash
php artisan migrate:fresh --seed
```

This creates sample products that can be viewed from:

```text
/products
```

---

# Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

In a separate terminal, start Vite:

```bash
npm run dev
```

The application will typically be available at:

```text
http://127.0.0.1:8000
```

Open:

```text
/products
```

---

# Application Routes

## Web Routes

| Method | URL               | Name             | Description           |
| ------ | ----------------- | ---------------- | --------------------- |
| GET    | `/`               | -                | Redirects to products |
| GET    | `/products`       | `products.index` | Product listing       |
| GET    | `/orders/create`  | `orders.create`  | Create order page     |
| GET    | `/orders/{order}` | `orders.show`    | View order            |

Web routes return Inertia pages.

---

## API Routes

### Products

| Method    | Endpoint                  | Description      |
| --------- | ------------------------- | ---------------- |
| GET       | `/api/products`           | List products    |
| POST      | `/api/products`           | Create a product |
| GET       | `/api/products/{product}` | Get a product    |
| PUT/PATCH | `/api/products/{product}` | Update a product |
| DELETE    | `/api/products/{product}` | Delete a product |

### Orders

| Method | Endpoint              | Description     |
| ------ | --------------------- | --------------- |
| GET    | `/api/orders`         | List orders     |
| POST   | `/api/orders`         | Create an order |
| GET    | `/api/orders/{order}` | Get an order    |

---

# API Examples

## Get Products

Request:

```http
GET /api/products
```

Example response:

```json
{
    "data": [
        {
            "id": 1,
            "name": "Example Product",
            "sku": "SKU-1234",
            "category": "Electronics",
            "price": "99.99",
            "stockQuantity": 20
        }
    ]
}
```

---

## Create an Order

Request:

```http
POST /api/orders
Content-Type: application/json
```

Body:

```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 2,
            "quantity": 1
        }
    ]
}
```

Example response:

```json
{
    "data": {
        "id": 1,
        "total": "299.97",
        "items": [
            {
                "id": 1,
                "productId": 1,
                "productName": "Example Product",
                "quantity": 2,
                "unitPrice": "99.99",
                "subtotal": "199.98"
            }
        ]
    }
}
```

---

# Frontend and API Architecture

The application separates **Inertia page navigation** from **API requests**.

For example, navigating to:

```text
/products
```

returns an Inertia page:

```text
Products/Index.vue
```

The Vue page then retrieves product data using Axios:

```js
axios.get('/api/products')
```

Similarly, creating an order uses:

```js
axios.post('/api/orders', {
    items: orderItems.value,
})
```

After the order is successfully created, the application navigates to the Inertia order page:

```js
router.visit(route('orders.show', orderId))
```

This separation ensures that:

* `/products` returns an Inertia response
* `/api/products` returns JSON
* `/orders/create` returns an Inertia response
* `/api/orders` returns JSON
* `/orders/{id}` returns an Inertia response
* `/api/orders/{id}` returns JSON

API route names use an `api.` prefix where necessary to avoid conflicts with Inertia web routes.

---

# Development Commands

Install dependencies:

```bash
composer install
npm install
```

Run Laravel:

```bash
php artisan serve
```

Run Vite:

```bash
npm run dev
```

Build frontend assets:

```bash
npm run build
```

Run migrations:

```bash
php artisan migrate
```

Reset database:

```bash
php artisan migrate:fresh
```

Reset database and seed:

```bash
php artisan migrate:fresh --seed
```

Run seeders:

```bash
php artisan db:seed
```

Clear Laravel caches:

```bash
php artisan optimize:clear
```

List routes:

```bash
php artisan route:list
```

---

# Testing

The application can be tested manually through the UI or using API tools such as Postman or Insomnia.

Recommended test scenarios:

1. View the products page.
2. Confirm seeded products are displayed.
3. Open the Create Order page.
4. Select one or more products.
5. Verify the order total is calculated correctly.
6. Attempt to order more than the available stock.
7. Confirm the API returns a `409 Conflict`.
8. Create a valid order.
9. Confirm the order is created successfully.
10. Confirm product stock is reduced.
11. Open the order details page.
12. Verify the order total and individual line items.

---

# Important Design Decisions

### Separate Web and API Routes

Inertia routes and API routes are intentionally separated.

Web routes return Vue pages through Inertia, while API routes return JSON.

This prevents Inertia navigation from accidentally receiving an API JSON response.

### Stock Validation on the Backend

Stock availability is checked on the backend when an order is created.

The frontend prevents obvious invalid selections, but the backend remains the source of truth.

### Order Item Price Snapshot

The unit price is stored on each order item when the order is created.

This means historical orders retain the correct price even if the product price changes later.

### Database Transactions

Order creation and stock updates should be performed inside a database transaction to ensure that an order cannot be partially created if an error occurs.

---

# Future Improvements

Potential future improvements include:

* Authentication and authorization
* Product creation and editing UI
* Product search and filtering
* Pagination
* Order history
* Order status management
* Low-stock notifications
* Inventory adjustment history
* Form Request validation
* API Resources
* Automated Feature and Unit tests
* Database transactions for order creation
* Optimistic locking or row-level locking for concurrent orders
* Docker development environment
* API authentication with Laravel Sanctum

---

# License

This project is for development and demonstration purposes.
