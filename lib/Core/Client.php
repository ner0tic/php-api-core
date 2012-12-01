<?php
namespace Core;

use Core\Api\ApiInterface;
use Core\HttpClient\HttpClientInterface;
use Core\HttpClient\HttpClient;
  
use \InvalidArgumentException as InvalidArgument;

/**
 * class Client instance of HttpClient
 */
 class Client
{
    /**
     
     * @global string AUTH_URL_TOKEN
     */
    const AUTH_URL_TOKEN = 'url_token';
    
    /**
     * @global string  AUTH_URL_CLIENT_ID
     */
    const AUTH_URL_CLIENT_ID = 'url_client_id';
    
    /**
     * @global string AUTH_HTTP_PASSWORD
     */
    const AUTH_HTTP_PASSWORD = 'http_password';
    
    /**
     * @global string AUTH_HTTP_TOKEN
     */
    const AUTH_HTTP_TOKEN = 'http_token';

    /**
     *
     * @var Foursquare\HttpClient $httpClient 
     */
    private $_httpClient = null;

    /**
     *
     * @var array apis
     * An array of the available endpoint apis to load.  
     * Think of it like a smarter lazy loader but not that smart...
     */
    private $_apis = array();

    /**
     *
     * @var array $headers 
     */
    private $_headers = array();

    /**
     * 
     * @param Foursquare\HttpClient\HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient = null) {
        $this->_httpClient = $httpClient ?: new HttpClient();
    }
      
    /**
     * Authenticate
     * @param string $login login credentials
     * @param string $secret client secret
     * @param string $method call method
     */
    public function authenticate($login, $secret = null, $method = null) {
      $this->getHttpClient()->setOption('auth_method', $method);

      if($method === self::AUTH_HTTP_PASSWORD || $method === self::AUTH_URL_CLIENT_ID) {
        $this->getHttpClient()
             ->setOption('login', $login)
             ->setOption('password', $secret);
      } 
      else 
        $this->getHttpClient()->setOption('token', $secret);

      $this->getHttpClient()->authenticate();
    }

    /**
     * Get
     * @param string $path
     * @param array $parameters
     * @param array $requestOptions
     * @return type
     */
      public function get($path, array $parameters = array(), $requestOptions = array()) {
        return $this->getHttpClient()->get($path, $parameters, $requestOptions);
      }
  
      /**
       * Post
       * @param string $path
       * @param array $parameters
       * @param array $requestOptions
       * @return type
       */
      public function post($path, array $parameters = array(), $requestOptions = array()) {
        return $this->getHttpClient()->post($path, $parameters, $requestOptions);
      }

      /**
       * GetHttpClient
       * @return Foursquare\HttpClient\HttpClient
       */
      public function getHttpClient() {
        $this->_httpClient->setHeaders($this->headers);

        return $this->_httpClient;
      }

      /**
       * SetHttpClient
       * @param Foursquare\HttpClient\HttpClientInterface $httpClient
       */
      public function setHttpClient(HttpClientInterface $httpClient) {
        $this->_httpClient = $httpClient;
      }

      /**
       * Api
       * @param string $name
       * @return api 
       * @throws InvalidArgument
       */
      public function api($name) {
        if (!isset($this->_apis[$name])) {
          $ns = "Api\$name";
          if(!$api = new $ns())
            throw new InvalidArgument();
          
          $this->_apis[$name] = $api;
        }
        return $this->_apis[$name];
      }

      /**
       * GetRateLimit
       * @return type
       */
      public function getRateLimit() {
        return $this->get('rate_limit');
      }

      /**
       * ClearHeaders
       */
      public function clearHeaders() {
        $this->setHeaders(array());
      }

      /**
       * 
       * @param type $headerSetHeader
       */
      public function setHeaders($header) {
        $this->_headers = $headers;
      }
  }
