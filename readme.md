#Database

This is a simple lib that contains one class, `Database`, with one static method, `Database::connect()`.

##Setup

- First, go into the `/lib/Database.class.php` file, and enter your default connection settings to your project's database.
- Then in your project, simply `require_once` the path to the `Database_Autoloader.php`.

Now you can call `Database::connect()`.

##Method Signature

Calling `Database::connect()` without any arguments will use your database config to return a database handle. If you wish to connect using different connection settings, you can pass an associative array with whichever connection setting you wish to override. The config array contains the associative array keys you need to use:

```php
<?php

require_once 'path/to/Database_Autoloader.php';

# Pass in an associative array like this
# with any number of these four params
# that you wish to override

$DATABASE_CONFIG_OPTIONS = [
  'hostName'     => 'yourHostName',
  'databaseName' => 'yourDatabaseName',
  'dbUserName'   => 'yourUsername',
  'dbPassword'   => 'yourpassword',
];

$mysqliHandle = Database::connect($DATABASE_CONFIG_OPTIONS);
```
