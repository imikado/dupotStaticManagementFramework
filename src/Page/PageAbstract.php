<?php

namespace Dupot\StaticManagementFramework\Page;

use Dupot\StaticManagementFramework\Http\Request;
use Dupot\StaticManagementFramework\Http\Response;
use Dupot\StaticManagementFramework\Setup\ConfigManager;

abstract class PageAbstract
{
    protected $request = null;
    protected $response = null;
    protected $configManager = null;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
    public function getResponse(): Response
    {
        return $this->response;
    }

    public function setConfigManager(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }
    public function getConfigManager(): ConfigManager
    {
        return $this->configManager;
    }

    public function before()
    {
    }
}
