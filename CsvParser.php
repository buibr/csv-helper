<?php

namespace buibr\csvhelper;

/**
 * This is just an example.
 */
class CsvParser implements \Iterator, \Countable
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
     * @var array
     */
    protected $headers;
    
    /**
     * Seperator of columns.
     * @var string
     */
    protected $delimeter = ',';
    
    /**
     * "quote" - quote all values with duble quotes on output
     * @var bool
     */
    protected $enclosure = FALSE;
    
    /**
     * @var bool
     */
    protected $encoding = TRUE;
    
    /**
     * Convert all to utf8
     *
     * @var bool
     */
    protected $utf8 = TRUE;
    
    /**
     * Whether to skip or throw on empty records from file.
     *
     * @var boolean
     */
    protected $throw_on_empty = FALSE;
    
    /**
     * In case we find any record that has les or more columns than headers, will throw an error.
     *
     * @var bool
     */
    protected $throw_on_mismatch_columns = TRUE;
    
    /**
     * @var int
     */
    private $position;
    
    
    public function __construct($file = NULL)
    {
        //  Iterator position
        $this->position = 0;
        
        if (empty($file)) {
            return $this;
        }
        
        //  parse the file.
        return $this->fromFile($file);
    }
    
    /**
     * Parse data from file provided here or from class instantance
     *
     * @param null $file
     *
     * @return \buibr\csvhelper\CsvParser
     * @throws \ErrorException
     */
    public function fromFile($file = NULL)
    {
        if (empty($file) && empty($this->file)) {
            throw new \ErrorException("File is not set.");
        }
        
        $this->file = $file ? $file : $this->file;
        
        try {
            $data = \file_get_contents($this->file, 'r');
            
            //  Detected encoding from file.
            $this->encoding = @mb_detect_encoding($data, mb_list_encodings(), TRUE);
            
            //  split lines
            $data = explode("\n", $data);
            
            return $this->parse($data);
            
        } catch (\Exception $e) {
            throw new \ErrorException($e);
        }
    }
    
    /**
     * @param array $data
     *
     * @return $this
     * @throws \ErrorException
     */
    private function parse(array &$data)
    {
        $this->headers = $this->parseRow($data[0]);
        
        array_shift($data);
        
        //  make data.
        foreach ($data as $id => &$row) {
            
            if ($this->throw_on_empty && empty($row)) {
                throw new \ErrorException('Invalid file. Detected empty record.');
            }
            
            if (empty($row)) {
                continue;
            }
            
            $parsedRow = $this->parseRow($row);
            
            if (count($parsedRow) !== count($this->headers) && $this->throw_on_mismatch_columns) {
                throw new \ErrorException('Invalid record has ben found at line: ' . $id);
            }
            
            $this->data[] = $parsedRow;
        }
        
        return $this;
    }
    
    /**
     * @param string $row
     *
     * @return array
     */
    private function parseRow(string &$row = '')
    {
        if ($this->utf8) {
            $row = @mb_convert_encoding($row, "UTF-8", $this->encoding);
        }
        
        return \str_getcsv($row, $this->delimeter, $this->enclosure);
    }
    
    /**
     * Add data from content.
     *
     * @param string $content
     *
     * @return \buibr\csvhelper\CsvParser
     * @throws \ErrorException
     */
    public function fromContent(string &$content)
    {
        return $this->fromData($content);
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
     *
     * @param string $data
     *
     * @return \buibr\csvhelper\CsvParser
     * @throws \ErrorException
     */
    public function fromData(string &$data)
    {
        if (empty($data)) {
            throw new \ErrorException("Data is not set.");
        }
        
        //  Detected encoding from file.
        $this->encoding = @mb_detect_encoding($data, mb_list_encodings(), TRUE);
        
        //  split lines
        $data = explode("\n", $data);
        
        return $this->parse($data);
    }
    
    /**
     *  Parse data from array to this data.
     *
     * Example
     * ```php
     * $arr = [
     *      [
     *          "name",
     *          "sname",
     *      ],
     *      [
     *          "burhan",
     *          "ibraimi",
     *      ],
     *      [
     *          "test name",
     *          "test sname",
     *      ]
     * ];
     * ```
     *
     * this function is not posible with encoding.
     *
     * @param array $data
     *
     * @return $this
     * @throws \ErrorException
     */
    public function fromArray(array &$data)
    {
        if (empty($data)) {
            throw new \ErrorException("Data array is not set.");
        }
        
        // get headers from
        $this->headers = \array_values(\current($data));
        
        //  remove headers
        \array_shift($data);
        
        //
        foreach ($data as &$v) {
            $this->data[] = \array_values($v);
        }
        
        //  remove the firs element as its headers
        @\array_shift($this->data);
        
        return $this;
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
     * @param array $data
     *
     * @return $this
     * @throws \ErrorException
     */
    public function fromAssocArray(array &$data)
    {
        if (empty($data)) {
            throw new \ErrorException("Data array is not set.");
        }
        
        // get headers from
        $this->headers = \array_keys(current($data));
        
        //
        foreach ($data as &$v) {
            $this->data[] = \array_values($v);
        }
        
        return $this;
    }
    
    /**
     * Parse full object to arrays with attached headers to each row.
     * @return array;
     */
    public function toArray(): array
    {
        $arr = [];
        foreach ($this->data as &$row) {
            if (empty($row)) {
                continue;
            }
            //  attach keys to object.
            $arr[] = @\array_combine($this->headers, $row);
        }
        
        return $arr;
    }
    
    /**
     * Parse all elements and return only one column as specified if exists.
     *
     * @param string|null $column
     *
     * @return array
     * @throws \ErrorException
     */
    public function toColumn(string $column = NULL): array
    {
        if (empty($column)) {
            throw new \ErrorException("Column not specified.");
        }
        
        //
        $position = \array_search(trim($column), $this->headers);
        
        //
        if (is_null($position)) {
            throw new \ErrorException("This '{$column}' column is not found in headers");
        }
        
        $return = [];
        foreach ($this->data as &$row) {
            $return[] = $row[$position];
        }
        
        return $return;
    }
    
    /**
     * Parse all elements and return only one column as specified  as key in array filled with $value.
     *
     * example if we have:
     *
     * ```csv
     * account,name,email,
     * 109,burhan,burhan@csv.pro
     * 101,burhan,burhan@csv.pro
     * 102,burhan,burhan@csv.pro
     * 103,burhan,burhan@csv.pro
     * ```
     *
     * and we call :
     *
     * ```php
     * (new CsvParser)->fromFile('path/to/csv')->toColumnFill('account', null);
     * ```
     *
     * the resoult will be:
     *
     * ```
     * Array
     * (
     *  [109] =>
     *  [101] =>
     *  [102] =>
     *  [103] =>
     * )
     *
     *
     * @param string|null $column
     * @param string|null $value
     *
     * @return array
     * @throws \ErrorException
     */
    public function toColumnFill($column = NULL, $value = NULL)
    {
        if (empty($column)) {
            throw new \ErrorException("Column not specified.");
        }
        
        $position = \array_search(trim($column), $this->headers);
        
        // if(!\in_array( trim($column), $this->headers)){
        if (is_null($position)) {
            throw new \ErrorException("This '{$column}' column is not found in headers");
        }
        
        $return = [];
        foreach ($this->data as $id => &$row) {
            $return[$row[$position]] = $value;
        }
        
        return $return;
    }
    
    /**
     * Rebuild csv as content for download or print in raw.
     *
     * @return string
     */
    public function toContent()
    {
        $content = implode($this->delimeter, $this->headers) . "\n";
        
        foreach ($this->data as &$row) {
            if (empty($row)) {
                continue;
            }
            
            $content .= implode($this->delimeter, $row) . "\n";
        }
        
        return $content;
    }
    
    /**
     * Rebuild csv with filtered columns and output as file content.
     *
     * @param array $columns
     *
     * @return string
     * @throws \ErrorException
     */
    public function toContentColumns(array $columns = [])
    {
        if (empty($columns)) {
            throw new \ErrorException("No columns have ben set. Please use toContent for full conver to content.");
        }
        
        $rows = $this->toColumns($columns, TRUE);
        $content = implode($this->delimeter, \array_keys($rows[0])) . "\n";
        
        foreach ($rows as &$row) {
            if (empty($row)) {
                continue;
            }
            
            $content .= implode($this->delimeter, \array_values($row)) . PHP_EOL;
        }
        
        return $content;
    }
    
    /**
     * Retrun only set columns to be returned
     *
     * @param array $columns
     * @param bool  $associative
     *
     * @return array
     * @throws \ErrorException
     */
    public function toColumns(array $columns, bool $associative = FALSE)
    {
        if (empty($columns)) {
            throw new \ErrorException("Column not specified.");
        }
        
        //
        $indexes = [];
        foreach ($columns as $col) {
            
            $cidx = \array_search(trim($col), $this->headers);
            
            if ($cidx === FALSE) {
                throw new \ErrorException("This '{$col}' column is not found in headers");
            }
            
            $indexes[$col] = $cidx;
        }
        
        if (empty($indexes)) {
            throw new \ErrorException("Not found ay column in headers.");
        }
        
        $return = [];
        foreach ($this->data as $id => &$row) {
            // $return[$id] = $row[$indexes];
            foreach ($indexes as $idkey => $index) {
                if ($associative)
                    $return[$id][$idkey] = $row[$index];
                else
                    $return[$id][$index] = $row[$index];
            }
        }
        
        return $return;
    }
    
    /**
     * Returns headers in one dimensional array
     *
     * @param array = php function  to fixx headers.
     *
     * @return array
     */
    public function getHeaders(array $functions = [])
    {
        if (empty($functions)) {
            return $this->headers;
        }
        
        foreach ($this->headers as $key => $header) {
            foreach ($functions as $func) {
                $this->headers[$key] = $func($header);
            }
        }
        
        return $this->headers;
    }
    
    
    /**
     * Get raw body without headers
     */
    public
    function getRaw()
    {
        return $this->data;
    }
    
    
    /**
     * Get encoding of this file/data
     */
    public
    function getEncoding()
    {
        return $this->encoding;
    }
    
    /**
     * Reset the count to first position.
     */
    public function rewind()
    {
        $this->position = 0;
    }
    
    public function key()
    {
        return $this->position;
    }
    
    public function next()
    {
        ++$this->position;
    }
    
    public function valid()
    {
        return isset($this->data[$this->position]);
    }
    
    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }
    
    /**
     * Current element column value.
     *
     * @param string $column
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function column(string $column)
    {
        $cidx = \array_search($column, $this->headers);
        
        if (is_null($cidx)) {
            throw new \ErrorException("This '{$column}' column is not found in headers");
        }
        
        return $this->current()[$cidx];
    }
    
    /**
     * One element by corrent position.
     *
     * @param boolean $associative
     *
     * @return array|mixed
     */
    public function current($associative = FALSE)
    {
        
        if ($associative) {
            return @\array_combine($this->headers, $this->data[$this->position]);
        }
        
        return $this->data[$this->position];
    }
    
    /**
     * @param array $columns
     * @param false $associative
     *
     * @return array
     * @throws \ErrorException
     */
    public function columns(array $columns, $associative = FALSE)
    {
        
        $indexes = [];
        foreach ($columns as $col) {
            
            $cidx = \array_search(trim($col), $this->headers);
            
            if ($cidx === FALSE) {
                throw new \ErrorException("This '{$col}' column is not found in headers");
            }
            
            $indexes[$col] = $cidx;
        }
        
        $return = [];
        foreach ($indexes as $idkey => $index) {
            if ($associative)
                $return[$idkey] = $this->current()[$index];
            else
                $return[$index] = $this->current()[$index];
        }
        
        return $return;
    }
    
    /**
     * @param bool $value
     */
    public function throwOnEmpty(bool $value = TRUE)
    {
        $this->throw_on_empty = $value;
    }
    
    /**
     * @param bool $value
     */
    public function throwOnMismatchColumns(bool $value = TRUE)
    {
        $this->throw_on_mismatch_columns = $value;
    }
}
