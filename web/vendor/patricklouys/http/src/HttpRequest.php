<?php

namespace Http;

class HttpRequest implements Request
{
    protected $parameters;
    protected $server;
    protected $files;
    protected $cookies;

    public function __construct(
        array $get,
        array $post,
        array $cookies,
        array $files,
        array $server
    ) {
        $this->parameters = array_merge($get, $post);
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
    }

    /**
     * Returns a parameter value or a default value if none is set.
     * 
     * @param  string $key
     * @param  string $defaultValue (optional)
     * @return string
     */
    public function getParameter($key, $defaultValue = null)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }

        return $defaultValue;
    }

    /**
     * Returns a file value or a default value if none is set.
     * 
     * @param  string $key
     * @param  string $defaultValue (optional)
     * @return string
     */
    public function getFile($key, $defaultValue = null)
    {
        if (array_key_exists($key, $this->files)) {
            return $this->files[$key];
        }

        return $defaultValue;
    }

    /**
     * Returns a cookie value or a default value if none is set.
     * 
     * @param  string $key
     * @param  string $defaultValue (optional)
     * @return string
     */
    public function getCookie($key, $defaultValue = null)
    {
        if (array_key_exists($key, $this->cookies)) {
            return $this->cookies[$key];
        }

        return $defaultValue;
    }

    /**
     * Returns a File Iterator.
     * 
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns a Cookie Iterator.
     * 
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Returns a File Iterator.
     * 
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * The URI which was given in order to access this page
     * 
     * @return string
     * @throws MissingRequestMetaVariableException
     */
    public function getUri()
    {
        return $this->getServerVariable('REQUEST_URI');
    }

    /**
     * Return just the path
     * 
     * @return string
     */
    public function getPath()
    {
        return strtok($this->getServerVariable('REQUEST_URI'), '?');
    }

    /**
     * Which request method was used to access the page;
     * i.e. 'GET', 'HEAD', 'POST', 'PUT'. 
     * 
     * @return string
     * @throws MissingRequestMetaVariableException
     */
    public function getMethod()
    {
        return $this->getServerVariable('REQUEST_METHOD');
    }

    /**
     * Contents of the Accept: header from the current request, if there is one.
     * 
     * @return string
     * @throws MissingRequestMetaVariableException
     */
    public function getHttpAccept()
    {
        return $this->getServerVariable('HTTP_ACCEPT');
    }

    /**
     * The address of the page (if any) which referred the user agent to the 
     * current page.
     * 
     * @return string
     * @throws MissingRequestMetaVariableException
     */
    public function getReferer()
    {
        return $this->getServerVariable('HTTP_REFERER');
    }

    /**
     * Content of the User-Agent header from the request, if there is one.
     * 
     * @return string
     * @throws MissingRequestMetaVariableException
     */
    public function getUserAgent()
    {
        return $this->getServerVariable('HTTP_USER_AGENT');
    }

    /**
     * The IP address from which the user is viewing the current page.
     * 
     * @return string
     * @throws MissingRequestMetaVariableException
     */
    public function getIpAddress()
    {
        return $this->getServerVariable('REMOTE_ADDR');
    }

    /**
     * Checks to see whether the current request is using HTTPS.
     * 
     * @return boolean
     */
    public function isSecure()
    {
        return (array_key_exists('HTTPS', $this->server)
            && $this->server['HTTPS'] !== 'off'
        );
    }

    /**
     * The query string, if any, via which the page was accessed.
     * 
     * @return string
     * @throws MissingRequestMetaVariableException
     */
    public function getQueryString()
    {
        return $this->getServerVariable('QUERY_STRING');
    }

    private function getServerVariable($key)
    {
        if (!array_key_exists($key, $this->server)) {
            throw new MissingRequestMetaVariableException($key);
        }
        
        return $this->server[$key];
    }
}