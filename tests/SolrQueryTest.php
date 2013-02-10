<?php

include_once dirname(__FILE__) . '/../bootstrap.php';

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

    /**
     * @expectedException Exceptions\InvalidDataException
     */
    public function testAddFacetsShouldThrowException()
    {
        $query = new SolrQuery();
        $query->addFacet(1);
    }

    public function testSetSortFieldsWithOneField()
    {
        $query = new SolrQuery();

        $sortFieldsArray = $query->setSortFields(
                array("facet_name"));

        $sortFieldsArray = $query->setSortFields(
                array('facet_name2', SolrQuery::SORT_ASC));

        $this->assertCount(2, $sortFieldsArray);
        $this->assertEquals($sortFieldsArray['facet_name'], SolrQuery::SORT_DESC);
        $this->assertEquals($sortFieldsArray['facet_name2'], SolrQuery::SORT_ASC);
        $this->assertArrayHasKey('facet_name', $sortFieldsArray);
        $this->assertArrayHasKey('facet_name2', $sortFieldsArray);
    }

    public function testSetSortFieldWithMultipleFields()
    {
        $query = new SolrQuery();

        $sortFieldsArray = array(
            array('facet_name'),
            array('facet_name2', SolrQuery::SORT_ASC),
            array('facet_name3', SolrQuery::SORT_DESC)
        );

        $this->assertCount(3, $query->setSortFields($sortFieldsArray));
    }

    public function testSetSortFieldDoesNotRepeatField()
    {
        $query = new SolrQuery();
        $query->setSortFields(array("facet_name"));
        $query->setSortFields(array("facet_name"));

        $this->assertCount(1, $query->getSortValues());
        $this->assertEquals(array("facet_name" => 'desc'), $query->getSortValues());
    }

    public function testSetSortFieldUpdate()
    {
        $query = new SolrQuery();
        $query->setSortFields(array('facet_name', SolrQuery::SORT_DESC));
        $query->setSortFields(array('facet_name', SolrQuery::SORT_ASC));

        $this->assertEquals(array('facet_name' => 'asc'), $query->getSortValues());
    }

    public function testAddSortField()
    {
        $query = new SolrQuery();
        $fieldSortArr = $query->addSortField('facet_1');

        $this->assertCount(1, $fieldSortArr);
        $this->assertEquals(array('facet_1' => 'desc'), $fieldSortArr);
    }

    /**
     * @expectedException Exceptions\InvalidDataException
     */
    public function testAddSortFieldThrowsException()
    {
        $query = new SolrQuery();
        $query->addSortField(1);
    }

    public function testAddField()
    {
        $query = new SolrQuery();
        $fieldList = $query->addField('field1');
        $this->assertCount(1, $fieldList);

        $fieldList = $query->addField('field2');
        $this->assertSame(array('field1', 'field2'), $fieldList);
    }

    public function testAddFieldDoesNotContainsRepeatedElements()
    {
        $query = new SolrQuery();
        $query->addField('field1');
        $query->addField('field2');
        $fieldArray = $query->addField('field1');

        $this->assertCount(2, $fieldArray);
        $this->assertSame($fieldArray, $query->getFieldsList());
    }
    
    public function testSetFieldList(){
        $query = new SolrQuery();
        $fieldList = $query->setFieldList(
            array('field1', 'field2', 'field3', 'field2'));
        
        $this->assertEquals(array('field1','field2', 'field3'), $fieldList);
        $this->assertCount(3, $fieldList);
    }

    public function testResetFieldList()
    {
        $query = new SolrQuery();
        $query->addField('field1');

        $query->resetFieldList();
        $this->assertEmpty($query->resetFieldList());
    }

    public function testAssemble()
    {
        
    }

}
