<?php
require 'CsvParser.php';

use \buibr\csvhelper\CsvParser;

$arr = [
    [
        'name','sname',
    ],
    [
        'burhan','tset'
    ],
    [
        'test33','aewqef'
    ],
    [
        'ssddff','34523'
    ],
];

$data = (new CsvParser)->fromArray($arr)->toArray();

print('<pre>');
print_r($data);
print('</pre>');