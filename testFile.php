<?php
require 'CsvParser.php';

use \buibr\csvhelper\CsvParser;

// print('<pre>');
// print_r($argv);
// print('</pre>');
// die;

$data = (new CsvParser)->fromFile($argv[1])->toArray();

// $arr = [
//     [
//         'name'=>'burhan',
//         'sname'=>'tset'
//     ],
//     [
//         'name'=>'burhan',
//         'sname'=>'tset'
//     ],
//     [
//         'name'=>'burhan',
//         'sname'=>'tset'
//     ],
// ];

// $data = (new CsvParser)->fromArray($arr)->toArray();

print('<pre>');
print_r($data);
print('</pre>');