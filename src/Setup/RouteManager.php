<?php

namespace Dupot\StaticManagementFramework\Setup;

use Exception;


class RouteManager
{
    const FIELD_PATTERN = 'pattern';
    const FIELD_CLASS = 'class';
    const FIELD_METHOD = 'method';

    protected $routeList = [];


    /**
    [
        {
            "url":"/news_edit_([0-9]*).html",
            "class":"My\\Content\\News",
            "method":"edit"
        }
    ]
     */
    public function loadConfigFromJson(string $jsonFilename): void
    {
        $this->routeList = json_decode(file_get_contents($jsonFilename));
    }

    public function findRouteWithUrl(string $url)
    {
        foreach ($this->routeList as $routeLoop) {
            $pattern = $this->getField($routeLoop, self::FIELD_PATTERN);
            if (preg_match($pattern, $url, $matchList)) {

                array_shift($matchList);

                $route = new Route();
                $route->setStatusFound();
                $route->setClassToCall($this->getField($routeLoop, self::FIELD_CLASS));
                $route->setMethodToCall($this->getField($routeLoop, self::FIELD_METHOD));
                $route->setArgsList($matchList);

                return $route;
            }
        }

        $routeNotFound = new Route();
        $routeNotFound->setStatusNotFound();
        return $routeNotFound;
    }

    public function getField(object $obj, string $field)
    {
        return $obj->$field;
    }
}
