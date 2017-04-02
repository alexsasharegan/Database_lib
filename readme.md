# Database
[![Latest Stable Version](https://poser.pugx.org/alexsasharegan/database/v/stable)](https://packagist.org/packages/alexsasharegan/database)
[![Total Downloads](https://poser.pugx.org/alexsasharegan/database/downloads)](https://packagist.org/packages/alexsasharegan/database)
[![Latest Unstable Version](https://poser.pugx.org/alexsasharegan/database/v/unstable)](https://packagist.org/packages/alexsasharegan/database)
[![License](https://poser.pugx.org/alexsasharegan/database/license)](https://packagist.org/packages/alexsasharegan/database)

PHP MySQL utilities to properly handle errors, connections, and make getting data simple and DRY.

- - - -


## Installation

With **Composer**:

```shell
composer require alexsasharegan/database
```

Then require in the vendor autoloader:
```php
<?php

require_once 'path/to/vendor/autoload.php';
```

## Usage

### Static Methods

Calling `Database::connect( [ string $configFile = './database.json', array $options = [] ] )` without any arguments will look for a configuration file called `database.json` file in the calling file's directory. To make use of this default behavior, place your `database.json` next to your php file. If your config file exists elsewhere, pass in the path as the first argument.

An example config file is included in the project. Just change the name from `database.example.json` to `database.json` and move it next to your calling php file. Here is the example config:

```php
<?php

// library defaults
$connectionOptions = [
    'DB_HOST'     => '127.0.0.1',
    'DB_NAME'     => 'test',
    'DB_PORT'     => '3306',
    'DB_CHARSET'  => 'utf8',
    'DB_USERNAME' => 'admin',
    'DB_PASSWORD' => 'admin',
];

// library defaults
$pdoOptions = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => FALSE,
	PDO::ATTR_STRINGIFY_FETCHES  => FALSE,
];

$mySQL = new \Database\MySQL($connectionOptions, $pdoOptions);
```

Some other static methods:

```php
<?php

use Database\MySQL;

# Takes a MySQL-formatted date string and returns a string file path
MySQL::SQLDateToPath( string $SQLDate );
# example
echo MySQL::SQLDateToPath( '2016-09-06 14:02:26' );
# Outputs: '2016/09/06'

# Returns a MySQL-formatted timestamp
echo MySQL::now();
# Outputs: '2016-09-06 14:04:15';

```

### Instance Methods

#### QUERY

```php
<?php
$mySQL = new \Database\MySQL($connectionOptions, $pdoOptions);

# use a try/catch block to handle a bad query
try {
  $mySQL->select(['firstName', 'lastName'])
        ->from('users')
        ->where('id', 'in', [1,2,3])
        # we can chain methods together here
        ->map(
        # this can be any callable type ( will be called with each row )
        # closures let us 'use' vars from parent scope
        # be wary of when you need to pass by reference using &
        function ( array $resultRow ) 
        {
            $users[] = new User($resultRow); # the $resultRow is an associative array
        }
    );
} catch ( Exception $e ) {
  # insert some custom error handling here
  exit( $e );
}
```
