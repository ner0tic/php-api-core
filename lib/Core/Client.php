<?php
namespace Core;

use Core\Api\ApiInterface,
    Guzzle\Http\Client as Guzzle,

    \InvalidArgumentException as InvalidArgument;

/**
 * class Client
 */
 class Client
{
    /**
     *
     * @var array apis
     * An array of the available endpoint apis to load.  
     * Think of it like a smarter lazy loader but not that smart...
     */
    private $_apis              =   array();
    
    /**
     * @var Guzzle\Http\Client $guzzleClient
     */
    protected $guzzleClient     =   null;
            
    /**
     * @var object $lastResponse
     */
    protected $lastResponse     =   null;
            
    /**
     * @var array $options
     */
    protected $options          =   array();   
    
    /**
     * 
     * @param Core\HttpClient\HttpClientInterface $httpClient
     */
    public function __construct() 
    {
        $this->guzzleClient = new guzzleClient( 'http://api.example.com/' );
    }
      
    /**
     * Get
     * @param string $path
     * @param array $parameters
     * @param array $requestOptions
     * @return type
     */
    public function get( $path, array $params = array(), $requestOptions = array() ) 
    {
        $requestOptions = array_merge( $this->options, $requestOptions );
        $url = strtr( $requestOptions[ 'url' ], array(
            ':path' =>  trim( $path, '/' )
        ) );
        
        $this->lastResponse = $this->request( $url, $params, 'GET', $requestOptions ); 
        
        return $this->lastResponse[ 'response' ];
    }

    /**
     * Post
     * @param string $path
     * @param array $parameters
     * @param array $requestOptions
     * @return type
     */
    public function post( $path, array $params = array(), $requestOptions = array() ) 
    {
      return false; 
    }

    /**
     * 
     * @param string $url
     * @param array $parameters
     * @param string $method
     * @param array $options
     * @return object
     */
    public function request( $url, array $parameters = array(), $method = 'GET', $options = array() )
    {
        if( !empty( $parameters ) )
            $querystring = utf8_encode( http_build_query( $parameters, '', '&' ) );
            
        switch( $method )
        {
            case 'GET':
            case 'get':
                $url .= '?' . $queryString;
                break;
            case 'POST':
            case 'post':
                
            case 'DELETE':
            case 'delete':
            
            default:
                throw new Exception( 'Invalid method type.' );
                break;                
        }
        
        
    }
    
    /**
     * Api
     * @param string $name
     * @return api 
     * @throws InvalidArgument
     */
    public function api( $name ) {
        if( !isset( $this->_apis[ $name ] ) ) 
        {
            $ns = "Api\$name";
            if( !$api = new $ns() )
                throw new InvalidArgument();

            $this->_apis[ $name ] = $api;
        }
        
        return $this->_apis[ $name ];
    }

    /**
     * GetOption
     * @param string $name
     * @return string|integer|array
     */
    public function getOption( $name )
    {
        return $this->options[ $name ];
    }

    /**
     * SetOption
     * @param type $name
     * @param type $value
     * @return \Core\HttpClient\HttpClient
     */
    public function setOption( $name, $value ) 
    {
        $this->options[ $name ] = $value;

        return $this;
    }
    
    public function setUrl( $url )
    {
        return $this->setOption( 'url', $url );
    }
    
    public function getUrl()
    {
        return $this->getOption( 'url' );
    }
}
