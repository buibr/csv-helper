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
        @\array_shift($this->data);

        return $this;
    }

    /**
     *  Parse data from file provided here or from class instantance
     * @param stirng $data  the file to parse from.
     * @return CsvParser $this
     */
    public function fromData( array &$data = null )
    {
        if(empty($data) && empty($this->data))
        {
            throw new \ErrorException("Data is not set.");
        }

        $this->data     = $data ? $data : $this->data;
        $first          = current($this->data);
        $this->headers  = \array_keys($first);

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
            //  convert from utf16 to utf8
            $row    = @iconv('UTF-8' , 'UTF-8' , $row);

            //  to array
            $row    = \str_getcsv($row, $this->delimeter, $this->enclosure);

            //  attach keys to object.
            $this->data[$id]   = @array_combine($this->headers, $row);

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

        $position = \array_search(trim($column), $this->headers);

        // if(!\in_array( trim($column), $this->headers)){
        if( is_null($position) ) {
            throw new \ErrorException("This '{$column}' column is not found in headers");
        }

        $return = [];
        foreach($this->data as $id=>&$row)
        {
            //  to array
            $row               = \str_getcsv($row, $this->delimeter, $this->enclosure);

            $return[]   = $row[$position];
        }

        return $return;

    }

    /**
     * Parse all elements and return only one column as specified  as key in array filled with $value.
     * @param string $colum - the column to be return as single array
     * @return array 
     */
    public function toColumnFill( $column = null, $value = null)
    {
        if(empty($column)){
            throw new \ErrorException("Column not specified.");
        }

        $position = \array_search(trim($column), $this->headers);

        // if(!\in_array( trim($column), $this->headers)){
        if( is_null($position) ) {
            throw new \ErrorException("This '{$column}' column is not found in headers");
        }

        $return = [];
        foreach($this->data as $id=>&$row)
        {
            //  to array
            $row                        = \str_getcsv($row, $this->delimeter, $this->enclosure);
            $return[$row[$position]]    = $value;
        }

        return $return;

    }

    /**
     * Rebuild csv as content for download or print in raw.
     * @param string $colum - the column to be return as single array
     * @return array 
     */
    public function toContent()
    {
        $content = implode($this->delimeter, $this->headers). "\n";

        foreach($this->data as &$row)
        {
            $content .= implode($this->delimeter, $row) . "\n";
        }

        return $content;
    }

}
