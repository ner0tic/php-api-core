<?php
namespace Core\HttpClient;

use Buzz\Browser,
    Buzz\Client\Curl,
    Buzz\Message\MessageInterface,

    Core\Exception\ApiLimitExceedException,
    Core\HttpClient\Listener\AuthListener;

class HttpClient implements HttpClientInterface 
{
    /**
     *
     * @var $remainingCalls 
     */
    public $remainingCalls;

    /**
     * @var Array $options 
     * An array of options for feed the client instance
     */
    protected $options = array(
        'protocol'      =>  'https',
        'api_url'       =>  '',
        'url'           =>  ':protocol://:api_url/:path',
        'user_agent'    =>  'php-api (https://github.com/ner0tic/php-api-core)',
        'http_port'     =>  443,
        'auth_method'   =>  null,
        'timeout'       =>  10,
        'api_limit'     =>  5000,
        'login'         =>  null,
        'token'         =>  null,
        'certificate'   =>  false # __DIR__.'/Certificates/CAfile.pem' 
    ); 

    /**
     *
     * @var Array $history
     * Roads? Where we're going we won't need roads.
     */
    protected $history = array();

    /**
     *
     * @var Browser $browser
     * Browser instance
     */
    protected $browser;

    /**
     *
     * @var Array $headers
     * Headers for the Browser instance
     */
    protected $headers = array();

    /**
     *
     * @var Array $lastResponse
     * Store the last response
     */
    protected $lastResponse;

    /**
     * 
     * @param array $options
     * @param \Buzz\Browser $browser
     */
    public function __construct( array $options = array(), Browser $browser = null ) 
    {
        $this->options = array_merge( $this->options, $options );
        $this->browser = $browser ?: new Browser( new Curl() );

        $this->browser->getClient()->setTimeout( $this->options['timeout'] );
        $this->browser->getClient()->setVerifyPeer( true ); 
        $this->browser->getClient()->setOption( CURLOPT_SSL_VERIFYHOST, 2 );

        if( isset( $this->options[ 'certificate' ] ) && file_exists( $this->options[ 'certificate' ] ) )
        {
            $this->browser->getClient()->setOption( CURLOPT_CAINFO, $this->option[ 'certificate' ] );
        }
    }

    /**
     * SetHeaders
     * @param String $headers
     */
    public function setHeaders( array $headers ) 
    {
        $this->headers = $headers;
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

    /**
     * Get
     * @param string $path
     * @param array $params
     * @param array $options
     * @return array 
     */
    public function get( $path, array $parameters = array(), array $options = array() ) 
    {      
//        if( 0 < count( $parameters ) )
//        {
//            $path .= ( false === strpos( $path, '?' ) ? '?':'&' ) . http_build_query( $parameters, '', '&' );
//        }
        
        return $this->request( $path, $parameters, 'GET', $options );
    }

    /**
     * Post
     * @param type $path
     * @param array $params
     * @param array $options
     * @return array 
     */
    public function post( $path, array $parameters = array(), array $options = array() ) 
    {
        return $this->request( $path, $parameters, 'POST', $options );
    }

    /**
     * Request
     * @param type $path
     * @param array $params
     * @param string $httpMethod
     * @param array $options
     * @return array
     */
    private function request( $path, array $params = array(), $httpMethod= 'GET', $options = array() ) 
    {
        $options = array_merge( $this->options, $options );
        $url = strtr( $options[ 'url' ], array( 
            ':path'     =>  trim( $path, '/' ),
            ':protocol' =>  $options[ 'protocol' ],
        ) );
        
        $this->lastResponse = $this->doRequest( $url, $params, $httpMethod, $options );

        return $this->decodeResponse( $this->lastResponse[ 'response' ] );
    }

    /**
     * DoRequest
     * @param string $url
     * @param array $params
     * @param string $httpMethod
     * @param array $options
     * @return array
     */
    public function doRequest( $url, array $parameters = array(), $httpMethod = 'GET', array $options = array() ) 
    {
        if( $this->options[ 'login' ] ) 
        {
            switch( $this->options[ 'auth_method' ] ) 
            {
                case Client::AUTH_HTTP_PASSWORD:
                    $this->browser->getClient()->setOption( CURLOPT_USERPWD, $this->options[ 'login '] . ':' . $this->options[ 'secret' ] );
                    break;
                case Client::AUTH_HTTP_TOKEN:
                    $this->browser->getClient()->setOption( CURLOPT_USERPWD, $this->options[ 'login' ] . '/token:' . $this->options[ 'secret' ] );
                    break;
                case Client::AUTH_URL_TOKEN:
                default:
                    $parameters = array_merge(
                            array(
                                'login' => $this->options[ 'login' ],
                                'token' => $this->options[ 'secret' ]
                            ), 
                            $parameters
                    );
                    break;
            }
        }

        if( !empty( $parameters ) ) 
        {
            $queryString = utf8_encode( http_build_query( $parameters, '', '&' ) );

            if( 'GET' === $httpMethod ) 
            {
                $url .= '?' . $queryString;
            } 
            else 
            {
                $this->browser->getClient()->setOption( CURLOPT_POST, true );
                $this->browser->getClient()->setOption( CURLOPT_POSTFIELDS, $queryString );
            }
        }
        
        $this->browser->getClient()->setOption( CURLOPT_URL, $url );
        $this->browser->getClient()->setOption( CURLOPT_PORT, $this->options[ 'http_port' ]);
        $this->browser->getClient()->setOption( CURLOPT_USERAGENT, $this->options[ 'user_agent' ]);
        $this->browser->getClient()->setOption( CURLOPT_FOLLOWLOCATION, true);
        $this->browser->getClient()->setOption( CURLOPT_RETURNTRANSFER, true);
        $this->browser->getClient()->setOption( CURLOPT_SSL_VERIFYPEER, false);
        $this->browser->getClient()->setOption( CURLOPT_TIMEOUT, $this->options[ 'timeout' ]);
        
        
        $response = $this->browser->call( $url, $httpMethod, $this->headers, json_encode( $parameters ) );
        $this->checkApiLimit( $response );

        return array(
            'response'      =>  $response->getContent(),
            'headers'       =>  $response->getHeaders(),
            'errorNumber'   =>  '',
            'errorMessage'  =>  ''
        );
    }

    /**
     * DecodeResponse
     * @param string $response
     * @return string
     */
    private function decodeResponse( $response ) 
    {
        $content = json_decode( $response, true );
        if( JSON_ERROR_NONE !== json_last_error() )
        {
            return $response;
        }
      
        return $content;
    }

    /**
     * CheckApiLimit
     * @param \Buzz\Messge\MessageInterface $response
     * @throws ApiLimitExceedException
     */
    protected function checkApiLimit( MessageInterface $response ) 
    {
        $this->remainingCalls = $response->getHeader( 'X-RateLimit-Remaining' );
      
        if( null !== $this->remainingCalls && 1 > $this->remainingCalls ) 
        {
            throw new ApiLimitExceedException( $this->options[ 'api_limit' ] );
        }
    }
}
