<?php


namespace Dupot\StaticManagementFramework\Http;

use Exception;

class Request
{
    protected $paramList = [];
    protected $method = null;

    const SOURCE_GET = 'sourceGet';
    const SOURCE_POST = 'sourcePost';
    const SOURCE_SESSION = 'sourceSession';
    const SOURCE_SERVER = 'sourceServer';

    const FIELD_REQUEST_URL = 'REQUEST_URI';
    const FIELD_REQUEST_METHOD = 'REQUEST_METHOD';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';


    public function __construct(array $paramList)
    {
        $this->paramList = $paramList;

        $this->method = $this->getSourceParamOr(self::SOURCE_SERVER, self::FIELD_REQUEST_METHOD, null);
    }

    protected function getSourceParamList(string $source): array
    {
        if (!isset($this->paramList[$source])) {
            throw new Exception('Cannot find ' . $source . ' in paramList');
        }
        return $this->paramList[$source];
    }
    protected function getSourceParam($source, $param)
    {
        if (!isset($this->paramList[$source]) or !isset($this->paramList[$source][$param])) {
            throw new Exception('Cannot find ' . $source . ' / ' . $param . ' in paramList');
        }
        return $this->paramList[$source][$param];
    }
    protected function getSourceParamOr($source, $param, $default = null)
    {
        if (!isset($this->paramList[$source]) or !isset($this->paramList[$source][$param])) {
            return $default;
        }
        return $this->paramList[$source][$param];
    }
    protected function setSourceParam($source, $param, $value)
    {
        $this->paramList[$source][$param] = $value;
        if ($source === self::SOURCE_SESSION) {
            $_SESSION[$param] = $value;
        }
    }

    public function getPostParamList(): array
    {
        return $this->getSourceParamList(self::SOURCE_POST);
    }
    public function getPostParam(string $param)
    {
        return $this->getSourceParam(self::SOURCE_POST, $param);
    }
    public function getPostParamOr(string $param, $default = null)
    {
        return $this->getSourceParamOr(self::SOURCE_POST, $param, $default);
    }
    public function getSessionParam(string $param)
    {
        return $this->getSourceParam(self::SOURCE_SESSION, $param);
    }
    public function getSessionParamOr(string $param, $default = null)
    {
        return $this->getSourceParamOr(self::SOURCE_SESSION, $param, $default);
    }
    public function setSessionParam(string $param, $value)
    {
        $this->setSourceParam(self::SOURCE_SESSION, $param, $value);
    }

    public function getGetParamList(): array
    {
        return $this->getSourceParamList(self::SOURCE_GET);
    }
    public function getServerParamList(): array
    {
        return $this->getSourceParamList(self::SOURCE_SERVER);
    }



    public function getUrl()
    {

        $parsedUrl = parse_url($this->getSourceParam(self::SOURCE_SERVER, self::FIELD_REQUEST_URL));
        $url = $parsedUrl['path'];

        return $url;
    }

    public function isMethodGet()
    {
        return ($this->method === self::METHOD_GET);
    }
    public function isMethodPost()
    {
        return ($this->method === self::METHOD_POST);
    }
}
