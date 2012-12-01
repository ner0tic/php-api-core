<?php

  namespace Core\Api;

  interface ApiInterface 
  {
    protected function get($path, $params, $requestOpts);
    
    protected function post($path, $params, $requestOpts);
  }
