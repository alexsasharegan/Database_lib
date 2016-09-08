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

Calling `Database::connect( [ string $configFile = './database.json', array $options = [] ] )` without any arguments will look for a configuration file called `database.json` file in the calling file's directory. To make use of this default behavior, place your `database.json` next to your php file. If your config file exists elsewhere, pass in the path as the first argument.

An example config file is included in the project. Just change the name from `database.example.json` to `database.json` and move it next to your calling php file. Here is the example config:

```json
{
  "host"     : "yourHostName",
  "database" : "yourDatabaseName",
  "username" : "yourUsername",
  "password" : "yourPassword"
}
```

If you wish to connect using different connection settings on the fly, as the second argument you can pass an associative array with whichever connection setting you wish to override. The config array contains the same keys as `database.json`:

```php
<?php

require_once 'path/to/Database_Autoloader.php';

use Database\MySQL;

# Pass in an associative array like this
# with any number of these four params
# that you wish to override

$CONFIG_OPTIONS = [
  'host'     => 'yourHostName',
  'database' => 'yourDatabaseName',
  'username' => 'yourUsername',
  'password' => 'yourpassword',
];

$mysqliHandle = MySQL::connect('path/to/database.json', $CONFIG_OPTIONS);

# if you're overriding all config options, you don't need the config file
# just pass null as the first argument
$mysqliHandle = MySQL::connect(null, $CONFIG_OPTIONS);

# if you want to save configurations for different databases,
# just use the json config format from database.json
# and name it however you like. Here is a suggested convention:
$mysqliHandleToUsers     = MySQL::connect('users.database.json');
$mysqliHandleToEmployees = MySQL::connect('employees.database.json');
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

#### SELECT

```php
<?php

# Alias the MySQL class
use Database\MySQL;

# create an instance with these credentials
# opens a MySQL connection and saves the handle internally
$db = new MySQL(null, [
  'host'     => '1.1.1.1',
  'database' => 'myDatabase',
  'username' => 'admin',
  'password' => 'adminPass',
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

#### INSERT

```php
<?php
require_once 'path/to/vendor/autoload.php';
use Database\MySQL;

$db = new MySQL('./path/to/database.json');

# some data for our new user
$newUser = [
  'firstName' => 'John',
  'lastName'  => 'Doe',
  'status'    => 'NEW',
  'created'   => $db->getSQLDate(),
];

# returns the insert id on success, FALSE on failure
$userId = $db->insert('users', $newUser);

# alternately, we can turn on ON DUPLICATE UPDATE
# with all the same insert values by passing TRUE
$userId = $db->insert('users', $newUser, TRUE);

# if you need to define a custom set of update key/value pairs,
# use Database\MySQL::insertOnDuplicate
$userId = $db->insertOnDuplicate('users', $newUser, ['status' => 'MODIFIED']);
# this will only update the status to 'MODIFIED' on duplicate
```

#### Other Methods

```php
<?php
require_once 'path/to/vendor/autoload.php';
use Database\MySQL;

$db = new MySQL;

$db->query("SELECT * FROM `users`");
# chainable method returns object instance

$db->iterateResult(function ($row) {
  # do stuff to row ...
});
# chainable method returns object instance


$db->getLastQuery();
# returns "SELECT * FROM `users`"

$db->getResult();
# returns the mysqli result object from the last query or NULL

$db->getError();
# returns the last error message for the most recent MySQLi function call that can succeed or fail

$db->affectRows();
# returns the number of affected rows from the last query

$db->insertId();
# returns the insert id from the last query or NULL

$db->escape( string $stringToEscape );
# returns the escaped string

# the connection handle is stored here:
Database\MySQL::db
# you can use all the mysqli methods from this prop

# examples getting the error from mysqli:
$db = new MySQL;
$db->db->error;

# with a different naming convention:
$mysql = new MySQL;
$mysql->db->error;
```
