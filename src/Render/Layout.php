<?php

namespace Dupot\StaticManagementFramework\Render;

class Layout
{
    protected $layoutPath = null;
    protected $contextList = [];

    public function __construct(string $layoutPath, array $contextList = [])
    {
        $this->layoutPath = $layoutPath;
        $this->contextList = $contextList;
    }

    public function setLayoutPath(string $layoutPath)
    {
        $this->layoutPath = $layoutPath;
    }

    public function assignContext(string $param, $value)
    {
        $this->contextList[$param] = $value;
    }
    public function appendContext(string $param, $value)
    {
        if (!isset($this->contextList[$param])) {
            $this->contextList[$param] = [];
        }
        $this->contextList[$param][] = $value;
    }

    public function render(): string
    {

        ob_start();
        include($this->layoutPath);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
