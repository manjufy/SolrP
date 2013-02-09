<?php

include_once '../bootstrap.php';

use PSolr\SolrQuery;

class SolrQueryTest extends \PHPUnit_Framework_TestCase
{
    
    public function testSetFacets()
    {
        $query = new SolrQuery();
        $query->setFacets(array('fake_facet'));
        $this->assertCount(1, $query->getFacets());
    }
    
    public function testResetFacets()
    {
        $query = new SolrQuery();
        $query->setFacets(array('fake_facet'));
        
        $this->assertCount(1, $query->getFacets());
        $query->resetFacets();
        $this->assertCount(0, $query->getFacets());
    }
    
    public function testAddFacets()
    {
        $query = new SolrQuery();
        $query->setFacets(array('fake_facet'));
        
        $this->assertCount(1, $query->getFacets());
        
        $this->assertCount(2, $query->addFacet('another_fake_facet'));
        $this->assertEquals('another_fake_facet', $query->getFacets()[1]);
    }
    
}
