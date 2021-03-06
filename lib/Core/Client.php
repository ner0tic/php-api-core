<?php
namespace Ner0tic\ApiEngine;

use Ner0tic\ApiEngine\HttpClient\HttpClientInterface;
use Ner0tic\ApiEngine\HttpClient\HttpClient;
use \InvalidArgumentException as InvalidArgument;

/**
 * class Client instance of HttpClient
 */
 class Client
{
    const AUTH_URL_TOKEN        = 'url_token';
    const AUTH_URL_CLIENT_ID    = 'url_client_id';
    const AUTH_HTTP_PASSWORD    = 'http_password';
    const AUTH_HTTP_TOKEN       = 'http_token';

    /**
     * @var Ner0tic\ApiEngine\HttpClient $httpClient 
     */
    protected $httpClient  =  null;

    /**
     * @var array apis
     * An array of the available endpoint apis to load.  
     * Think of it like a smarter lazy loader but not that smart...
     */
    private $_apis = array();

    /**
     * @var array $headers 
     */
    private $_headers           = array();
    
    protected $authUrlClientId;
    
    protected $authHttpToken;
    
    protected $authHttpPassword;
    
    protected $authUrlToken;

    /**
     * @param Core\HttpClient\HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient = null) 
    {
        $this->httpClient = $httpClient ?: new HttpClient();
    }
      
    /**
     * Authenticate
     * @param string $login login credentials
     * @param string $secret client secret
     * @param string $method call method
     */
    public function authenticate($login, $secret = null, $method = null) 
    {
        $this->getHttpClient()->setOption('auth_method', $method);

        if($method === self::AUTH_HTTP_PASSWORD || $method === self::AUTH_URL_CLIENT_ID) 
        {
            $this->getHttpClient()
                 ->setOption('login', $login)
                 ->setOption('secret', $secret);
        } 
        else 
            $this->getHttpClient()->setOption('token', $secret);
    }
    
    public function deauthenticate()
    {
        $this->authenticate(null, null, null);
    }

    /**
     * Get
     * @param string $path
     * @param array $parameters
     * @param array $requestOptions
     * @return type
     */
    public function get($path, array $parameters = array(), $requestOptions = array()) 
    {
        return $this->getHttpClient()->get($path, $parameters, $requestOptions);
    }

    /**
     * Post
     * @param string $path
     * @param array $parameters
     * @param array $requestOptions
     * @return type
     */
    public function post($path, array $parameters = array(), $requestOptions = array()) 
    {
      return $this->getHttpClient()->post($path, $parameters, $requestOptions);
    }

    /**
     * GetHttpClient
     * @return Ner0tic\ApiEngine\HttpClient\HttpClient
     */
    public function getHttpClient() 
    {
        return $this->httpClient;
    }

    /**
     * SetHttpClient
     * @param Ner0tic\ApiEngine\HttpClient\HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient) {
        $this->httpClient = $httpClient;
    }

    /**
     * Api
     * @param string $name
     * @return api 
     * @throws InvalidArgument
     */
    public function api($name) {
        if(!isset($this->_apis[ $name ])) 
        {
            $ns = "Api\$name";
            if(!$api = new $ns())
                throw new InvalidArgument();

            $this->_apis[ $name ] = $api;
        }
        
        return $this->_apis[ $name ];
    }

    /**
     * GetRateLimit
     * @return type
     */
    public function getRateLimit() 
    {
        return $this->get('rate_limit');
    }

    /**
     * ClearHeaders
     */
    public function clearHeaders() 
    {
        $this->setHeaders(array());
    }

    /**
     * 
     * @param type $headerSetHeader
     */
    public function setHeaders($header) 
    {
        $this->_headers = $headers;
    }
    
    public function setOption($name, $value)
    {
        return $this->httpClient->setOption($name, $value);
    }
    
    public function setUrl($url)
    {
        return $this->httpClient->setOption('url', $url);
    }
    
    public function getUrl()
    {
        return $this->httpClient->getOption('url');
    }
}
