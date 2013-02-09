<?php

include_once "../bootstrap.php";

use PSolr\SolrServer;

class SolrServerTest extends \PHPUnit_Framework_TestCase{
    
    public function setUp(){
        
    }
    
    public function testSolrServer(){
        $server = new SolrServer();
        $this->assertInstanceOf("PSolr\SolrServer", $server);
        
        $expected = 'http://localhost';
        $this->assertEquals($expected, (string) $server);
    }
}

?>
