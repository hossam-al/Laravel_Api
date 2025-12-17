<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Commerce API — Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #ec4899;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            overflow-x: hidden;
        }

        /* Animated particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100vh) translateX(100px);
                opacity: 0;
            }
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.95) 0%, rgba(139, 92, 246, 0.95) 100%);
            padding: 80px 0 60px;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.5;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 2rem;
        }

        .hero-badges {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .badge-hero {
            padding: 0.5rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }

        .badge-hero:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Download Buttons */
        .btn-download {
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            color: #6366f1;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.9rem 2rem;
            border-radius: 50px;
            border: none;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            animation: pulse 2s infinite;
        }

        .btn-download:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #4f46e5;
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .btn-download:active {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-download-secondary {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            animation: pulse-secondary 2s infinite;
        }

        .btn-download-secondary:hover {
            background: linear-gradient(135deg, #ff8787 0%, #ff6b6b 100%);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 107, 107, 0.5);
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            }

            50% {
                box-shadow: 0 8px 25px rgba(255, 255, 255, 0.4);
            }
        }

        @keyframes pulse-secondary {

            0%,
            100% {
                box-shadow: 0 8px 20px rgba(255, 107, 107, 0.4);
            }

            50% {
                box-shadow: 0 8px 25px rgba(255, 107, 107, 0.6);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Sticky nav */
        .sticky-nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 2rem;
            transition: all 0.3s;
        }

        .nav-pills .nav-link {
            color: #64748b;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .nav-pills .nav-link:hover {
            color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        /* Search */
        .search-box {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-box input {
            border-radius: 50px;
            padding: 0.75rem 3rem 0.75rem 1.5rem;
            border: 2px solid #e2e8f0;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .search-box i {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* Content */
        .content-wrapper {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            animation: fadeIn 0.6s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            font-weight: 700;
            border: none;
            padding: 1rem 1.5rem;
        }

        /* Sections */
        section {
            scroll-margin-top: 100px;
            margin-bottom: 3rem;
        }

        h5 {

            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        /* Tables */
        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .table tbody tr {
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.05);
            transform: scale(1.01);
        }

        /* Method badges */
        .method-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .method-get {
            background: var(--success);
            color: white;
        }

        .method-post {
            background: #3b82f6;
            color: white;
        }

        .method-put {
            background: var(--warning);
            color: white;
        }

        .method-delete {
            background: var(--danger);
            color: white;
        }

        /* Tabs */
        .rr-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }

        .rr-tab {
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #e2e8f0;
            background: white;
            color: #64748b;
            font-weight: 600;
            transition: all 0.3s;
        }

        .rr-tab:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .rr-tab.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .rr-content {
            animation: fadeIn 0.3s;
        }

        /* Code blocks */
        pre.code-sample {
            background: #0d1117;
            color: #e6edf3;
            padding: 1.5rem;
            border-radius: 12px;
            overflow-x: auto;
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            margin-bottom: 1rem;
        }

        .copy-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
        }

        .copy-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .copy-btn.copied {
            background: var(--success);
            border-color: var(--success);
        }

        /* Accordion */
        .accordion-button {
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-radius: 10px !important;
        }

        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            color: var(--primary);
        }

        .accordion-button::after {
            transition: transform 0.3s ease;
        }

        .accordion-button:not(.collapsed)::after {
            transform: rotate(180deg);
        }

        .accordion-item {
            border: none;
            margin-bottom: 0.5rem;
            border-radius: 10px !important;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Scroll to top */
        .scroll-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .scroll-top.show {
            display: flex;
        }

        .scroll-top:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.6);
        }

        /* Alert styles */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(99, 102, 241, 0.1));
            color: #ffff;
            border-left: 4px solid var(--primary);
        }

        /* Footer */
        footer {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
        }

        code {
            white-space: pre-wrap;
        }
    </style>
</head>

