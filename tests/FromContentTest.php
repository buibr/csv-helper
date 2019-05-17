<?php
use PHPUnit\Framework\TestCase;

use buibr\csvhelper\CsvParser;

class FromContentTest extends TestCase
{
    public function testContent()
    {
        
        $csv = new CsvParser();

        $data = \file_get_contents(__DIR__ . '/data/xml.xml');

        $csv->fromContent( $data );

        $this->assertNotEmpty( $csv->getRaw() );
        $this->assertNotEmpty( $csv->getHeaders() );

        return $csv;
    }

    /**
     * @depends testContent
     */
    public function testArray(CsvParser $csv)
    {
        $this->assertIsArray( $csv->toArray() );
    }

    /**
     * @depends testContent
     */
    public function testHeaders(CsvParser $csv)
    {
        $arr = $csv->getHeaders();
        
        $this->assertEquals('Firstname', $arr[0]);
    }
    
    /**
     * @depends testContent
     */
    public function testToColumns(CsvParser $csv)
    {
        //  Associative array
        $asoc =  $csv->toColumns(['Firstname'], true);
        $this->assertArrayHasKey('Firstname', $asoc[0]);
        $this->assertEquals('Burhan', $asoc[0]['Firstname']);
        

        //  On dimensional array.
        $odm =  $csv->toColumns(['Firstname','Lastname']);
        $this->assertEquals('Burhan', $odm[0][0]);
        $this->assertEquals('Ibrahimi', $odm[0][1]);

    }


    /**
     * @depends                  testContent
     */
    public function testExceptionOnWrongColumns(CsvParser $csv)
    {
        $this->expectExceptionMessage( "This 'notexists' column is not found in headers");
        
        $csv->toColumns(['notexists']);
    }


}
?>