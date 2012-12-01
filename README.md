***php-api-core
=============
core files for a REST api walker

**Usage
=============
How to add your api url.  Note the `:path` string at the end. This is needed
for some api's (foursquare, github)
```php
use Core\Api\AbstractApi;

$api = AbstractApi();
$client = api->getClient();

$client->setOption('url','http://blah.com/:path');
```

If you have api keys to use, mash them into a [pem](google.com) file and 
set the `certificate` option to the path  of the file.
```php
$client->setOption('certificate', $pem_file);
```

Make a query
```php
$result = $api->get($endpoint, $paramters, $request_options);
```