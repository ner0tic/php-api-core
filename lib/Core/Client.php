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
     
     * @var string $_authUrlToken
     */
    private $_authUrlToken = 'url_token';
    
    /**
     * @var string  $_authUrlClientId
     */
    private $_authUrlClientId = 'url_client_id';
    
    /**
     * @var string $_authHttpPassword
     */
    private $_authHttpPassword = 'http_password';
    
    /**
     * @var string $_authHttpToken
     */
    private $_authHttpToken = 'http_token';

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

      if($method === $this->_authHttpPassword || $method === $this->_authUrlClientId) {
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
    
    /**
     * Sets Http Pasword
     * @param string $ahp Http password
     */
    public function setAuthHttpPassword($ahp) {
      $this->_authHttpPassword = $ahp;
    }
    
    /**
     * Gets the Http Password
     * @return string
     */
    public function getAuthHttpPassword() {
      return $this->_authHttpPassword;
    }
    
    /**
     * 
     * @param string $aht Http token
     */
    public function setAuthHttpToken($aht) {
      $this->_authHttpToken = $aht;
    }
    
    /**
     * 
     * @return string
     */
    public function getAuthHttpToken() {
      return $this->_authHttpToken;
    }
    
    /**
     * 
     * @param string $aci Client Id
     */
    public function setAuthClientId($aci) {
      $this->_authClientId = $aci;
    }
    
    /**
     * 
     * @param string $aut URL token
     */
    public function setAuthUrlToken($aut) {
      $this->_authUrlToken = $aut;
    }
}
