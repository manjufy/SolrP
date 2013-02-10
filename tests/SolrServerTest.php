<?php

include_once dirname(__FILE__) ."/../bootstrap.php";

class SolrServerTest extends \PHPUnit_Framework_TestCase{
    
    public function setUp(){
        
    }
    
    public function testSolrServer(){
        $server = new SolrServer();
        $this->assertInstanceOf("SolrServer", $server);
        
        $expected = 'http://localhost';
        $this->assertEquals($expected, (string) $server);
    }
}

?>
