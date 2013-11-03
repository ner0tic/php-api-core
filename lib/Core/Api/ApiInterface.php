<?php

  namespace Ner0tic\ApiEngine\Api;

  interface ApiInterface 
  {
    public function get($path, $params, $requestOpts);
    
    public function post($path, $params, $requestOpts);
  }
