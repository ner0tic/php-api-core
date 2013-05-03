php-api-core
==================

ORM agnostic php library to access REST apis
version 2.0

Installation
=============
Add to composer:
```javascript
require:
  "ner0tic/php-api-core": "2.0.0"
```

Usage
=============
```php
use Core\Api\AbstractApi;

$api = new AbstractApi();
$client = $api->getClient();
```

If you have api keys to use, mash them into a [pem](google.com) file and 
set the `certificate` option to the path  of the file.
```php
$client->setOption('certificate', $pem_file);
```

Make a query
```php
$result = $api->get($endpoint, $parameters, $request_options);
```

Working example:
```php
use Core\Api\AbstractApi();

$api = new AbstractApi();
$client = $api->getClient();

$client->setUrl( 'http://api.example.com/' );

$users = $client->get('users', array('last_name' => 'smith'));

foreach($users as $user)
  $user = new \Acme\UserBundle\Entity\User($user);

// ... use $users as needed
```