<?php

namespace Dupot\StaticManagementFramework\Setup;



class Route
{
    const STATUS_FOUND = 'statusFound';
    const STATUS_NOT_FOUND = 'statusNotFound';

    protected $status;

    protected $classToCall;
    protected $methodToCall;
    protected $argsList = [];

    public function setClassToCall(string $classToCall)
    {
        $this->classToCall = $classToCall;
    }
    public function setMethodToCall(string $methodToCall)
    {
        $this->methodToCall = $methodToCall;
    }
    public function setArgsList(array $argsList)
    {
        $this->argsList = $argsList;
    }

    public function getClassToCall()
    {
        return $this->classToCall;
    }
    public function getMethodToCall()
    {
        return $this->methodToCall;
    }
    public function getArgsList()
    {
        return $this->argsList;
    }

    public function setStatusFound()
    {
        $this->status = self::STATUS_FOUND;
    }
    public function setStatusNotFound()
    {
        $this->status = self::STATUS_NOT_FOUND;
    }

    public function isStatusFound()
    {
        return ($this->status === self::STATUS_FOUND
        );
    }
}
