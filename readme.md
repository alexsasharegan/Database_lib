# Database

### PHP Mysqli utilities to properly handle errors, connections, and make getting data simple and DRY.

- - - -


## Installation

If you're using **Composer**:

```shell
$ composer require alexsasharegan/database
```

Then require in the vendor autoloader:
```php
<?php

require_once 'path/to/vendor/autoload.php';
```

To install from **github**:

```shell
$ git clone https://github.com/alexsasharegan/Database_lib.git
```

Then require in the autoloader:

```php
<?php

require_once 'path/to/Database_lib/Database_Autoloader.php';
```

## Usage

- - - -

### Static Methods

Calling `Database::connect( [ array $options ] )` without any arguments will use the class defaults to return a database handle. To set up your config defaults, open up the source file `path/to/Database_lib/src/MySQL.php`. It will look something like this:

```php
<?php

namespace Database;
use Database\Exceptions\BadQuery;

class MySQL {

  public  $db = null,
          $_query = '',
          $queryResult = null,
          $columns = [];

  # change these to your own configuration
  public static $DATABASE_CONFIG_OPTIONS = [
    'hostName'     => 'yourHostName',
    'databaseName' => 'yourDatabaseName',
    'dbUserName'   => 'yourUsername',
    'dbPassword'   => 'yourpassword',
  ];

  ...
}
```

If you wish to connect using different connection settings on the fly, you can pass an associative array with whichever connection setting you wish to override. The config array contains the associative array keys you need to use:

```php
<?php

require_once 'path/to/Database_Autoloader.php';

use Database\MySQL;

# Pass in an associative array like this
# with any number of these four params
# that you wish to override

$DATABASE_CONFIG_OPTIONS = [
  'hostName'     => 'yourHostName',
  'databaseName' => 'yourDatabaseName',
  'dbUserName'   => 'yourUsername',
  'dbPassword'   => 'yourpassword',
];

$mysqliHandle = MySQL::connect($DATABASE_CONFIG_OPTIONS);
```

Some other static methods:

```php
<?php

use Database\MySQL;

# Takes a MySQL-formatted date string and returns a string file path
MySQL::SQLDateToPath( string $SQLDate, string $timezone = "America/Phoenix" )
# example
echo MySQL::SQLDateToPath( '2016-09-06 14:02:26' );
# Outputs: '2016/09/06'

# Returns a MySQL-formatted timestamp
MySQL::getSQLDate( string $timezone = "America/Phoenix" )
# example
echo MySQL::getSQLDate();
# Outputs: '2016-09-06 14:04:15';
```

### Instance Methods

```php
<?php

# Alias the MySQL class
use Database\MySQL;

# create an instance with these credentials
# opens a MySQL connection and saves the handle internally
$db = new MySQL([
    'hostName'     => '1.1.1.1',
    'databaseName' => 'myDatabase',
    'dbUserName'   => 'admin',
    'dbPassword'   => 'adminPass',
  ]);

# create an empty user to save our query result
$users = [];

# use a try/catch block to handle a bad query
try {
  $db->query( "SELECT * FROM `users`" )
    # we can chain methods together here
    ->iterateResult(
      # this can be any callable type ( will be called with each row )
      # closures let us 'use' vars from parent scope
      # be wary of when you need to pass by reference using &
      function ( array $resultRow ) use ( &$user ) {
        $users[] = $resultRow; # the $resultRow is an associative array
      }
    );
} catch ( Exception $e ) {
  # insert some custom error handling here
  exit( $e );
}
```
