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

    public function testSetFieldList()
    {
        $query = new SolrQuery();
        $fieldList = $query->setFieldList(
                array('field1', 'field2', 'field3', 'field2'));

        $this->assertEquals(array('field1', 'field2', 'field3'), $fieldList);
        $this->assertCount(3, $fieldList);
    }

    public function testResetFieldList()
    {
        $query = new SolrQuery();
        $query->addField('field1');

        $query->resetFieldList();
        $this->assertEmpty($query->resetFieldList());
    }

    public function testAssembleShouldReturDefaultQuery()
    {
        $query = new SolrQuery();
        $this->assertEquals("?q=*:*", $query->assemble());
    }
    
    public function testAssembleShouldReturnQueryWithParams(){
        $query = new SolrQuery();
        
        $query->setParam("param1", array("value1", "value2"));
        $query->addParam("param2", "value3");
        
        $expected = "?q=*:*&fq=param1:(value1+OR+value2)+AND+param2:(value3)";
        $this->assertEquals($expected, $query->assemble());
    }
    
    public function testAssembleShouldReturnQueryWithSort(){
        $solrQuery = new SolrQuery();
        $solrQuery->setSortFields(
            array(
                array("sort1", SolrQuery::SORT_DESC),
                array("sort2", SolrQuery::SORT_ASC)
            ));
        $expected = "?q=*:*&sort=sort1+desc,sort2+asc";
        $this->assertEquals($expected, $solrQuery->assemble());
    }

    public function testAddSingleParam()
    {
        $query = new SolrQuery();
        $paramArr = $query->addParam('param1', 'value1');

        $this->assertCount(1, $paramArr);
        $this->assertCount(1, $paramArr['param1']);
        $this->assertEquals($paramArr['param1'], array('value1'));
    }

    public function testAddMultipleValeusByParam()
    {
        $query = new SolrQuery();
        $query->addParam('param1', 'value2');
        $paramArr = $query->addParam('param1', 'value1');

        $this->assertCount(2, $paramArr['param1']);
        $this->assertEquals(array('value2', 'value1'), $paramArr['param1']);
    }

    public function testAddMultiParams()
    {
        $query = new SolrQuery();

        $query->addParam('param1', 'param1_value');
        $paramsArr = $query->addParam('param2', 'param2_value');

        $this->assertCount(2, $paramsArr);
        $this->assertEquals(array('param1', 'param2'), array_keys($paramsArr));
    }

    /**
     * @expectedException \Exceptions\InvalidDataException
     * @dataProvider providerTestAddParamShouldThrowException
     */
    public function testAddParamShouldThrowException($paramName, $paramValue)
    {
        $query = new SolrQuery();
        $query->addParam($paramName, $paramValue);
    }

    public static function providerTestAddParamShouldThrowException()
    {
        return array(
            array(1, ''),
            array(null, ''),
            array(false, ''),
            array('', ''),
            array('param1', ''),
            array('param', null),
            array('param', false),
        );
    }

    public function testSetParams()
    {
        $query = new SolrQuery();

        $arrParams = $query->setParams(array('param1' => array('value1')));
        $this->assertCount(1, $arrParams);
    }

    public function testSetParamWithMultipleValues()
    {
        $query = new SolrQuery();

        $arrParams = $query->setParams(
                array(
                    'param1' => array('value1', 'value2'),
                    'param2' => array('value1')
                ));


        $this->assertCount(2, $arrParams);
        $this->assertCount(2, $arrParams['param1']);
        $this->assertCount(1, $arrParams['param2']);
    }

    public function testSetSameParamsWithDifferentValuesShouldMergeValues()
    {
        $query = new SolrQuery();
        $arrayParam = array('param1' => array('value1'));
        $query->setParams(array('param1' => array('value1', 'value2')));
        $query->setParams($arrayParam);

        $allParams = $query->getParams();

        $this->assertEquals(
                array("param1" => array('value1', 'value2')), $allParams);

        $this->assertCount(2, $allParams['param1']);
    }

    /**
     * @expectedException Exceptions\InvalidStructureException
     */
    public function testSetParamsShouldThrowException()
    {
        $query = new SolrQuery();
        $query->setParams(array(''));
    }

    public function testSetParamsWithEmptyArrayShouldResetParams()
    {
        $query = new SolrQuery();
        $query->addParam("param1", "value1");
        $this->assertCount(1, $query->getParams());

        $params = $query->setParams(array());
        $this->assertEmpty($params);
    }

    /**
     * @dataProvider providerTestIsValidParamsStructure
     */
    public function testIsValidParamsStructure($structure, $expected)
    {
        $query = new SolrQuery();
        $this->assertEquals(
                $query->isValidParamsStructure($structure), $expected);
    }

    public function providerTestIsValidParamsStructure()
    {
        return array(
            array(
                array(), true,
            ),
            array(
                array(''), false
            ),
            array(
                array(array()), false
            ),
            array(
                array('param' => ''), false
            ),
            array(
                array('param' => array()), true
            )
        );
    }

}
