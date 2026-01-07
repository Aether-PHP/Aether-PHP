# ☄️ Aether-PHP

<div align="center">

```
     █████╗ ███████╗████████╗██╗  ██╗███████╗██████╗         ██████╗ ██╗  ██╗██████╗
     ██╔══██╗██╔════╝╚══██╔══╝██║  ██║██╔════╝██╔══██╗        ██╔══██╗██║  ██║██╔══██╗
     ███████║█████╗     ██║   ███████║█████╗  ██████╔╝ █████╗ ██████╔╝███████║██████╔╝
    ██╔══██║██╔══╝     ██║   ██╔══██║██╔══╝  ██╔══██╗ ╚════╝ ██╔═══╝ ██╔══██║██╔═══╝
██║  ██║███████╗   ██║   ██║  ██║███████╗██║  ██║        ██║     ██║  ██║██║
╚═╝  ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝        ╚═╝     ╚═╝  ╚═╝╚═╝
```

**The divine lightweight PHP framework**

![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-Source--Available-yellow?style=for-the-badge)
![Size](https://img.shields.io/badge/size-%3C1MB-4CAF50?style=for-the-badge)
![Dependencies](https://img.shields.io/badge/dependencies-0-red?style=for-the-badge)

**< 1 MB • Zero dependencies • Pure PHP 8.3+**

Built from scratch. No Composer. No bloat.  
Because waiting 8 seconds to install 147 packages just to echo "Hello World" is a crime.

[Features](#-features) • [Quick Start](#-quick-start) • [Documentation](#-documentation) • [Roadmap](#-roadmap) • [License](#-license)

</div>

---

## 🚀 Why Aether-PHP ?

Modern PHP frameworks are **bloated**. Laravel needs 100+ MB of dependencies just to start. Symfony isn't much better.

**Aether-PHP is different:**
- **Blazingly fast** : Zero overhead, pure PHP execution
- **Incredibly lightweight** : < 1 MB total size
- **Security-first** : Argon2id hashing, input sanitization, secure sessions
- **Zero dependencies** : No Composer packages, no external libraries
- **Production-ready** : Full auth system, routing, database wrapper included
- **Easily extensible** : Gateway pattern, traits, and clean OOP architecture

Perfect for SaaS, REST APIs, web apps, and microservices where performance matters.

---

## ✨ Features

### Core Framework
- ✅ **Automatic routing** via PHPDoc annotations (`@route`, `@method`)
- ✅ **RESTful API support** with automatic JSON responses
- ✅ **Clean MVC architecture** with Controller Gateway system
- ✅ **Input sanitization** and security traits
- ✅ **Strict types** and modern PHP 8 features

### Authentication System
- ✅ **Complete auth flow** (login, register, logout)
- ✅ **User instance serialization** (UID, username, email, permissions)
- ✅ **Gateway pattern** for extensible auth logic

### Database
- ✅ **PDO wrapper** with prepared statements
- ✅ **Query builder** for SELECT, INSERT, and more
- ✅ **Multi-driver support** (MySQL ready, SQLite coming soon - as well as Mongodb maybe lol)
- ✅ **Type-safe parameter binding**

---

## 📦 Installation

### Requirements
- PHP 8.3 or higher
- Apache2 with mod_rewrite
- MySQL/MariaDB (for auth system)

### Setup

**1. Clone the repository**

```bash
git clone https://github.com/dawnl3ss/Aether-PHP.git
cd Aether-PHP
```

**2. Configure your database**

Edit `src/Aether/Config/ProjectConfig.php`:

```php
const string DATABASE_ADDRESS = '127.0.0.1';
const string DATABASE_USERNAME = 'root';
const string DATABASE_PASSWORD = 'root';
const string AUTH_DATABASE_GATEWAY = "aetherphp";
const string AUTH_TABLE_GATEWAY = "users";
```

**3. Import the database structure**

```bash
mysql -u root -p < db_structure.sql
```

**4. Configure Apache**

Make sure `.htaccess` is enabled and `mod_rewrite` is active:

```apache
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . index.php [L]
DirectorySlash Off
```

**5. Test the installation**

Navigate to `http://localhost/` and you should see the homepage.

**Default admin credentials:**
- Email: `admin@gmail.com`
- Password: `aetherphp`

---

## 📚 Documentation

### Creating Your First Controller

Controllers are automatically discovered and registered by the `ControllerGateway`.  
  
Create a file in `app/App/Controller/MyController.php`:  

```php
<?php
declare(strict_types=1);

namespace App\Controller;

class MyController {
    
    /**
     * Homepage
     * 
     * [@method] => GET
     * [@route] => /
     */
    public function index() {
        echo "Welcome to Aether-PHP!";
    }
    
    /**
     * User profile with dynamic parameter
     * 
     * [@method] => GET
     * [@route] => /user/{id}
     */
    public function profile($id) {
        echo "User ID: " . $id;
    }
}
```

**That's it !** The router automatically detects your controller and registers the routes.  

**! Be carreful !** : PHPDoc must follow a certain style

```php
    /**
     * Some text/phpdoc
     * 
     * [@method] => {METHOD} <- !! 
     * [@route] => {ROUTE} <- !!
     */
    public function whateveryouwant() {
        echo "Welcome to Aether-PHP!";
    }
}
```

If the PHPDoc does not contain `@method` nor `@route`, the Controller Gateway will not be able to recognize the routes.

### Creating API Endpoints

API controllers go in `app/App/Controller/Api/` and return JSON responses.

```php
<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Aether\Api\Format\JsonResponse;use Aether\Http\HttpParameterUnpacker;

class ProductApiController {
    
    public function __construct() {
        header('Content-Type: application/json');
    }
    
    /**
     * Get all products
     * 
     * [@method] => GET
     * [@route] => /api/v1/products
     */
    public function list() {
        $response = new JsonResponse();
        $response->_add("status", "success")
                 ->_add("products", [
                     ["id" => 1, "name" => "Product A"],
                     ["id" => 2, "name" => "Product B"]
                 ]);
        
        echo $response->_encode();
    }
    
    /**
     * Create a product
     * 
     * [@method] => POST
     * [@route] => /api/v1/products/create
     */
    public function create() {
        $params = new HttpParameterUnpacker();
        $name = $params->_getAttribute("name");
        
        $response = new JsonResponse();
        
        if (!$name) {
            $response->_add("status", "error")
                     ->_add("message", "Product name required");
        } else {
            $response->_add("status", "success")
                     ->_add("message", "Product created");
        }
        
        echo $response->_encode();
    }
}
```

### Using the Database Wrapper

The `DatabaseWrapper` provides a clean, secure interface to your database.

```php
<?php
use Aether\Database\DatabaseWrapper;

// Initialize wrapper
$db = new DatabaseWrapper("your_database_name");

// SELECT query
$users = $db->_select("users", "*", ["email" => "admin@gmail.com"]);

// SELECT with multiple conditions
$activeUsers = $db->_select("users", "username, email", [
    "status" => "active",
    "role" => "admin"
]);

// INSERT query
$db->_insert("users", [
    "username" => "johndoe",
    "email" => "john@example.com",
    "password_hash" => password_hash("secret", PASSWORD_ARGON2ID),
    "perms" => ""
]);

// Check if record exists
if ($db->_exist("users", ["email" => "john@example.com"])) {
    echo "User already exists!";
}
```

### Authentication System

Aether-PHP comes with a complete authentication system out of the box.

#### Registering Users

```php
use Aether\Auth\Gateway\RegisterAuthGateway;

$gateway = new RegisterAuthGateway(
    "john_doe",              // username
    "john@example.com",      // email
    "securepassword123"      // password
);

if ($gateway->_tryAuth()) {
    echo $gateway->_getStatus(); // "user successfully signed up."
} else {
    echo $gateway->_getStatus(); // error message
}
```

#### Logging In

```php
use Aether\Auth\Gateway\LoginAuthGateway;

$gateway = new LoginAuthGateway(
    "john@example.com",      // email
    "securepassword123"      // password
);

if ($gateway->_tryAuth()) {
    echo "Logged in successfully!";
}
```

#### Checking Authentication

```php
use Aether\Auth\User\UserInstance;

if (UserInstance::_isLoggedIn()) {
    $user = unserialize($_SESSION["user"]);
    echo "Welcome, " . $user->_getUsername();
}
```

#### Logging Out

```php
use Aether\Auth\Gateway\LogoutAuthGateway;

$gateway = new LogoutAuthGateway();
$gateway->_tryAuth();
```


## 🎯 Architecture Overview

### Project Structure

```
Aether-PHP/
├── app/
│   └── App/
│       └── Controller/
│           ├── Api/
│           │   ├── ApiController.php
│           │   └── AuthApiController.php
│           └── AppController.php
├── src/
│   └── Aether/
│       ├── Api/
│       ├── Auth/
│       │   ├── Gateway/
│       │   ├── Security/
│       │   └── User/
│       ├── Config/
│       ├── Modules/
│       │   └── Database/
│       ├── Router/
│       │   ├── Http/ 
│       │   └── Route/
│       └── Security/
├── autoload.php                 # PSR-4 autoloader
├── index.php                    # Application entry point
├── .htacces
└── db_structure.sql
```

### How Routing Works

1. All requests go through `index.php`
2. The `ControllerGateway` scans all controllers in `app/App/Controller/` or `app/App/Controller/Api` for APIs
3. It reads PHPDoc annotations (`@method` and `@route`)
4. Routes are automatically registered in the `Router`
5. The `Router` matches the current request and executes the appropriate controller method

### Database Architecture

- **DatabaseDriver** - Abstract base class for all database drivers
- **DatabasePdoDriver** - PDO implementation with MySQL support
- **DatabaseWrapper** - High-level interface for queries (SELECT, INSERT, etc.)
- All queries use prepared statements for security

### Authentication Flow

1. User submits credentials
2. Appropriate **AuthGateway** is created (Login, Register, or Logout)
3. Gateway performs validation and database operations
4. On success, **UserInstance** is created and serialized in `$_SESSION`
5. `UserInstance::_isLoggedIn()` checks for valid session

---

## 🛣️ Roadmap

- [ ] Route caching system (dev -> prod in 1ms)
- [ ] Middleware support (rate limiting, CORS, authentication, etc.)
- [ ] Full RBAC permission system
- [ ] Admin dashboard
- [ ] Module/plugin architecture
- [ ] SQLite database driver
- [ ] Template engine integration
- [ ] Form validation helpers
- [ ] File upload handling
- [ ] Email service integration
- [ ] CLI tool for scaffolding
- [ ] Unit testing framework integration
- [ ] API documentation generator

---

## 🤝 Contributing

This project is **source-available** but not open-source. Contributions are welcome but subject to review.

If you find bugs or have feature requests:
1. Open an issue on GitHub
2. Describe the problem or feature clearly
3. Include code examples if applicable

For major changes or commercial use, contact me : **alexandre.voisin@epita.fr**

---

## 📄 License

**Aether-PHP Framework — Source-Available License**  
© 2025-present dawnl3ss — All rights reserved

### You are allowed to:
- ✅ View and study the source code
- ✅ Use Aether-PHP in your personal and commercial projects
- ✅ Modify it for your own needs

### You are NOT allowed to:
- ❌ Redistribute Aether-PHP (modified or not)
- ❌ Remove this license or the "Aether-PHP" branding
- ❌ Sell or sublicense Aether-PHP itself

**Want to redistribute or sell Aether-PHP-based products?**  
→ Contact **alexandre.voisin@epita.fr** for commercial licensing

This is **NOT** an open-source license. See [LICENSE](LICENSE) for full terms.

---

## 🎯 Use Cases

Aether-PHP is perfect for:

- **Microservices** - Minimal footprint, maximum performance
- **REST APIs** - Built-in JSON response handling
- **SaaS Applications** - Authentication and database ready
- **Backend for web or mobile apps** - Fast, secure, scalable
- **Game servers** - Lightweight, low latency
- **Internal tools** - Quick setup, no dependency hell

---

## 💬 Final Word

Started PHP at 11. Now I'm 20.  
This is the cleanest, fastest, most embeddable thing I've ever built.

**Star if you hate bloat too.**  
**Fork if you're crazy enough to make it even faster**

- [Dawnl3ss](https://github.com/dawnl3ss)

---
