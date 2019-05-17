<?php

require 'vendor/autoload.php';

use buibr\csvhelper\CsvParser;

$csv = new CsvParser();

$csv->fromFile( __DIR__ . '/tests/data/xml.xml');

$csv->next();
$csv->next();
$csv->next();

print('<pre>');
print_r([$csv->key(), $csv->current(1)]);
print('</pre>');
die;