<body>
    <!-- Particles -->
    <div class="particles" id="particles"></div>

    <!-- Scroll to top -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Hero -->
    <div class="hero">
        <div class="container">
            <div class="hero-content text-center">
                <h1>
                    <i class="fas fa-rocket me-3"></i>E-Commerce API
                </h1>
                <p class="hero-subtitle">
                    Laravel-based REST API for managing products, categories, orders & users
                </p>
                <div class="hero-badges">
                    <span class="badge-hero"><i class="fab fa-laravel me-2"></i>Laravel</span>
                    <span class="badge-hero"><i class="fas fa-shield-alt me-2"></i>Sanctum Auth</span>
                    <span class="badge-hero"><i class="fas fa-database me-2"></i>MySql</span>
                    <span class="badge-hero"><i class="fas fa-bolt me-2"></i>RESTful</span>
                </div>

                <!-- Download Buttons -->
                <div class="mt-4 text-center">
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ asset('API-DOCUMENTATION.md') }}" download="API-DOCUMENTATION.md"
                            class="btn btn-download">
                            <i class="fas fa-file-alt me-2"></i>
                            API Documentation
                            <span class="badge bg-light text-dark ms-2">MD</span>
                        </a>
                        <a href="{{ asset('E-Commerce-API.postman_collection.json') }}"
                            download="E-Commerce-API.postman_collection.json"
                            class="btn btn-download btn-download-secondary">
                            <i class="fas fa-rocket me-2"></i>
                            Postman Collection
                            <span class="badge bg-light text-dark ms-2">JSON</span>
                        </a>
                    </div>
                    <p class="text-white-50 small mt-3 mb-0">
                        <i class="fas fa-info-circle me-1"></i> Complete documentation with all endpoints, validation
                        rules & ready-to-use Postman collection
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <!-- Search -->
        <div class="search-box">
            <input type="text" class="form-control" id="searchEndpoint"
                placeholder="🔍 Search endpoints (e.g., login, products, orders...)">
            <i class="fas fa-search"></i>
        </div>

        <!-- Sticky Navigation -->
        <div class="sticky-nav">
            <ul class="nav nav-pills justify-content-center flex-wrap">
                <li class="nav-item"><a class="nav-link" href="#authentication"><i class="fas fa-key me-2"></i>Auth</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="#products"><i class="fas fa-box me-2"></i>Products</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="#admin-products"><i
                            class="fas fa-crown me-2"></i>Admin</a></li>
                <li class="nav-item"><a class="nav-link" href="#categories"><i
                            class="fas fa-tags me-2"></i>Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="#orders"><i
                            class="fas fa-shopping-cart me-2"></i>Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="#order-items"><i class="fas fa-list me-2"></i>Items</a>
                </li>

            </ul>
        </div>

        <!-- Security & usage card -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">🔒 Security First</h5>
                <p class="mb-1"><strong>HTTPS Required</strong> — Use SSL/TLS for all production traffic.</p>
                <p class="mb-1"><strong>Token Authentication</strong> — Bearer token via Authorization header
                    (Sanctum).</p>
                <hr />
                <h6 class="card-subtitle mb-2">⚡ Rate Limiting</h6>
                <p class="mb-1">60 requests/minute — Fair usage policy applies.</p>
                <h6 class="card-subtitle mb-2">📤 File Uploads</h6>
                <p class="mb-1">Up to 2MB — Images & Documents accepted. Validate file types server-side.</p>
                <h6 class="card-subtitle mb-2">🔄 Token Lifetime</h6>
                <p class="mb-1">7 Days (auto refresh supported)</p>
                <hr />
                <p class="mb-0"><strong>Production Environment Notes:</strong></p>
                <ul class="mb-0">
                    <li>✅ SSL/HTTPS Required</li>
                    <li>✅ Token Expiry: 7 days</li>
                    <li>✅ Max File Upload: 2MB</li>
                </ul>
            </div>
        </div>



        <section id="authentication" class="mb-4">
            <h5>Authentication</h5>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Path</th>
                        <th>Description</th>
                        <th>Auth</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>POST</td>
                        <td>/login</td>
                        <td>User login (returns token)</td>
                        <td>No</td>
                    </tr>
                    <tr>
                        <td>POST</td>
                        <td>/register</td>
                        <td>Create new user</td>
                        <td>No</td>
                    </tr>
                    <tr>
                        <td>POST</td>
                        <td>/logout</td>
                        <td>Logout current user (revoke tokens)</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>POST</td>
                        <td>/update</td>
                        <td>Update user profile</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>DELETE</td>
                        <td>/deleteUser</td>
                        <td>Delete authenticated user</td>
                        <td>Yes</td>
                    </tr>
                </tbody>
            </table>
            <div class="collapse mb-4" id="ex-authentication">
                <div class="card card-body">
                    <strong>Sample Request (login):</strong>
                    <pre><code>POST /api/v1/login
    Content-Type: application/json
    {
        "email": "user@example.com",
        "password": "secret"
    }</code></pre>
                    <strong>Sample Response:</strong>
                    <pre><code>{
        "token": "ey...",
        "user": { "id": 1, "name": "Alice" }
    }</code></pre>
                    <hr />
                    <strong>Quick cURL (login):</strong>
                    <pre><code>curl -s -X POST http://127.0.0.1:8000/api/v1/login \
    -H "Content-Type: application/json" \
    -d '{"email":"user@example.com","password":"secret"}'</code></pre>
                </div>
            </div>
        </section>

        <section class="mb-4">
            <h5 id="products">Products</h5>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Path</th>
                        <th>Description</th>
                        <th>Auth</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>GET</td>
                        <td>/products</td>
                        <td>List or search products (use ?search=)</td>
                        <td>Yes</td>
                    </tr>
   <tr>
                        <td>GET</td>
                        <td>/products/{id}</td>
                        <td>Get product details</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>POST</td>
                        <td>/products</td>
                        <td>Create a product (Owner only)</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>POST</td>
                        <td>/products/{id}</td>
                        <td>Update product (Owner only)</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>DELETE</td>
                        <td>/products/{id}</td>
                        <td>Delete product (Owner only)</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>DELETE</td>
                        <td>/products/DeleteAll/delete</td>
                        <td>Delete all products (Owner only)</td>
                        <td>Yes</td>
                    </tr>
                </tbody>
            </table>
            <div class="collapse mb-4" id="ex-products">
                <div class="card card-body">
                    <strong>Sample Request (create product):</strong>
                    <pre><code>POST /api/v1/products
