What it gives?
--------------

It adds new features to Schema which you can use in your migrations:
```php
Schema::create('tests', function ($table) {
    $table->binary('md5', 16); // will use BINARY(16) instead of BLOB
    $table->binary('different'); // default BINARY(255), still not BLOB

    $table->set('flags', ['a', 'b', 'c', 'd']); // SET('a', 'b', 'c', 'd')

    $table->tinyText('sometext'); // TINYTEXT -- other sizes already supported

    $table->tinyBlob('someblob'); // TINYBLOB
    $table->blob('biggerblob'); // BLOB -- like Illuminate's binary() method
    $table->mediumBlob('evenbiggerblob'); // MEDIUMBLOB
    $table->longBlob('biggestblob'); // LONGBLOB

    $table->index('sometext', 'foo', 10); // Third param is index length not
                                          // algorithm like in Illuminate
    $table->unique('someblob', null, 10); // null for automatic naming
});

```

What versions of Laravel are supported
--------------------------------------

It has been tested to work with Laravel 5.0 and 5.5.

How to install
--------------

Add package to `composer.json`
```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/rafis/schema-extended"
    }
],
"require": {
    "laravel/framework": "5.0.*",
    "rafis/schema-extended": "~1.0"
},
```

Do not forget to run `composer install` or `composer update rafis/schema-extended` after modifying `composer.json`.

Replace "alias" in the configuration file `config/app.php`:
```php
'aliases' => array(
    ...
    // 'Schema' => 'Illuminate\Support\Facades\Schema',
    'Schema'    => 'SchemaExtended\Schema',
),
```
