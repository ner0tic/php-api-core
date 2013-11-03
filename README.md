php-api-core
==================

ORM agnostic php library to access REST apis

Usage
=============
```php
use Ner0tic\ApiEngine\Api\AbstractApi;

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
use Ner0tic\ApiEngine\Api\AbstractApi();

$api = new AbstractApi();
$client = $api->getClient();

$users = $client->get('users', array('last_name' => 'smith'));

foreach($users as $user)
  $user = new \Acme\UserBundle\Entity\User($user);

// ... use $users as needed
```