Authorization: Bearer {token}
Content-Type: multipart/form-data
fields: name, price, stock, category_id, image</code></pre>
                    <strong>Sample Response:</strong>
                    <pre><code>{ "id": 123, "name": "T-Shirt", "price": 19.99 }</code></pre>
                </div>
            </div>
        </section>

        <section class="mb-4">
            <h5 id="admin-products">Admin Products (Admin-only)</h5>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Path</th>
                        <th>Description</th>
                        <th>Auth</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>GET</td>
                        <td>/admin-products</td>
                        <td>List or search products (use ?search=)</td>
                        <td>Yes</td>
                    </tr>
                      <tr>
                        <td>GET</td>
                        <td>/admin-products/{id}</td>
                        <td>Get admin product details</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>POST</td>
                        <td>/admin-products</td>
                        <td>Create admin product</td>
                        <td>Yes</td>
                    </tr>

                    <tr>
                        <td>POST</td>
                        <td>/admin-products/{id}</td>
                        <td>Update admin product</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>DELETE</td>
                        <td>/admin-products/{id}</td>
                        <td>Delete admin product</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>DELETE</td>
                        <td>/admin-products/DeleteAll/delete</td>
                        <td>Delete all admin's products</td>
                        <td>Yes</td>
                    </tr>
                </tbody>
            </table>
            <div class="collapse mb-4" id="ex-admin-products">
                <div class="card card-body">
                    <strong>Sample Request (create admin product):</strong>
                    <pre><code>POST /api/v1/admin-products
Authorization: Bearer {token}
{ name, price, stock }</code></pre>
                    <strong>Sample Response:</strong>
                    <pre><code>{ "id": 10, "name": "Admin Item" }</code></pre>
                </div>
            </div>
        </section>

        <section class="mb-4">
            <!-- Permissions summary for categories -->
            <div class="alert alert-info">
                <strong>Permissions:</strong> Owner = create / update / delete. Admin &amp; User = view / search only.
            </div>
            <h5 id="categories">Categories</h5>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Path</th>
                        <th>Description</th>
                        <th>Auth</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>GET</td>
                        <td>/category</td>
                        <td>List/search categories (Admin and User can view/search)</td>
                        <td>Yes</td>
                    </tr>
                        <tr>
                        <td>GET</td>
                        <td>/category/{id}</td>
                        <td>Get category details</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>POST</td>
                        <td>/category</td>
                        <td>Create category (Owner only)</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>POST</td>
                        <td>/category/{id}</td>
                        <td>Update category (Owner only)</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>DELETE</td>
                        <td>/category/{id}</td>
                        <td>Delete category (Owner only)</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>DELETE</td>
                        <td>/category/DeleteAll/delete</td>
                        <td>Delete all categories (Owner only)</td>
                        <td>Yes</td>
                    </tr>
                </tbody>
            </table>
            <div class="collapse mb-4" id="ex-categories">
                <div class="card card-body">
                    <strong>Sample Request (create):</strong>
                    <pre><code>POST /api/v1/category
