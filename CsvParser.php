<?php

namespace buibr\csvhelper;

/**
 * This is just an example.
 */
class CsvParser extends \yii\base\Widget
{
    /**
     * This is the file from where we get data.
     */
    public $file;

    /**
     * This is the data where we put all procesed records
     */
    protected $data;

    /**
     * Save all headers on this object
     */
    protected $headers;

    /**
     * Seperator of columns.
     */
    protected $colum_seperator = ',';

    /**
     * "quote" - quote all values with duble quotes
     */
    protected $quote = false;

    /**
     * Convert all to utf8
     */
    protected $utf8 = false;

    

    public function run()
    {
        return "Hello!";
    }
}
