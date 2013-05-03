<?php

  namespace Core\Api;

  interface ApiInterface 
  {
    public function get( $path, $params, $requestOpts );
    
    public function post( $path, $params, $requestOpts );
  }
