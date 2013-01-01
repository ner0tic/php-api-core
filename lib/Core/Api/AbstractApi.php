<?php
  namespace Core\Api;
  
  use Core\Client;

  abstract class AbstractApi implements ApiInterface {
    /**
     *
     * @var Core\Client 
     */
    protected $client;

    /**
     * 
     * @param Core\Client $client
     */
    public function __construct(Client $client = null) {
      $this->client = $client instanceof Client ? $client : new Client();
    }

    /**
     * 
     * @param srting $path
     * @param array $params
     * @param array $requstOpts
     * @return type
     */
    public function get($path, $params = array(), $requestOpts = array()) {
      return $this->client->get($path, $params, $requestOpts);
    }

    /**
     * 
     * @param string $path
     * @param array $params
     * @param array $requestOpts
     * @return type
     */
    public function post($path, $params = array(), $requestOpts = array()) {
      return $this->client->post($path, $params, $requestOpts);
    }
  }
