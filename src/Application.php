<?php

namespace Dupot\StaticManagementFramework;

use Dupot\StaticManagementFramework\Http\Request;
use Dupot\StaticManagementFramework\Http\Response;
use Dupot\StaticManagementFramework\Setup\ConfigManager;
use Dupot\StaticManagementFramework\Setup\RouteManager;

class Application
{
    const CONFIG_MANAGER = 'configManager';
    const ROUTE_MANAGER = 'routeManager';
    const REQUEST = 'request';
    const RESPONSE = 'response';

    protected $configManager = null;
    protected $routeManager = null;
    protected $request = null;
    protected $response = null;

    public function __construct(array $paramList)
    {
        $this->setConfigManager($paramList[self::CONFIG_MANAGER]);
        $this->setRouteManager($paramList[self::ROUTE_MANAGER]);
        $this->setRequest($paramList[self::REQUEST]);
        $this->setResponse($paramList[self::RESPONSE]);
    }

    public function setConfigManager(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function setRouteManager(RouteManager $routeManager)
    {
        $this->routeManager = $routeManager;
    }
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }



    public function run()
    {
        $url = $this->request->getUrl();

        $route = $this->routeManager->findRouteWithUrl($url);
        if ($route->isStatusFound()) {
            $class = $route->getClassToCall();
            $method = $route->getMethodToCall();
            $argsList = $route->getArgsList();

            $classObj = new $class;
            $classObj->setRequest($this->request);
            $classObj->setResponse($this->response);
            $classObj->setConfigManager($this->configManager);

            $classObj->before();

            return call_user_func_array(array($classObj, $method), $argsList);
        }

        die('404');
    }
}
