<?php
require 'CsvParser.php';

use \buibr\csvhelper\CsvParser;

$data = (new CsvParser)->fromFile($argv[1])->toArray();

print('<pre>');
print_r($data);
print('</pre>');