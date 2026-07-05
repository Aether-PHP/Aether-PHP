<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3+-4F5B93?style=flat-square&logo=php&logoColor=white" alt="PHP 8.3+"/>
  <img src="https://img.shields.io/badge/footprint-%3C_1MB-1a1a2e?style=flat-square" alt="< 1MB"/>
  <img src="https://img.shields.io/badge/dependencies-none-1a1a2e?style=flat-square" alt="Zero deps"/>
  <img src="https://img.shields.io/badge/license-Source--Available-1a1a2e?style=flat-square" alt="License"/>
  <img src="https://img.shields.io/github/stars/Aether-PHP/Aether-PHP?style=flat-square&color=4F5B93" alt="Stars"/>
</p>

<h1 align="center">Aether-PHP</h1>

<p align="center">
A ground-up PHP 8.3+ framework built for speed, security, and surgical precision.<br/>
No Composer. No vendor directory. No runtime dependencies. Just PHP.
</p>

---

## What is this

Aether is a self-contained backend framework that ships everything needed to build REST APIs, web applications, and SaaS backends in a single directory under 1MB. It has its own autoloader, its own query builder, its own session layer, its own middleware pipeline. Nothing is imported. Everything is strict-typed.

It is designed to be dropped into any project and run immediately — no package manager, no install step, no configuration ceremony.

```
public/index.php  →  autoload.php  →  Aether::_run()  →  your controllers
```

---

## Architecture at a Glance

```
.
├── public/             # Web root — index.php + .htaccess
├── app/App/            # Your application code (controllers, models)
├── src/Aether/         # Framework core (router, DB, auth, HTTP, security...)
├── storage/            # Runtime data (sessions, file cache)
├── bin/                # CLI entry point
├── tests/              # PHPUnit test suite
└── benchmarks/         # Boot-time performance tests
```

All services are accessed through a single entry point:

```php
Aether()->_db()       // Database query builder
Aether()->_http()     // HTTP client & response factory
Aether()->_cache()    // Cache adapters (APCu, file-based)
Aether()->_session()  // Session & authentication state
Aether()->_io()       // File I/O with typed parsers
Aether()->_config()   // Environment configuration
```

---

## Routing

Routes are declared via PHPDoc annotations directly on controller methods. No route files. No configuration arrays. The framework scans `app/App/Controller/` at boot, reflects each class, and builds the route table automatically.

```php
/**
 * [@base] => /api/users
 */
class UserController extends Controller {

    /**
     * [@method] => GET
     * [@route] => /{id}
     */
    public function show($id) {
        // GET /api/users/123 → $id = "123" (sanitized)
    }

    /**
     * [@method] => POST
     * [@route] => /
     * [@middlewares] => AuthMiddleware
     */
    public function create() {
        // POST /api/users — requires authentication
    }
}
```

- Supports `GET`, `POST`, `PUT`, `DELETE`, `QUERY`
- Dynamic parameters (`{id}`) are automatically extracted and sanitized
- Per-route middleware via `[@middlewares]` annotation
- Compiled route table cached in APCu (1h TTL) for zero-reflection production boots

---

## Database

A fluent query builder over PDO with driver abstraction. Currently ships MySQL and SQLite drivers.

```php
// SELECT with joins, ordering, limits
$posts = Aether()->_db()->_mysql('blog')
    ->_table('posts')
    ->_select('posts.id', 'posts.title', 'users.name')
    ->_join('users', 'posts.author_id = users.id')
    ->_where('posts.published', 1)
    ->_orderBy('posts.created_at', 'DESC')
    ->_limit(10)
    ->_send();

// INSERT
Aether()->_db()->_mysql('blog')
    ->_table('posts')
    ->_insert('title', 'Hello World')
    ->_insert('author_id', 1)
    ->_send();

// UPDATE with WHERE
Aether()->_db()->_mysql('blog')
    ->_table('posts')
    ->_set('title', 'Updated Title')
    ->_where('id', 42)
    ->_send();

// EXISTS check
$taken = Aether()->_db()->_mysql('app')
    ->_table('users')
    ->_exist()
    ->_where('email', 'john@example.com')
    ->_send(); // returns bool

// COUNT
$total = Aether()->_db()->_mysql('app')
    ->_table('users')
    ->_count('id')
    ->_where('active', 1)
    ->_send(); // returns int

// Raw SQL
$result = Aether()->_db()->_mysql('app')
    ->_raw("SELECT VERSION()");
```

All queries use prepared statements. Table/column identifiers are validated against a strict allowlist regex before interpolation.

---

## Middleware

A functional pipeline built with `array_reduce`. Middleware classes implement a single method:

