<?php

require 'vendor/autoload.php';

use buibr\csvhelper\CsvParser;

$csv = new CsvParser( __DIR__ . '/tests/data/xml.xml');

// $csv->fromFile( __DIR__ . '/tests/data/xml.xml');

// $csv->next();
// // $csv->next();
// // $csv->next();

// print('<pre>');
// print_r([$csv->key(), $csv->current()]);
// print('</pre>');



while( !empty($csv->valid()) ){

    print('<pre>');
    print_r([
        $csv->column('Firstname'), 
        \implode(' ',$csv->columns(['Firstname','Lastname'])),
        $csv->current(),
    ]);
    print('</pre>');
    
    $csv->next();
}

die;