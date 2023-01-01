<?php

namespace Dupot\StaticManagementFramework\Page;

use Dupot\StaticManagementFramework\Http\Request;
use Dupot\StaticManagementFramework\Http\Response;

abstract class PageAbstract
{
    protected $request = null;
    protected $response = null;

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
}