```php
class LoggingMiddleware implements MiddlewareInterface {
    public function _handle(callable $_next) {
        // before
        error_log('[' . date('H:i:s') . '] ' . $_SERVER['REQUEST_URI']);

        $_next(); // pass to next middleware or final handler

        // after
    }
}
```

Register globally in `app/App/App.php`:

```php
Aether::$_middlewares = [
    MaintenanceMiddleware::class,
    RatelimitMiddleware::class,
    CsrfMiddleware::class,
    SecurityHeadersMiddleware::class,
    CorsMiddleware::class,
];
```

Or per-route via the `[@middlewares]` annotation on controller methods.

### Built-in middleware

| Middleware | Behavior |
|---|---|
| `RatelimitMiddleware` | IP-based throttling. Configurable via `RATELIMIT_MAX_LIMIT` and `RATELIMIT_SECOND_INTERVAL` env vars. |
| `CsrfMiddleware` | Token validation on state-changing requests. Anti-replay: token regenerates after each successful verify. Exposed via `X-CSRF-Token` header. |
| `SecurityHeadersMiddleware` | Sets `X-Content-Type-Options`, `X-Frame-Options: DENY`, `Referrer-Policy`, `Permissions-Policy`, `Cross-Origin-Opener-Policy`, `Cross-Origin-Resource-Policy`. |
| `CorsMiddleware` | Cross-origin resource sharing headers. |
| `MaintenanceMiddleware` | Returns 503 when `MAINTENANCE=true` in `.env`. |
| `AuthMiddleware` | Rejects unauthenticated requests. |

---

## Authentication

Session-based auth with Argon2ID password hashing. The system provides login, register, and logout gateways out of the box.

```php
// Login
$gateway = new LoginAuthGateway($email, $password);
if ($gateway->_tryAuth()) {
    // session created, user stored
}

// Check state
if (Aether()->_session()->_auth()->_isLoggedIn()) {
    $user = Aether()->_session()->_auth()->_getUser();
    // $user->uid, $user->username, $user->email, $user->perms
}

// Logout
(new LogoutAuthGateway())->_tryAuth();
```

Auth tables and database are configurable via `.env` (`AUTH_DATABASE_GATEWAY`, `AUTH_TABLE_GATEWAY`). A SQL schema for the users table is included at `src/Aether/Database/Auth/db_structure.sql`.

---

## Sessions

Sessions are stored as files in `storage/sessions/` with a security layer that encodes all session values as `base64(data + ":::" + HMAC-SHA256(data, secret))`. This prevents tampering even if session files are leaked.

Configuration:
- `SESSION_HMAC` — 32-character secret key for HMAC signing
- `COOKIE_SESSION_TTL` — session lifetime in minutes
- `SESSION_COOKIE_SECURE` — enforce Secure flag on cookie

---

## HTTP Client

An outbound HTTP client with built-in SSRF protection:

```php
$request = Aether()->_http()->_request('https://api.example.com/data', HttpMethodEnum::GET);
$response = $request->_send();
// $response->_getBody(), $response->_getCode()
```

SSRF hardening (enabled by default):
- Only `http://` and `https://` schemes allowed
- Private/reserved IP ranges blocked (127.x, 10.x, 192.168.x, etc.)
- DNS resolution checked — no rebinding bypasses
- Disable with `HTTP_ALLOW_PRIVATE=1` for internal services

### Response Factory

```php
Aether()->_http()->_response()->_json(['ok' => true], 200)->_send();
Aether()->_http()->_response()->_html($html, 200)->_send();
Aether()->_http()->_response()->_xml($xml, 200)->_send();
Aether()->_http()->_response()->_text($text, 200)->_send();
```

---

## Cache

Two adapters available:

```php
// APCu (in-memory, shared across requests in the same worker)
Aether()->_cache()->_apcu()->_set('key', $value, 3600);
Aether()->_cache()->_apcu()->_get('key');
Aether()->_cache()->_apcu()->_has('key');

// File-based (stored in storage/cache/ as JSON)
Aether()->_cache()->_files()->_set('key', $data, 300);
Aether()->_cache()->_files()->_get('key');
```

The route table is cached in APCu to skip reflection on subsequent requests.

---

## Modules

Pluggable module system with lifecycle hooks. Modules live in `src/Aether/Modules/` and are loaded via `ModuleFactory::_load()`.

```php
class CustomModule extends AetherModule {
    protected string $_name = 'MyModule';
    protected float $_version = 1.0;

    public function _onLoad() {
        // runs when the module is loaded
    }

    public static function _make(): AetherModule {
        return new self();
    }
}
```

### Included modules

