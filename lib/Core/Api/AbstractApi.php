<?php
namespace Core\Api;
  
use Core\Client;

abstract class AbstractApi implements ApiInterface 
{
    /**
     *
     * @var Core\Client 
     */
    protected $client;

    /**
     * 
     * @param Core\Client $client
     */
    public function __construct( Client $client = null ) 
    {
        $this->client = $client instanceof Client ? $client : new Client();
    }

    /**
     * 
     * @param srting $path
     * @param array $parameters
     * @param array $requstOpts
     * @return type
     */
    public function get( $path, $parameters = array(), $requestOpts = array() ) 
    {
        return $this->client->get( $path, $parameters, $requestOpts );
    }

    /**
     * 
     * @param string $path
     * @param array $parameters
     * @param array $requestOpts
     * @return type
     */
    public function post( $path, $parameters = array(), $requestOpts = array() ) 
    {
        return $this->client->post( $path, $parameters, $requestOpts );
    }

    public function setOption( $name, $value )
    {
        return $this->client->setOption( $name, $value );
    }
}
