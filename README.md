# dupotStaticManagementFramework
framework to manage a static website created using dupotStaticGenerationFramework 

# how to use it ?

``` bash
composer require dupot/static-management-framework
mkdir public
touch public/index.php
```
In public/index.php, paste this code

``` php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Dupot\StaticManagementFramework\Application;
use Dupot\StaticManagementFramework\Http\Request;
use Dupot\StaticManagementFramework\Http\Response;
use Dupot\StaticManagementFramework\Setup\ConfigManager;
use Dupot\StaticManagementFramework\Setup\RouteManager;

try {
    session_start();

    define('ROOT_PATH', __DIR__ . '/../');

    $debug = true;

    $request = new Request([
        Request::SOURCE_GET => $_GET,
        Request::SOURCE_POST => $_POST,
        Request::SOURCE_SESSION => $_SESSION,
        Request::SOURCE_SERVER => $_SERVER
    ]);

    $routeManager = new RouteManager();
    $routeManager->loadConfigFromJson(__DIR__ . '/../src/conf/routing.json');

    $configManager = new ConfigManager();
    //$configManager->loadConfigFromIni(__DIR__ . '/../src/conf/yourConfigFile.ini');

    $configManager->setSectionParam('path', 'root', ROOT_PATH);

    $application = new Application([
        Application::CONFIG_MANAGER => $configManager,
        Application::ROUTE_MANAGER => $routeManager,
        Application::REQUEST => $request,
        Application::RESPONSE => new Response()
    ]);
    $application->run();
} catch (Exception $e) {

    if ($debug) {
        print_r($e, true);
    }
}

```
This will be start file for your web application, add a json file to setup your routes
``` bash
mkdir -p src/conf/
touch src/conf/routing.json
```
In src/conf/routing.json, paste this code

``` json
[
    {
        "pattern": "#/myOtherPage.html#",
        "class": "My\\Infrastructure\\Pages\\OtherPage",
        "method": "index"
    },
    {
        "pattern": "#/#",
        "class": "My\\Infrastructure\\Pages\\HomePage",
        "method": "index"
    }
]
```

Example of Home page:
``` php
<?php

namespace My\Infrastructure\Pages;

use Dupot\StaticManagementFramework\Page\PageAbstract;
use Dupot\StaticManagementFramework\Render\Layout;
use Dupot\StaticManagementFramework\Render\View;

class HomePage extends PageAbstract
{
    protected $layout = null;

    public function __construct()
    {
        $this->layout = new Layout(__DIR__ . '/Layouts/default.php');
    }

    public function index()
    {
        //$errorList = $this->processLogin();

        $view = new View(
            __DIR__ . '/View/home.php',
            []
        );

        $this->layout->appendContext('contentList', $view);

        return $this->render();
    }

    public function render()
    {

        echo $this->layout->render();
    }
}

```
