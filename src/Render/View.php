<?php

namespace Dupot\StaticManagementFramework\Render;

class View
{
    protected $viewPath = null;
    protected $contextList = [];

    public function __construct(string $viewPath, array $contextList = [])
    {
        $this->viewPath = $viewPath;
        $this->contextList = $contextList;
    }

    public function render(): string
    {
        ob_start();
        include($this->viewPath);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
