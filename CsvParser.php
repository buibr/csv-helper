<?php

namespace buibr\csvhelper;

/**
 * This is just an example.
 */
class CsvParser
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
     *  
     */
    protected $encoding = true;


    /**
     * Convert all to utf8
     */
    protected $utf8 = true;


    /**
     * 
     */
    public function __construct( $file = null )
    {
        if(empty($file)){
            return $this;
        }

        return $this->fromFile($file);
    }


    /**
     *  Parse data from file provided here or from class instantance
     * @param stirng $file  the file to parse from.
     * @return CsvParser $this
     */
    public function fromFile( $file = null )
    {
        if(empty($file) && empty($this->file))
        {
            throw new \ErrorException("File is not set.");
        }

        $this->file = $file ? $file : $this->file;

        if(!\is_readable($this->file))
        {
            throw new \ErrorException("File is not found");
        }

        $data = \file_get_contents($this->file);

        if(empty($data))
        {
            throw new \ErrorException("Empty file uploaded.");
        }
        
        //  Detected encoding from file.
        $this->encoding = @mb_detect_encoding($data, mb_list_encodings(), true);

        //  split lines 
        $data     = explode("\n",$data);

        return $this->parse($data);

    }


    /** 
     * Sometimes we have dificulty to get the file read from our application, example from remote url
     * in this case we make sure that we have the content of the file we want to parse then push to this function.
     * example:
     * ```
     *  $data = \file_get_contents(url);
     *  $csvparser = new CsvParser();
     *  $csvparser->fromData($data);
     * ```
     * @param stirng $data raw data of the csv file
     * @return CsvParser $this
     */
    public function fromData( string &$data ){
        if(empty($data))
        {
            throw new \ErrorException("Data is not set.");
        }
        
        //  Detected encoding from file.
        $this->encoding = @mb_detect_encoding($data, mb_list_encodings(), true);

        //  split lines 
        $data     = explode("\n",$data);

        return $this->parse($data);
    }


    /**
     * The same as fromData
     * @param stirng $data  the file to parse from.
     * @return CsvParser $this
     */
    public function fromContent( string &$content )
    {
        return $this->fromData($content);
    }


    /**
     *  Parse data from array to this data.
     *  
     * Example
     * ```php 
     * $arr = [
     *      [
     *          "name"=>"burhan",
     *          "sname"=>"ibraimi",
     *      ],
     *      [
     *          "name"=>"test",
     *          "sname"=>"test",
     *      ]
     * ];
     * ```
     * 
     * this function is not posible with encoding.
     * 
     * @param stirng $data  the file to parse from.
     * @return CsvParser $this
     */
    public function fromArray( array &$data )
    {
        if(empty($data))
        {
            throw new \ErrorException("Data array is not set.");
        }

        // get headers from 
        $this->headers  = \array_values(current($data));

        //  
        foreach($data as &$v){
            $this->data[] = \array_values($v);
        }

        //  remove the firs element as its headers
        @\array_shift($this->data);

        return $this;
    }


    /**
     * Make normal rrows to data arrays
     * @return CsvParser $this
     */
    private function parse( array &$data){
        //  make data.
        foreach($data as &$row){

            if($this->utf8) {
                $row        = @mb_convert_encoding($row, "UTF-8", $this->encoding);
            }

            //  to array
            $this->data[]   = \str_getcsv($row, $this->delimeter, $this->enclosure);
        }

        //  
        $this->headers      = $this->data[0];

        //  remove the firs element as its headers
        @\array_shift($this->data);

        return $this;
    }


    /**
     * Parse full object to arrays with attached headers to each row.
     * @return array $data;
     */
    public function toArray()
    {
        $arr = [];
        foreach($this->data as &$row)
        {
            //  attach keys to object.
            $arr[]   = array_combine($this->headers, $row);
            
        }

        return $arr;
    }


    /** 
     * Retrun only set columns to be returned
     * @param string $colum - the column to be return as single array
     * @return array 
     */
    public function toColumns( array $columns, bool $associative = false )
    {
        if(empty($columns)){
            throw new \ErrorException("Column not specified.");
        }

        //  
        $indexes = [];
        foreach($columns as $col){

            $cidx = \array_search(trim($col), $this->headers);

            if($cidx === false) 
            {
                throw new \ErrorException("This '{$col}' column is not found in headers");
            }

            $indexes[$col] = $cidx;
        }

        if(empty($indexes)){
            throw new \ErrorException("Not found ay column in headers.");
        }

        $return = [];
        foreach($this->data as $id=>&$row)
        {
            // $return[$id] = $row[$indexes];
            foreach($indexes as $idkey=>$index){
                if($associative)
                    $return[$id][$idkey] = $row[$index];
                else
                    $return[$id][$index] = $row[$index];
            }
        }

        return $return;

    }


    /** 
     * Parse all elements and return only one column as specified if exists.
     * @param string $colum - the column to be return as single array
     * @return array 
     */
    public function toColumn( string $column = null )
    {
        if(empty($column)){
            throw new \ErrorException("Column not specified.");
        }

        //
        $position = \array_search(trim($column), $this->headers);

        // if(!\in_array( trim($column), $this->headers)){
        if(is_null($position) ) 
        {
            throw new \ErrorException("This '{$column}' column is not found in headers");
        }

        $return = [];
        foreach($this->data as &$row)
        {
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


    /**
     * Returns headers in one dimensional array
     * @param  array = php function  to fixx headers.
     * @return array
     */
    public function getHeaders( array $functions = [])
    {
        if(empty($functions)){
            return $this->headers;
        }

        foreach($this->headers as $key=>$header){
            foreach($functions as $func){
                $this->headers[$key] = $func($header);
            }
        }

        return $this->headers;
    }


    /**
     * Get raw body without headers
     */
    public function getRaw(){
        return $this->data;
    }


    /**
     * Get encoding of this file/data
     */
    public function getEncoding(){
        return $this->encoding;
    }


}