- **AetherCLI** — Command-line interface accessible via `php bin/aether`. Argument parsing, colored output, extensible command system.
- **I18n** — Internationalization via `__('key')` helper. Translation files organized by locale (`lang/en/`, `lang/fr/`).

---

## File I/O

Typed file operations with parser support:

```php
// JSON
$data = Aether()->_io()->_file(IOTypeEnum::JSON, 'config.json')->_readDecoded();

// ENV
$env = Aether()->_io()->_file(IOTypeEnum::ENV, '.env')->_readDecoded();

// Text
$lines = Aether()->_io()->_file(IOTypeEnum::TEXT, 'log.txt')->_readLines();
```

---

## Project Structure

```
your-project/
├── public/
│   ├── index.php                    # Entry point
│   ├── .htaccess                    # Apache URL rewriting
│   └── views/                       # PHP template files
├── app/
│   └── App/
│       ├── App.php                  # Middleware + module registration
│       └── Controller/
│           ├── HomeController.php   # Your view controllers
│           └── Api/
│               └── V1Controller.php # Your API controllers
├── src/
│   └── Aether/                      # Framework source (don't edit)
├── storage/
│   ├── sessions/                    # Session files
│   └── cache/                       # File cache
├── bin/
│   └── aether                       # CLI entry
├── autoload.php                     # PSR-4 autoloader (Aether\, App\, modules)
├── .env                             # Environment configuration
└── phpunit.xml                      # Test configuration
```

---

## Environment Configuration

Create `.env` from `.env.example`:

```env
# Application
APP_ENV=dev
PROJECT_NAME=MyApp

# Database
DATABASE_ADDRESS=127.0.0.1
DATABASE_USERNAME=root
DATABASE_PASSWORD=secret

# Authentication
AUTH_DATABASE_GATEWAY=myapp
AUTH_TABLE_GATEWAY=users

# Sessions
COOKIE_SESSION_TTL=120
SESSION_FOLDER_PATH=../storage/sessions/
SESSION_HMAC=your-32-char-secret-key-here!!!!
SESSION_COOKIE_SECURE=1

# Rate limiting
RATELIMIT_SECOND_INTERVAL=60
RATELIMIT_MAX_LIMIT=100

# Infrastructure
MAINTENANCE=false
REDIS_ADDRESS=127.0.0.1
REDIS_PORT=6379

# HTTP client
HTTP_FOLLOW_REDIRECTS=0
HTTP_ALLOW_PRIVATE=0
```

---

## Requirements

- PHP 8.3+
- Apache / Nginx / PHP built-in server
- PDO extension (for database features)
- APCu extension (optional, for route caching)
- cURL extension (for outbound HTTP)

---

## Installation

```bash
git clone https://github.com/Aether-PHP/Aether-PHP.git
cd Aether-PHP
cp .env.example .env
# Edit .env with your settings
php -S localhost:8000 -t public/
```

No `composer install`. No `npm`. No build step. It just runs.

---

## Running Tests

```bash
./vendor/bin/phpunit
# or
php phpunit.phar
```

Test coverage includes: query builder, route matching, security traits (password hashing, HMAC, input sanitization), HTTP parameter unpacking, parsers, cache adapters.

---

## Security Summary

| Layer | Implementation |
|---|---|
| Password storage | Argon2ID (`password_hash` with `PASSWORD_ARGON2ID`) |
| CSRF | Per-session token, timing-safe comparison, anti-replay regeneration |
| Session integrity | HMAC-SHA256 signed session values, HttpOnly + Secure + SameSite cookies |
| Rate limiting | IP-based sliding window with configurable threshold |
| SQL injection | Prepared statements + identifier allowlist validation |
| SSRF | URL scheme restriction, private IP blocking, DNS rebind protection |
| Headers | X-Frame-Options DENY, nosniff, strict referrer, restrictive permissions-policy |
| Input | Automatic sanitization on route parameters via `UserInputValidatorTrait` |

---

## License

**Source-Available** — not open-source.

You may view, study, use (personal and commercial), and modify Aether-PHP for your own projects.

You may **not** redistribute, sublicense, or sell the framework itself. Branding and license notices must remain intact.

Commercial redistribution licensing available: **alexandre.voisin@epita.fr**

---

## Author

**dawnl3ss** (Alexandre VOISIN)

- [LinkedIn](https://www.linkedin.com/in/alexvsn/)
- [Website](https://dawnless.me)
- [Aether-PHP docs](https://aetherphp.net/en/docs)

---

<p align="center">
<a href="https://github.com/Aether-PHP/Aether-PHP">GitHub</a> · <a href="https://aetherphp.net/en/docs">Documentation</a> · <a href="https://github.com/Aether-PHP/Aether-PHP/issues">Report a bug</a>
</p>
