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
    protected $delimeter = ',';

    /**
     * "quote" - quote all values with duble quotes on output
     */
    protected $enclosure = false;

    /**
     * Convert all to utf8
     */
    protected $utf8 = false;


    /**
     *  Parse data from file provided here or from class instantance
     * @param stirng $file  the file to parse from.
     * @return CsvParser $this
     */
    public function fromFile( $file = null )
    {
        if(empty($file) && empty($this->file))
        {
            throw new \ErrorException("File is not defined.");
        }

        $this->file = $file ? $file : $this->file;

        if(!\is_readable($this->file))
        {
            throw new \ErrorException("File is not found");
        }

        $this->data = \file($this->file);

        if(empty($this->data))
        {
            throw new \ErrorException("Empty file uploaded.");
        }

        $this->headers = str_getcsv($this->data[0]);

        //  remove the firs element as its headers
        \array_shift($this->data);

        return $this;
    }

    /**
     * Parse full object to arrays with attached headers to each row.
     * @return array $data;
     */
    public function toArray()
    {
        foreach($this->data as $id=>&$row)
        {
            //  to array
            $row               = \str_getcsv($row, $this->delimeter, $this->enclosure);

            //  attach keys to object.
            $this->data[$id]   = array_combine($this->headers, $row);

        }

        return $this->data;

    }

    /**
     * Parse all elements and return only one column as specified if exists.
     * @param string $colum - the column to be return as single array
     * @return array 
     */
    public function toColumn( $column = null )
    {
        if(empty($column)){
            throw new \ErrorException("Column not specified.");
        }

        if(!\in_array($column, $this->headers)){
            throw new \ErrorException("This column is not found in headers");
        }

        $position = \array_search($column, $this->headers);

        $return = [];
        foreach($this->data as $id=>&$row)
        {
            //  to array
            $row               = \str_getcsv($row, $this->delimeter, $this->enclosure);

            $return[]   = $row[$position];
        }

        return $return;

    }


    

}
