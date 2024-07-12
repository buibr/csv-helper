# CSV Helper

CSV Helper is a robust parser/builder for converting CSV data to arrays and vice versa. This tool is designed for handling large CSV datasets efficiently.

## Features

- Parse CSV files into arrays.
- Extract specific columns from CSV files.
- Iterate through CSV records seamlessly.
- Build CSV files from arrays.

## Installation

The preferred method of installing this extension is through [Composer](http://getcomposer.org/download/).

You can install the package by running the following command:

```sh
composer require --prefer-dist buibr/csv-helper "^1.5"
```

Alternatively, you can add it directly to the `require` section of your `composer.json` file:

```json
"require": {
    "buibr/csv-helper": "*"
}
```

## Usage

### Parse CSV Data

#### Get Data as Array

To parse a CSV file and get the data as an array:

```php
<?php 
use buibr\csvhelper\CsvParser;

$parser = new CsvParser('path/to/file');
$data   = $parser->fromFile()->toArray();
?>
```

#### Get Data as Array from File

Alternatively, you can parse the CSV file and directly get the data as an array in a single line:

```php
<?php 
use buibr\csvhelper\CsvParser;

$data = (new CsvParser)->fromFile('path/to/file')->toArray();
?>
```

### Extract Specific Columns

#### Get Only One Column as a One-Dimensional Array

Consider the following `file.csv`:

```csv
name,email,phone
aaa,bbb,ccc
ddd,eee,fff
ggg,hhh,iii
```

To extract the `email` column:

```php
<?php
use buibr\csvhelper\CsvParser;

$data = (new CsvParser)->fromFile('path/to/file')->toColumn('email');
?>
```

Result:

```php
$data = [
    0 => "bbb",
    1 => "eee",
    2 => "hhh"
];
```

#### Get Specific Columns (Version 1.5.4+)

To extract multiple columns:

```php
<?php
use buibr\csvhelper\CsvParser;

$data = (new CsvParser('path/to/file'))->toColumns(['email', 'phone']);
?>
```

Result:

```php
$data = [
    0 => ["bbb", "ccc"],
    1 => ["eee", "fff"],
    2 => ["hhh", "iii"]
];
```

### Accessing Data

#### Get the First Element

```php
<?php
use buibr\csvhelper\CsvParser;

$csv    = new CsvParser('path/to/file');
$first  = $csv->current();
```

Result:

```php
Array
(
    [0] => John
    [1] => Doe
    [2] => johndoe@test.test
    [3] => 003344003203
    [4] => Unknown
)
```

#### Get Associative Array

```php
<?php
use buibr\csvhelper\CsvParser;

$assoc  = $csv->current(true);
```

Result:

```php
Array
(
    [Firstname] => John
    [Lastname] => Doe
    [Email] => johndoe@test.test
    [Phone] => 003344003203
    [Adress] => Unknown
)
```

#### Iterate Through CSV Records

```php
<?php
use buibr\csvhelper\CsvParser;

$csv = new CsvParser('path/to/file');

while ($csv->valid()) {
    // Get item as array
    $item = $csv->current(true);

    // Get the value of the 'Firstname' column from the current record
    $name = $csv->column('Firstname');

    // Get some of the columns
    $fullname = implode(' ', $csv->columns(['Firstname', 'Lastname']));

    $csv->next();
}
```

## For More Use Cases

Explore the `test` folder in this repository to see additional examples and use cases.

---

This README provides a detailed guide on installing and using the CSV Helper. If you encounter any issues or have questions, feel free to open an issue on the GitHub repository.
