CSV Helper
===============
Parser/Builder of CSV data to file/file to data


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist buibr/csv-helper "^1.5"
```

or add

```
"buibr/csv-helper": "*"
```

to the require section of your `composer.json` file.


Usage
------------

Get data as array of format [$key=>$value]:

```php
<?php 
use buibr\csvhelper\CsvParser; 

$parser = new CsvParser('path/to/file');
$data   = $parser->fromFile()->toArray();

?>
```

or

```php
<?php 
use buibr\csvhelper\CsvParser; 

$data = (new CsvParser)->fromFile('path/to/file')->toArray();

?>
```

Get only one column value as one dimensional array.

Example
```csv
name,email,phone
aaa,bbb,ccc
ddd,eee,fff
ggg,hhh,iii
```

run

```php

$data = (new CsvParser)->fromFile('path/to/file')->toColumn('email');

```

response 

```php
[
    0 => "bbb",
    1 => "eee",
    2 => "hhh"
]
```


From 1.5.4 Get only neded columns.
------------

```php

$data = (new CsvParser('path/to/file'))->toColumns(['email', 'phone']);

[
    0 => [
        "bbb",
        "ccc"
    ]
    1 => [
        "eee",
        "fff"
    ]
    2 => [
        "hhh",
        "iii"
    ]
]
```

As Array

```php

$csv    = new CsvParser('path/to/file');

//  get firs telement 
$first  = $csv->current();
Array
(
    [0] => John
    [1] => Doe
    [2] => johndoe@test.test
    [3] => 003344003203
    [4] => Unknown
)

//  returns associative array with data of current position
$assoc  = $csv->current(true);
Array
(
    [Firstname] => John
    [Lastname] => Doe
    [Email] => johndoe@test.test
    [Phone] => 003344003203
    [Adress] => Unknown
)


//  get next element 
$csv->next();
$second = $csv->current();


```

OR

```php

$csv    = new CsvParser('path/to/file');

while( $csv->valid() ){

    //  get current item as array.
    $item = $csv->current(true);
    
    //  get the the value of Firstname column from current record.
    $name = $csv->column('Firstname');

    // get some of columns 
    $fullname = \implode(' ', $csv->columns(['Firstname','Lastname']));

    //  

    $csv->next();
}
```

