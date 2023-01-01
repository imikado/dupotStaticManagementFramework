<?php

namespace Dupot\StaticManagementFramework\Http;

class Response
{
    public function redirect(string $url)
    {
        header('Location:' . $url);
        exit;
    }
}
