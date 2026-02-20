# ☄️ Aether-PHP

<div align="center">

```php
     █████╗ ███████╗████████╗██╗  ██╗███████╗██████╗         ██████╗ ██╗  ██╗██████╗
     ██╔══██╗██╔════╝╚══██╔══╝██║  ██║██╔════╝██╔══██╗        ██╔══██╗██║  ██║██╔══██╗
     ███████║█████╗     ██║   ███████║█████╗  ██████╔╝ █████╗ ██████╔╝███████║██████╔╝
    ██╔══██║██╔══╝     ██║   ██╔══██║██╔══╝  ██╔══██╗ ╚════╝ ██╔═══╝ ██╔══██║██╔═══╝
██║  ██║███████╗   ██║   ██║  ██║███████╗██║  ██║        ██║     ██║  ██║██║
╚═╝  ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝        ╚═╝     ╚═╝  ╚═╝╚═╝
```

**The divine lightweight PHP framework**

![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-Source--Available-3B54C0?style=for-the-badge)
![Size](https://img.shields.io/badge/size-%3C1MB-3B54C0?style=for-the-badge)
![Dependencies](https://img.shields.io/badge/dependencies-0-777BB4?style=for-the-badge)
  
![Stars](https://img.shields.io/github/stars/dawnl3ss/Aether-PHP?style=for-the-badge&logo=github&logoColor=white&color=C8CDFB)
![Forks](https://img.shields.io/github/forks/dawnl3ss/Aether-PHP?style=for-the-badge&logo=github&logoColor=white&color=C8CDFB)


**Zero dependencies • < 1 MB • Pure PHP 8.3+**

Built from scratch. No Composer. No bloat.

</div>

---

## 🎯 Overview


Reclaim your freedom from bloated frameworks. **Aether-PHP** is a pure PHP 8.3+ framework engineered for speed, simplicity, and seamless integration. At under 1MB with zero dependencies, it's the perfect backend for modern frontend apps - React, Vue, Svelte, or vanilla JS. Embed it, extend it, own it.  
  
Built with a fully OOP-oriented architecture, Aether is designed from day one to be dropped into any project without friction. It's not just lightweight - it's your secret weapon for delivering high-performance freelance projects faster than ever.  

### Why Aether?

- **Performance First**: Optimized for speed with minimal overhead, boot-time tested
- **Zero Dependencies**: No Composer, no vendor bloat, just pure PHP 8.3+
- **OOP Architecture**: Fully object-oriented design with strict types throughout
- **Security Built-in**: CSRF protection with anti-replay, rate limiting, secure sessions, Argon2ID password hashing
- **Developer Experience**: Annotation-based routing, automatic controller scanning, clean API
- **Lightweight**: Under 1MB footprint, perfect for embedded deployments
- **Modular**: Extensible module system (CLI, I18n) with easy integration

---

## ✨ Core Features

### 🛣️ Router System

**Annotation-based routing** with automatic controller discovery. Define routes using PHP Doc comments-no configuration files needed.

```php
/**
 * [@base] => /api/v1
 */
class ApiController extends Controller {

    /**
     * [@method] => GET
     * [@route] => /users/{id}
     */
    public function getUser($id) {
        # - Route automatically registered: GET /api/v1/users/{id}
    }
}
```

- **Automatic scanning** of `app/App/Controller/` and `app/App/Controller/Api/`
- **Dynamic route parameters** with automatic sanitization
- **Base path support** via `@base` annotation
- **Method support**: GET, POST, PUT, DELETE

### 💾 Database Layer

**Fluent Query Builder** with multi-driver support. Type-safe database operations with prepared statements.

```php
# - Fluent query builder
$users = Aether()->_db()->_mysql('mydb')
    ->_table('users')
    ->_select('id', 'name', 'email')
    ->_where('status', 'active')
    ->_join('profiles', 'users.id = profiles.user_id')
    ->_send();

# - Insert
Aether()->_db()->_mysql('mydb')
    ->_table('users')
    ->_insert('name', 'John')
    ->_insert('email', 'john@example.com')
    ->_send();

# - Update
Aether()->_db()->_mysql('mydb')
    ->_table('users')
    ->_update()
    ->_set('name', 'Jane')
    ->_where('id', 1)
    ->_send();
```

**Supported Drivers:**
- MySQL (via PDO)
- SQLite (via PDO)
- Extensible via `DatabaseDriver` interface

**Query Methods:**
- `_select()` - SELECT queries with joins
- `_insert()` - INSERT operations
- `_update()` - UPDATE with WHERE clauses
- `_delete()` - DELETE operations
- `_exist()` - Check record existence
- `_drop()` - DROP table operations
- `_raw()` - Execute raw SQL queries

### 🔐 Authentication System

**Complete authentication framework** with user management, session handling, and gateway pattern.

```php
# - Login gateway
$auth = new LoginAuthGateway($email, $password);
if ($auth->_tryAuth()) {
    # - User logged in, session created
}

# - Check authentication status
if (Aether()->_session()->_auth()->_isLoggedIn()) {
    $user = Aether()->_session()->_auth()->_getUser();
    # - Access user properties: uid, username, email, perms
}
```

**Features:**
- Password hashing with **Argon2ID**
- Session-based authentication
- User instance management
- Configurable auth tables via `.env`
- Gateway pattern for extensibility

### 🔄 Middleware Pipeline

**Composable middleware system** with built-in security middleware.

```php
# - In app/App/App.php
private static $_middlewares = [
    RatelimitMiddleware::class,      # - 100 requests per 60 seconds
    CsrfMiddleware::class,            # - CSRF protection
    SecurityHeadersMiddleware::class, # - Security headers
    AuthMiddleware::class,            # - Authentication guard
    MaintenanceMiddleware::class      # - Maintenance mode
];
```

**Built-in Middleware:**
- **CsrfMiddleware**: CSRF token validation with anti-replay protection
- **RatelimitMiddleware**: IP-based rate limiting (100 req/60s, configurable)
- **SecurityHeadersMiddleware**: Comprehensive security headers (CSP, X-Frame-Options, etc.)
- **AuthMiddleware**: Route protection requiring authentication
- **MaintenanceMiddleware**: Maintenance mode support

### 🌐 HTTP Layer

**Type-safe HTTP request/response handling** with multiple format support.

```php
# - JSON response
Aether()->_http()->_response()->_json([
    'status' => 'success',
    'data' => $data
], 200)->_send();

# - HTML response
Aether()->_http()->_response()->_html($html, 200)->_send();

# - XML, TEXT, PDF formats also supported
```

**Response Formats:**
- JSON (automatic encoding)
- HTML
- XML
- TEXT
- PDF

**Request Handling:**
- `HttpParameterUnpacker` for extracting JSON from `php://input`
- Type-safe parameter access
- Automatic content-type detection

### 📝 View System

**Template rendering** with variable extraction and security checks.

```php
# - In controller
protected function _render(string $view, array $params = []) {
    ViewInstance::_make($view, $params);
}

# - Usage
$this->_render('home', [
    'loggedin' => Aether()->_session()->_auth()->_isLoggedIn(),
    'user' => $user
]);
```

Views are located in `public/views/` and support PHP templating with automatic variable extraction.

### 💨 Cache System

**Pluggable caching** with adapter pattern.

```php
# - APCU cache
$cache = Aether()->_cache()->_apcu();
$cache->_set('key', $value, 3600); # - TTL in seconds
$value = $cache->_get('key');
```

**Current Adapters:**
- **APCU** (implemented)
- Files, Redis, Memcached (planned, extensible via interface)

### 🔧 Service Manager

**Centralized service access** via the `Aether()` helper function.

```php
# - Access all services through one entry point
Aether()->_db()->_mysql('database');
Aether()->_cache()->_apcu();
Aether()->_http()->_response();
Aether()->_session()->_auth();
Aether()->_io()->_file();
Aether()->_config()->_get('KEY');
```

**Service Hubs:**
- `_db()` - Database operations
- `_cache()` - Caching layer
- `_http()` - HTTP request/response
- `_session()` - Session management
- `_io()` - File I/O operations
- `_config()` - Configuration access

### 🌍 Modules

**Extensible module system** for adding functionality.

**Built-in Modules:**
- **AetherCLI**: Command-line interface (`bin/aether`)
- **I18n**: Internationalization with `__()` helper function

**Module Structure:**
```
src/Aether/Modules/YourModule/
├── module.yml
└── src/
    └── YourModule.php (extends AetherModule)
```

### 🔒 Security Features

- **CSRF Protection**: Token-based with automatic regeneration (anti-replay)
- **Rate Limiting**: IP-based throttling (100 requests per 60 seconds)
- **Security Headers**: CSP, X-Frame-Options, Referrer-Policy, etc.
- **Secure Sessions**: HttpOnly, Secure, SameSite=Strict cookies
- **Password Hashing**: Argon2ID algorithm
- **Input Validation**: Automatic sanitization via `UserInputValidatorTrait`
- **Session Security**: Encoded session data with security layer

### 📁 File I/O System

**Type-safe file operations** with parser support.

```php
# - Read JSON file
$data = Aether()->_io()->_file(IOTypeEnum::JSON, 'data.json')->_readDecoded();

# - Write ENV file
Aether()->_io()->_file(IOTypeEnum::ENV, '.env')->_write($content);

# - Read lines
$lines = Aether()->_io()->_file(IOTypeEnum::TEXT, 'file.txt')->_readLines();
```

**Supported Types:**
- JSON (with encoding/decoding)
- ENV (environment files)
- TEXT (plain text)

---

## 📦 Requirements

- **PHP**: 8.3 or higher
- **Web Server**: Apache, Nginx, or PHP built-in server
- **Database**: MySQL or SQLite (optional, via PDO)
- **Extensions**: PDO, APCU (for caching, optional)

---

## 🚀 Installation

### Clone the Repository

```bash
git clone https://github.com/Aether-PHP/Aether-PHP.git
cd Aether-PHP
```

### Configure Environment

Create a `.env` file in the root directory:

```env
APP_ENV=dev
PROJECT_NAME=Aether App

# Database Configuration
DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_user
DB_PASS=your_password

# Authentication Configuration
AUTH_DATABASE_GATEWAY=your_database
AUTH_TABLE_GATEWAY=users
```

---

## ⚡ Quick Start

### Basic Application Structure

```php
<?php
# - index.php
require_once __DIR__ . '/autoload.php';

$app = new \Aether\Aether();
$app->_run();
```

### Define Routes with Annotations

```php
<?php
# - app/App/Controller/HomeController.php
namespace App\Controller;

use App\AppController;

class HomeController extends AppController {
    /**
     * [@method] => GET
     * [@route] => /
     */
    public function home() {
        $this->_render('home', [
            'loggedin' => Aether()->_session()->_auth()->_isLoggedIn(),
        ]);
    }
}
```

### Create an API Controller

```php
<?php
# - app/App/Controller/Api/UserController.php
namespace App\Controller\Api;

use Aether\Router\Controller\Controller;

/**
 * [@base] => /api/v1/users
 */
class UserController extends Controller {
    /**
     * [@method] => GET
     * [@route] => /{id}
     */
    public function show($id) {
        $user = Aether()->_db()->_mysql($_ENV['DB_NAME'])
            ->_table('users')
            ->_select('*')
            ->_where('id', $id)
            ->_send();
        
        if (empty($user)) {
            return Aether()->_http()->_response()->_json([
                'error' => 'User not found'
            ], 404)->_send();
        }
        
        return Aether()->_http()->_response()->_json($user[0], 200)->_send();
    }
    
    /**
     * [@method] => POST
     * [@route] => /
     */
    public function create() {
        $unpacker = new \Aether\Http\HttpParameterUnpacker();
        $name = $unpacker->_getAttribute('name');
        $email = $unpacker->_getAttribute('email');
        
        Aether()->_db()->_mysql($_ENV['DB_NAME'])
            ->_table('users')
            ->_insert('name', $name)
            ->_insert('email', $email)
            ->_send();
        
        return Aether()->_http()->_response()->_json([
            'status' => 'success',
            'message' => 'User created'
        ], 201)->_send();
    }
}
```

### Database Query Examples

```php
# - Select with conditions
$users = Aether()->_db()->_mysql('mydb')
    ->_table('users')
    ->_select('id', 'name', 'email')
    ->_where('status', 'active')
    ->_send();

# - Check existence
$exists = Aether()->_db()->_mysql('mydb')
    ->_table('users')
    ->_exist()
    ->_where('email', 'user@example.com')
    ->_send();

# - Raw query
$result = Aether()->_db()->_mysql('mydb')->_raw("SELECT COUNT(*) FROM users");
```
---

### Design Principles

- **Separation of Concerns**: Each component has a single, well-defined responsibility
- **Dependency Injection**: Services managed through ServiceManager
- **Interface-Based**: Core functionality defined through interfaces for flexibility
- **Middleware Pipeline**: Request processing through composable middleware
- **Module System**: Extensible architecture via modules
- **Annotation-Based**: Routes defined via PHP Doc comments, no config files
- **Type Safety**: Strict types (`declare(strict_types=1)`) throughout

---

## 📚 Advanced Usage

### Custom Middleware

```php
<?php
namespace App\Middleware;

use Aether\Middleware\MiddlewareInterface;

class CustomMiddleware implements MiddlewareInterface {
    public function _handle(callable $_next) {
        # - Before request
        # - ...
        
        $_next();
        
        # - After request
        # - ...
    }
}
```

Register in `app/App/App.php`:
```php
private static $_middlewares = [
    CustomMiddleware::class,
    # - ... other middleware
];
```

### Custom Module

```php
<?php
namespace App\Modules\CustomModule;

use Aether\Modules\AetherModule;

class CustomModule extends AetherModule {
    protected string $_name = 'CustomModule';
    protected float $_version = 1.0;
    protected string $_description = 'Custom module description';
    
    public function _onLoad() {
        # - Module initialization logic
    }
    
    public static function _make(): AetherModule {
        return new self();
    }
}
```

### Environment Configuration

Access configuration via `ProjectConfig`:

```php
use Aether\Config\ProjectConfig;

$value = ProjectConfig::_get('DB_HOST', 'localhost'); # - with default
$value = ProjectConfig::_get('DB_HOST'); # - without default
```

### CLI Usage

```bash
# Run CLI commands
php bin/aether [command]

# Or make executable
chmod +x bin/aether
./bin/aether [command]
```

---

## 📄 License

**Source-Available License**

© 2025-present dawnl3ss - All rights reserved

**You are allowed to:**
- View and study the source code
- Use Aether-PHP in your personal and commercial projects
- Modify it for your own needs

**You are NOT allowed to:**
- Redistribute Aether-PHP (modified or not)
- Remove this license or the "Aether-PHP" branding
- Sell or sublicense Aether-PHP itself

For commercial redistribution licensing, contact: **alexandre.voisin@epita.fr**

> This is NOT an open-source license.

---

## 🤝 Contributing

While Aether-PHP is source-available, contributions and feedback are welcome. For questions, suggestions, or support:

- **GitHub Issues**: Report bugs or request features
- **Email**: alexandre.voisin@epita.fr
- **Website**: [getaether.space](https://getaether.space)

---

## 👤 Author

**dawnl3ss** (Alexandre VOISIN)

- LinkedIn: [alexvsn](https://www.linkedin.com/in/alexvsn/)
- Website: [dawnless.me](https://dawnless.me)
- Hardware Hub: [hardware-hub.fr](https://hardware-hub.fr)

---

<div align="center">

[⭐ Star on GitHub](https://github.com/Aether-PHP/Aether-PHP) • [📖 Documentation](https://getaether.space/en/docs) • [🐛 Report Bug](https://github.com/Aether-PHP/Aether-PHP/issues)

</div>
