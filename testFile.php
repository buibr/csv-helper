<?php
require 'CsvParser.php';

use \buibr\csvhelper\CsvParser;

// print('<pre>');
// print_r($argv);
// print('</pre>');
// die;

$data = (new CsvParser)->fromFile($argv[1])->toArray();
$data = (new CsvParser)->fromFile($argv[1])->toColumn('Domain');

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