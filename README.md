Yii2 CSV Helper
===============
Parser/Builder of CSV data to file/file to data

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist buibr/yii2-csv-helper "*"
```
```
composer require --prefer-dist buibr/yii2-csv-helper "*"
```

or add

```
"buibr/yii2-csv-helper": "*"
```

to the require section of your `composer.json` file.


Usage
-----

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

