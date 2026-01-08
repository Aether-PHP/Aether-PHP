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

![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-Source--Available-3B54C0?style=for-the-badge)
![Size](https://img.shields.io/badge/size-%3C1MB-3B54C0?style=for-the-badge)
![Dependencies](https://img.shields.io/badge/dependencies-0-777BB4?style=for-the-badge)
  
![Stars](https://img.shields.io/github/stars/dawnl3ss/Aether-PHP?style=for-the-badge&logo=github&logoColor=white&color=C8CDFB)
![Forks](https://img.shields.io/github/forks/dawnl3ss/Aether-PHP?style=for-the-badge&logo=github&logoColor=white&color=C8CDFB)


**The divine lightweight PHP framework**  
**Zero dependencies • < 1 MB • Pure PHP 8.3+**

Built from scratch. No Composer. No bloat.

</div>

---

Reclaim your freedom from bloated frameworks. Aether is a pure PHP 8.3+ framework engineered for speed, simplicity, and seamless integration. At under 1MB with zero dependencies, it's the perfect backend for modern frontend apps - React, Vue, Svelte, or vanilla JS. Embed it, extend it, own it.

Built with a fully OOP-oriented architecture, Aether is designed from day one to be dropped into any project without friction. It's not just lightweight - it's your secret weapon for delivering high-performance freelance projects faster than ever.

## 🚀 Why Aether Will Change How You Build 

- Blazing Performance: Boots in milliseconds, no overhead, pure PHP power.
- Effortless Embedding: OOP design makes integration into existing projects a breeze.
- No Bloat, Ever: Zero external dependencies - everything you need, nothing you don't.
- Freelance-Ready: Scalable, secure, and modular for real-world client missions.
- Future-Proof: Actively evolving with clean, maintainable code.

Aether isn't another PHP framework - it's the solution.

## 🏗️ Architecture Deep Dive: Elegant, Modular, Performant

Aether's core is built around clean separation of concerns, interfaces, traits, and enums - making it highly extensible while staying incredibly light.

### 1. Bootstrapping & Kernel
Everything starts in Aether::_init():

- Session start with extended lifetime
- Module loading via ModuleFactory
- Middleware pipeline execution
- Router dispatch through ControllerGateway

This gives you full control over the request lifecycle.

### 2. Routing & Controllers - Annotation Magic
No YAML, no XML, no massive config arrays. Routes are defined directly in controllers using docblock annotations.

How it works: ControllerGateway reflects over your controllers, extracts `[@route]` and `[@method]`, and registers them automatically.

> Example: Simple Home Route

```php
class AppController extends Controller {

    /**
     * [@method] => GET
     * [@route] => /
     */
    public function index() {
        $this->_render("index", ["title" => "Welcome to Aether"]);
    }
}
```

> Example: Basic REST API Route

```php
class ApiController extends Controller {

    /**
     * [@method] => GET
     * [@route] => /api/fetch-users
     */
    public function users_fetch() {
        $response = ResponseFactory::_create(
            HttpResponseFormatEnum::JSON,
            [
                "admin" => "admin@aetherphp",
                "user1" => "user1@aetherphp",
                "user2" => "user2@aetherphp",
            ],
            200
        );
        $response->_send();
    }
}
```

### 3. Authentication System - Secure by Design
Built around AuthInterface and gateway pattern:

- LoginAuthGateway
- RegisterAuthGateway
- LogoutAuthGateway

Passwords hashed with Argon2ID, permissions stored as JSON-encoded arrays, users serialized safely in session.

> Example: Basic Login Check

```php
$gateway = new LoginAuthGateway($username, $password);

if ($gateway->_tryAuth()){
    echo "Login successful!";
} else {
    echo "Login failed: " . $gateway->_getStatus();
}
```

CSRF protection via CsrfMiddleware in the pipeline - automatic token generation and validation on POST/PUT/DELETE.

### 4. Database Layer - Simple Yet Powerful
DatabaseWrapper + driver system supports MySQL & SQLite out of the box. Use DatabaseWrapper for singleton access, specifying the database name and the driver.

> Usage Example: Fetch Users

```php
$db = new DatabaseWrapper("dbname", DatabaseDriverEnum::MYSQL);
$users = $db->_select("users", '*', ["active" => 1]);

foreach ($users as $user){
    var_dump($user);
}
```

Prepared statements everywhere - no SQL injection worries.

### 5. Session & User Management
- SessionInstance centralizes $_SESSION access
- UserInstance handles permissions with methods like _hasPerm(), _isAdmin()
- Automatic session update on user changes

> Example: Check Admin

```php
if (UserInstance::_isLoggedIn() && SessionInstance::_getUser()->_isAdmin()){
    echo "Admin access granted.";
}
```

### 6. Views & Rendering
Simple but effective:

```php
ViewInstance::_make("home", ["message" => "Hello, Aether!"]);
```

Supports full PHP templates - no templating engine bloat.

However, if you are in a Controller, just use the _render() method.  
> Homepage Example :
```php
    /**
     * [@method] => GET
     * [@route] => /index
     */
    public function index(){
        $this->_render("index", ["title" => "Welcome to Aether"]);
    }

    /**
     * Exactly the same thing
     *
     * [@method] => GET
     * [@route] => /index2
     */
    public function index2(){
        ViewInstance::_make("index2", ["title" => "Welcome to Aether"]);
    }
```


### 7. HTTP Requests & Responses
Use RequestFactory to create and send requests. Responses via ResponseFactory with formats like JSON, XML, TEXT.

> Example: Create JSON Response
```php
$response = ResponseFactory::_create(
    HttpResponseFormatEnum::JSON,
    ["status" => "success", "message" => "Operation complete"],
    200
);
$response->_send();

```

> Example: Handle POST Input

```php
$unpacker = new HttpParameterUnpacker();
$input = $unpacker->_getAttribute("input-field");
echo $input;
```

For requests:

```php
$request = RequestFactory::_create(
    HttpMethodEnum::GET,
    "https://api.example.com/data"
);
$response = $request->_send();
```
### 8. CLI Power Tools
Run from project root:

```bash
php bin/aether make:controller ProductController
php bin/aether setup
php bin/aether source:script scripts/migrate.php
```

Perfect for automation, migrations, and seeding.

### 9. Modules & Extensibility
Load custom modules easily:

```php
ModuleFactory::_load([
    MyAnalyticsModule::class,
    CustomApiModule::class
]);
```

Each module should implements AetherModule with _onLoad() hook.

## Requirements

- PHP 8.3+
- PDO extension
- Web server with URL rewriting (for clean routes)

## Quick Start

```bash
git clone https://github.com/dawnl3ss/Aether-PHP
cd Aether-PHP
cp .env.example .env
# Edit .env with your DB info and credentials
```

Point your web server to the project root - you're live!

## The Vision

Aether started as a personal rebellion against framework fatigue. Today, it's evolving into the go-to lightweight backend for freelancers who value speed, control, and clean code.

Join the movement. Build faster. Deliver better.

## Contributing

Pull requests welcome! Focus on keeping it light, clean, and OOP-driven.

## License

Commercial license required for redistribution. Source available for personal and freelance use. See LICENSE.md.

## Author

dawnl3ss (Alex') - Passionate about clean, performant PHP.

GitHub: [dawnl3ss](https://github.com/dawnl3ss)  
Project: [github.com/dawnl3ss/Aether-PHP](https://github.com/dawnl3ss/Aether-PHP)

**Aether PHP - The divine lightweight PHP framework**