Authorization: Bearer {token}
{ "name": "New Category" }</code></pre>
                    <strong>Sample Response:</strong>
                    <pre><code>{ "id": 5, "name": "New Category" }</code></pre>
                    <hr />
                    <strong>Quick cURL (login then create):</strong>
                    <pre><code># Login and capture token
curl -s -X POST http://127.0.0.1:8000/api/v1/login -H "Content-Type: application/json" -d '{"email":"user@example.com","password":"secret"}'

# Create category (replace TOKEN)
curl -X POST http://127.0.0.1:8000/api/v1/category -H "Authorization: Bearer TOKEN" -H "Content-Type: application/json" -d '{"name":"New Category"}'</code></pre>
                </div>
            </div>
        </section>

        <section class="mb-4">
            <h5 id="orders">Orders</h5>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Path</th>
                        <th>Description</th>
                        <th>Auth</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>GET</td>
                        <td>/orders</td>
                        <td>List orders (users see own; owner sees all)</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>POST</td>
                        <td>/orders</td>
                        <td>Create order (products array or product_id + quantity)</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>GET</td>
                        <td>/orders/{id}</td>
                        <td>Get order details</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>PUT</td>
                        <td>/orders/{id}</td>
                        <td>Update order status</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>DELETE</td>
                        <td>/orders/{id}</td>
                        <td>Delete order (Owner only)</td>
                        <td>Yes</td>
                    </tr>
                </tbody>
            </table>
            <div class="collapse mb-4" id="ex-orders">
                <div class="card card-body">
                    <strong>Sample Request (create order):</strong>
                    <pre><code>POST /api/v1/orders
Authorization: Bearer {token}
{
    "products": [{ "product_id": 1, "quantity": 2 }]
}</code></pre>
                    <strong>Sample Response:</strong>
                    <pre><code>{ "id": 42, "total": 39.98 }</code></pre>
                </div>
            </div>
        </section>

 



    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Particles animation
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 15 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';
            particlesContainer.appendChild(particle);
        }

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Active nav link on scroll
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.sticky-nav .nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 150)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').slice(1) === current) {
                    link.classList.add('active');
                }
            });

            // Scroll to top button
            const scrollTop = document.getElementById('scrollTop');
            if (window.scrollY > 300) {
                scrollTop.classList.add('show');
            } else {
                scrollTop.classList.remove('show');
            }
        });

        // Scroll to top click
        document.getElementById('scrollTop').addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Search functionality
        const searchInput = document.getElementById('searchEndpoint');
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.accordion-item, section').forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                    item.style.animation = 'fadeIn 0.3s';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Copy code functionality
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('copy-btn') || e.target.closest('.copy-btn')) {
                const btn = e.target.classList.contains('copy-btn') ? e.target : e.target.closest('.copy-btn');
                const code = btn.parentElement.querySelector('code').textContent;

                navigator.clipboard.writeText(code).then(() => {
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                    btn.classList.add('copied');

                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.classList.remove('copied');
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy:', err);
                });
            }
        });

        // Add copy buttons to all code samples
        document.querySelectorAll('pre.code-sample').forEach(pre => {
            if (!pre.querySelector('.copy-btn')) {
                const btn = document.createElement('button');
                btn.className = 'copy-btn';
                btn.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
                pre.appendChild(btn);
            }
        });

        // Toggle Request/Response tab panels
        document.querySelectorAll('.rr-tabs').forEach(function(tabs) {
            tabs.addEventListener('click', function(e) {
                const tab = e.target.closest('.rr-tab');
                if (!tab) return;

                const endpoint = tabs.dataset.endpoint;
                tabs.querySelectorAll('.rr-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const key = tabs.dataset.endpoint;
                const content = document.querySelector('.rr-content[data-endpoint="' + key + '"]');
                if (!content) return;

                content.querySelectorAll('.rr-panel').forEach(p => p.classList.add('d-none'));
                const panel = content.querySelector('.rr-panel[data-tab="' + tab.dataset.tab + '"]');
                if (panel) panel.classList.remove('d-none');
            });
        });

        // Add hover effect to tables
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Console welcome message
        console.log('%c🚀 E-Commerce API Documentation', 'font-size: 20px; font-weight: bold; color: #6366f1;');
        console.log('%cBuilt with Laravel & Sanctum', 'font-size: 14px; color: #64748b;');
    </script>
</body>

</html>
