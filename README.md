<div align="center">

# Dupot Static Management Framework

**A tiny, dependency-free PHP micro-framework for adding a dynamic back-office to your static websites.**

[![Packagist](https://img.shields.io/packagist/v/dupot/static-management-framework.svg)](https://packagist.org/packages/dupot/static-management-framework)
[![License: LGPL v3](https://img.shields.io/badge/License-LGPL_v3-blue.svg)](LICENSE)
![PHP](https://img.shields.io/badge/PHP-%3E%3D7.2-777BB4?logo=php&logoColor=white)
![Dependencies](https://img.shields.io/badge/dependencies-0-brightgreen)

</div>

---

## Why?

Static websites are fast, secure and cheap to host. But at some point you need a few dynamic pages to **manage** them: a login form, an editor for your news, a button that regenerates the site…

Pulling in a full-stack framework for that is overkill. **Dupot Static Management Framework** gives you exactly what you need, and nothing more:

- 🪶 **Lightweight**: about ten small classes, **zero dependencies**. You can read the whole source code in 10 minutes.
- 🧭 **Regex routing in JSON**: declare your routes in a single `routing.json` file. Captured groups are passed as arguments to your methods.
- 🧩 **Simple page controllers**: extend `PageAbstract` and get the request, response and configuration injected automatically.
- 🎨 **Plain PHP templates**: `Layout` and `View` use native PHP files, with no template language to learn.
- ⚙️ **INI configuration**: load your settings from `.ini` files and override them at runtime.
- 🔐 **Session helpers**: read and write GET / POST / SESSION / SERVER values through one `Request` object.
- 🤝 **Companion of [dupotStaticGenerationFramework](https://github.com/imikado/dupotStaticGenerationFramework)**: generate your site with one, manage it with the other.

## Table of contents

- [Installation](#installation)
- [Quick start](#quick-start)
- [Routing](#routing)
- [Pages](#pages)
- [Layouts and views](#layouts-and-views)
- [Configuration](#configuration)
- [Request and response](#request-and-response)
- [Architecture](#architecture)
- [License](#license)

## Installation

```bash
composer require dupot/static-management-framework
```

Requirements: PHP ≥ 7.2. No other dependencies.

## Quick start

Here is the target project structure:

```
my-project/
├── composer.json
├── public/
│   └── index.php                  # Front controller
└── src/
    ├── conf/
    │   └── routing.json           # Your routes
    └── Infrastructure/
        └── Pages/
            ├── HomePage.php
            ├── AboutPage.php
            ├── Layout/
            │   └── default.php
            └── View/
                ├── home.php
                └── about.php
```

### 1. Autoload your own code

Add your namespace to your project's `composer.json`, then run `composer dump-autoload`:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

### 2. Create the front controller

File: `public/index.php`

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Dupot\StaticManagementFramework\Application;
use Dupot\StaticManagementFramework\Http\Request;
use Dupot\StaticManagementFramework\Http\Response;
use Dupot\StaticManagementFramework\Setup\ConfigManager;
use Dupot\StaticManagementFramework\Setup\RouteManager;

define('ROOT_PATH', __DIR__ . '/../');

$debug = true;

try {
    session_start();

    $request = new Request([
        Request::SOURCE_GET     => $_GET,
        Request::SOURCE_POST    => $_POST,
        Request::SOURCE_SESSION => $_SESSION,
        Request::SOURCE_SERVER  => $_SERVER,
    ]);

    $routeManager = new RouteManager();
    $routeManager->loadConfigFromJson(ROOT_PATH . 'src/conf/routing.json');

    $configManager = new ConfigManager();
    // $configManager->loadConfigFromIni(ROOT_PATH . 'src/conf/config.ini');
    $configManager->setSectionParam('path', 'root', ROOT_PATH);

    $application = new Application([
        Application::CONFIG_MANAGER => $configManager,
        Application::ROUTE_MANAGER  => $routeManager,
        Application::REQUEST        => $request,
        Application::RESPONSE       => new Response(),
    ]);
    $application->run();
} catch (Exception $e) {
    if ($debug) {
        echo '<pre>' . htmlspecialchars((string) $e) . '</pre>';
    }
}
```

### 3. Declare your routes

File: `src/conf/routing.json`

```json
[
    {
        "pattern": "#^/about\\.html$#",
        "class": "App\\Infrastructure\\Pages\\AboutPage",
        "method": "index"
    },
    {
        "pattern": "#^/$#",
        "class": "App\\Infrastructure\\Pages\\HomePage",
        "method": "index"
    }
]
```

### 4. Run it

```bash
php -S localhost:8000 -t public
```

Open <http://localhost:8000> and you're done 🎉

## Routing

Each route is a JSON object with three fields:

| Field     | Description                                                         |
|-----------|---------------------------------------------------------------------|
| `pattern` | A PCRE regular expression, with delimiters, tested on the URL path. |
| `class`   | The fully qualified class name of the page to instantiate.          |
| `method`  | The method to call on that page.                                    |

Routes are tested **in order** and the first match wins. If no route matches, the application answers `404`.

**Captured groups are passed as method arguments.** That makes routes with parameters easy:

```json
{
    "pattern": "#^/news_edit_([0-9]+)\\.html$#",
    "class": "App\\Infrastructure\\Pages\\NewsPage",
    "method": "edit"
}
```

```php
public function edit(string $id)
{
    // /news_edit_42.html  →  $id === '42'
}
```

> 💡 Anchor your patterns with `^` and `$`. A pattern like `#/#` matches every URL.

## Pages

A page is a class that extends `PageAbstract`. Before your method is called, the framework injects the `Request`, the `Response` and the `ConfigManager`, then calls the `before()` hook. Override `before()` for shared logic such as an authentication check.

File: `src/Infrastructure/Pages/HomePage.php`

```php
<?php

namespace App\Infrastructure\Pages;

use Dupot\StaticManagementFramework\Page\PageAbstract;
use Dupot\StaticManagementFramework\Render\Layout;
use Dupot\StaticManagementFramework\Render\View;

class HomePage extends PageAbstract
{
    protected $layout = null;

    public function __construct()
    {
        $this->layout = new Layout(__DIR__ . '/Layout/default.php');
    }

    public function index()
    {
        $view = new View(__DIR__ . '/View/home.php', [
            'title' => 'Welcome!',
        ]);

        $this->layout->appendContext('contentList', $view);

        echo $this->layout->render();
    }
}
```

File: `src/Infrastructure/Pages/AboutPage.php`

```php
<?php

namespace App\Infrastructure\Pages;

use Dupot\StaticManagementFramework\Page\PageAbstract;
use Dupot\StaticManagementFramework\Render\Layout;
use Dupot\StaticManagementFramework\Render\View;

class AboutPage extends PageAbstract
{
    protected $layout = null;

    public function __construct()
    {
        $this->layout = new Layout(__DIR__ . '/Layout/default.php');
    }

    public function index()
    {
        $this->layout->appendContext('contentList', new View(__DIR__ . '/View/about.php'));

        echo $this->layout->render();
    }
}
```

### Example: protecting your pages

```php
public function before()
{
    if (!$this->getRequest()->getSessionParamOr('isLogged', false)) {
        $this->getResponse()->redirect('/login.html');
    }
}
```

## Layouts and views

Layouts and views are **plain PHP files**. The variables you pass are available in `$this->contextList`.

- `Layout::assignContext($key, $value)` sets a value.
- `Layout::appendContext($key, $value)` adds a value to a list, which is useful for stacking several views.

File: `src/Infrastructure/Pages/Layout/default.php`

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <div class="container">
        <?php foreach ($this->contextList['contentList'] as $contentLoop) : ?>
            <?php echo $contentLoop->render(); ?>
        <?php endforeach; ?>
    </div>
</body>
</html>
```

File: `src/Infrastructure/Pages/View/home.php`

```php
<h1><?php echo htmlspecialchars($this->contextList['title']); ?></h1>

<a href="/about.html">Go to the About page</a>
```

File: `src/Infrastructure/Pages/View/about.php`

```php
<h1>About</h1>

<a href="/">Back to the Home page</a>
```

## Configuration

`ConfigManager` loads INI files (with sections) and lets you add values at runtime:

```ini
; src/conf/config.ini
[site]
name = "My website"
output = "/var/www/my-static-site"
```

```php
$configManager->loadConfigFromIni(ROOT_PATH . 'src/conf/config.ini');

// In a page
$siteName = $this->getConfigManager()->getSectionParam('site', 'name');
```

`getSectionParam()` throws an exception if the parameter doesn't exist, so a missing setting never goes unnoticed.

## Request and response

| Method                                   | Description                                  |
|------------------------------------------|----------------------------------------------|
| `getPostParam($name)`                    | POST value. Throws if missing.               |
| `getPostParamOr($name, $default)`        | POST value, or the default.                  |
| `getPostParamList()` / `getGetParamList()` | All POST / GET values.                     |
| `getSessionParam($name)` / `getSessionParamOr($name, $default)` | Session value.        |
| `setSessionParam($name, $value)`         | Writes to the session (and `$_SESSION`).     |
| `isMethodGet()` / `isMethodPost()`       | Tests the HTTP method.                       |
| `getUrl()`                               | Path of the requested URL.                   |
| `Response::redirect($url)`               | HTTP redirection.                            |

## Architecture

```
HTTP request
     │
     ▼
public/index.php ──► Application::run()
                           │
                           ├─► RouteManager::findRouteWithUrl()   (routing.json)
                           │
                           ├─► new YourPage()  + Request, Response, ConfigManager
                           ├─► YourPage::before()
                           └─► YourPage::method(...captured groups)
                                      │
                                      ▼
                               Layout + View  ──►  HTML
```

| Class                   | Role                                         |
|-------------------------|----------------------------------------------|
| `Application`           | Dispatches the request to the right page     |
| `Setup\RouteManager`    | Loads routes and matches URLs                |
| `Setup\ConfigManager`   | Manages INI configuration                    |
| `Http\Request`          | Wraps GET / POST / SESSION / SERVER          |
| `Http\Response`         | Redirections                                 |
| `Page\PageAbstract`     | Base class for your pages                    |
| `Render\Layout`         | Page template                                |
| `Render\View`           | Content fragment                             |

## Contributing

Issues and pull requests are welcome on [GitHub](https://github.com/imikado/dupotStaticManagementFramework).

## License

Released under the [LGPL-3.0](LICENSE) license.
Created by [Michael Bertocchi](https://www.dupot.org).
