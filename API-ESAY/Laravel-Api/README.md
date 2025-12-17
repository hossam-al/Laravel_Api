<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Laravel E-commerce API

Lightweight REST API built with Laravel for managing products, categories, orders and users. This README explains setup, authentication, main endpoints and quick Postman examples.

---

## Requirements
- PHP 8.x
- Composer
- SQLite/MySQL
- Node.js (for front-end assets if needed)

## Quick setup
1. Clone repository
2. Copy `.env.example` to `.env` and configure DB settings
3. Install dependencies
   ```bash
   composer install
   npm install # optional for frontend assets
   ```
4. Generate app key
   ```bash
   php artisan key:generate
   ```
5. Run migrations and seeders
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
6. Start dev server
   ```bash
   php artisan serve
   ```

## Authentication
This API uses Laravel Sanctum for token-based authentication.

- Register: POST /api/v1/register
- Login: POST /api/v1/login
- Protected routes require `Authorization: Bearer {token}` header.

## Main endpoints
Base path: `/api/v1`

Products
- GET `/products` — list products (filters: search, category_id, min_price, max_price)
- GET `/products/{id}` — get single product
- POST `/products` — create product (Super Admin only)
- PUT `/products/{id}` — update product (Super Admin only)
- DELETE `/products/{id}` — delete product (Super Admin only)

Admin Products (per-admin catalog)
- GET `/admin-products` — list current admin's products
- POST `/admin-products` — create admin product (Admin only)
- PUT `/admin-products/{id}` — update admin product (Admin only)
- DELETE `/admin-products/{id}` — delete admin product (Admin only)

Categories
- GET `/category` — list categories
- POST `/category` — create category (Admin)
- PUT `/category/{id}` — update category
- DELETE `/category/{id}` — delete category

Orders
- GET `/orders` — list orders (super-admin sees all; users see own)
- POST `/orders` — create order (body: `product_id` + `quantity` or `products` array)
- GET `/orders/{id}` — show order
- PUT `/orders/{id}` — update order status (admin only)
- DELETE `/orders/{id}` — delete order (super-admin)

Order Items
- GET `/order-items` — list items or filter by `?order_id=`
- POST `/order-items` — add item to order
- PUT `/order-items/{id}` — update item
- DELETE `/order-items/{id}` — delete item

Roles
- CRUD available under `/roles` (Super Admin manages roles)

## Models (quick)
- Product: `app/Models/products.php` (fillable: name,user_id,image_url,sku,description,price,stock,is_active,is_featured,category_id)
- AdminProduct: `app/Models/AdminProduct.php` (admin-scoped products)
- Order / OrderItem: `app/Models/Order.php`, `app/Models/OrderItem.php`

## Important behaviors
- Super Admin (role_id=1) controls main catalog (create/update/delete).
- Admin (role_id=2) manages own products in `admin_products` table.
- All stock changes are performed server-side and decremented when orders/items created.
- Transactions are used for order and order-item operations.

## Postman quick examples
1) Register and login to get token.
2) Create order (single product)
```json
POST /api/v1/orders
{
  "product_id": 2,
  "quantity": 1
}
```
3) Create order (multiple products)
```json
POST /api/v1/orders
{
  "products": [
    {"id": 2, "quantity": 1},
    {"id": 3, "quantity": 2}
  ]
}
```

## Troubleshooting
- Foreign key errors during migrate: use careful migration order or create new migrations instead of editing existing ones in production.
- If you see `Attempt to read property "price" on bool` ensure product exists and API sends correct `product_id`.

## Next steps (suggested)
- Add Policies for Orders and Products
- Add unit and feature tests for order flows
- Harden concurrency on stock updates (row locks or atomic decrement)

---

If you want, I can also generate a Postman collection and seeders for 10 categories + 10 products in English. Tell me which one to do next.
