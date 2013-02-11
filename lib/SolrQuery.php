<?php

/**
 * SolrQuery handle all the parameters needed to build an full query string to
 * solr request
 * 
 * @author Leandro Magalhaes <le.fatecpd@gmail.com>
 * 
 * @package Solrp
 * 
 * @version 1.0
 * 
 */
class SolrQuery
{
    /**
     * Constant for desc sorting
     * @var string 
     */

    const SORT_DESC = 'desc';

    /**
     * Constant for asc sorting
     * @var string 
     */
    const SORT_ASC = 'asc';

    /**
     * Facets array
     * @var array
     */
    private $_facets = array();

    /**
     * Fields Array
     * @var array
     */
    private $_fieldList = array();

    /**
     * Parameters array
     * @var array
     */
    private $_params = array();

    /**
     * Sorting array
     * @var array
     */
    private $_sortFields = array();

    public function __construct()
    {
        
    }

    /**
     * Return all the defined query facets
     * 
     * @return array
     */
    public function getFacets()
    {
        return $this->_facets;
    }

    /**
     * Define query facets to be brought with the query
     * 
     * @param array $facets
     * 
     * @return void
     */
    public function setFacets(array $facets)
    {
        $this->_facets = $facets;
    }

    /**
     * Add a new facet to the facets collection and return an array with all 
     * the defined facets
     * 
     * @param String $facetName
     * 
     * @throws Exceptions\InvalidDataException
     * 
     * @return array
     */
    public function addFacet($facetName)
    {
        if (!is_string($facetName)) {
            throw new Exceptions\InvalidDataException();
        }
        array_push($this->_facets, $facetName);
        return $this->_facets;
    }

    /**
     * Reset facets to its default value witch is an empty array
     * 
     * Facets can also be reset by setting facets and empty array,
     * e.g. $this->setFacets(array()); 
     * 
     * @return void
     */
    public function resetFacets()
    {
        $this->_facets = array();
    }

    /**
     * Get the sorting array
     * 
     * @return array
     */
    public function getSortValues()
    {
        return $this->_sortFields;
    }

    /**
     * Sets an array with the fields to be sorted and the sorting orientation
     * 
     * The array must be an key=>value array where key is the sorting field
     * and value is the sorting orientation. E.g. array('field_name' => 'asc')
     * 
     * Many fields can be set at the same time if wrapped in and array e.g.
     * array( array('field1' => 'desc'), array('field2' => 'asc')) should add 
     * field1 as desc and field2 as asc
     * 
     * Use pre-defined constants to sort, 
     * E.g SolrQuery::SORT_DESC and SolrQuery::SORT_ASC
     * 
     * Retun an array containing all the options set 
     * 
     * @param array
     * 
     * @throws Exception\InvalidDataException
     * 
     * @return array
     */
    public function setSortFields(array $sortFields)
    {

        if (is_array($sortFields[0])) {

            foreach ($sortFields as $field) {
                list($fieldName, $orientation) = $this->parseSortArray($field);
                $this->addSortField($fieldName, $orientation);
            }
        } else {
            list($fieldName, $orientation) = $this->parseSortArray($sortFields);
            $this->addSortField($fieldName, $orientation);
        }

        return $this->_sortFields;
    }

    /**
     * Add a new field to be sorted and the sorting orientation. By default sorting
     * orientation is desc
     * 
     * Returns the already set sortFields
     * 
     * @param string $fieldName
     * 
     * @param string $sort
     * 
     * @throws Exception\InvalidDataException
     * 
     * @return array
     */
    public function addSortField($fieldName, $orientation = SolrQuery::SORT_DESC)
    {
        if (!is_string($fieldName) || empty($fieldName)) {
            throw new Exceptions\InvalidDataException(
                    "Field name must by a non-empty string");
        }
        $this->_sortFields[$fieldName] = $orientation;

        return $this->_sortFields;
    }

    /**
     * Parses the option and let it ready to be used by addSortField.
     * 
     * Returns and array containing the $fieldName and $orientation that can 
     * be input straight ahead, e.g. array($fieldname, $orientation)
     * 
     * @param array $option
     * 
     * @return array
     */
    private function parseSortArray(array $option)
    {
        list($fieldName, $orientation) = array_pad($option, 2, null);

        if (is_null($orientation)) {
            $orientation = self::SORT_DESC;
        }

        return array($fieldName, $orientation);
    }

    /**
     * Add a field to the field list.
     * 
     * Returns the list of fields already set
     * 
     * @param string $field
     * 
     * @return array 
     */
    public function addField($field)
    {
        if (is_string($field) && !empty($field)) {
            $this->_fieldList[] = $field;
            $this->_fieldList = array_unique($this->_fieldList);
        }
        return $this->_fieldList;
    }

    /**
     * Return the field list set
     * 
     * @return array
     */
    public function getFieldsList()
    {
        return $this->_fieldList;
    }

    /**
     * Sets multiple field list at the same time.
     * 
     * If an empty array is passed as parameters the field list will be
     * reset. Same effect of using resetFieldList
     * 
     * @param array $fields
     * 
     * @return array
     */
    public function setFieldList(array $fields)
    {
        $this->_fieldList = array_unique($fields, SORT_REGULAR);
        return $this->_fieldList;
    }

    /**
     * Reset fieldList to its default value witch is an empty array
     * 
     * FieldList can also be reset by passing setFieldList an empty array,
     * e.g. $this->setFieldList(array());
     * 
     * @return void
     */
    public function resetFieldList()
    {
        $this->_fieldList = array();
    }

    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Params must be an multi-dimensional array with the following structure
     * 
     * if $params is an empty array the params will be reset to its original value
     * which is an empty 
     * 
     * array( 
     *     'param1' => array(['value1'], '[valueN]',
     *      ...
     *     'paramM' => 'value1', 'valueNM'  ));
     * 
     * @param array $params
     */
    public function setParams(array $params)
    {

        if (!self::isValidParamsStructure($params)) {
            throw new Exceptions\InvalidStructureException();
        }

        if (empty($params)) {
            $this->resetParams();
        } else {
            foreach ($params as $paramKey => $values) {
                $this->setParam($paramKey, $values);
            }
        }
        return $this->_params;
    }

    /**
     * Reset param to its original value
     * 
     * @return void
     */
    public function resetParams()
    {
        $this->_params = array();
    }

    /**
     * Set Multiple values to the same param at the same time
     * 
     * @param string $name
     * 
     * @param array $values
     * 
     * @return array
     * 
     */
    public function setParam($name, array $values)
    {
        $params = isset($this->_params[$name]) ? $this->_params[$name] : array();
        $params += $values;

        return $this->_params[$name] = array_unique($params);
    }

    /**
     * Validate params structure
     * 
     * @return boolean
     */
    public static function isValidParamsStructure(array $data)
    {
        $return = true;
        foreach ($data as $key => $value) {
            $return &= (is_string($key) && is_array($value));
        }

        return (boolean) $return;
    }

    /**
     * Sets a new parameters to parameter list.
     * 
     * All parameters might be muti-valued  and are grouped in a 
     * multi-dimensional array e.g.
     * array( 
     *      'param1' => array('value1', 'valueN'),
     *      ...
     *      'paramM' => array('value1', 'valueNM'));
     * 
     * All parameters with the same name will be grouped and all duplicated
     * value to the same parameters will be removed letting no duplicated value
     * 
     * If is somehow needed to pass the false to the value better to surround 
     * with quotes or double quotes otherwith it will be taken as the boolean
     * false and throw an exception
     * 
     * Throws exception if $name param is an nullable value or $value is and empty
     * string
     * 
     * @param string $name
     * 
     * @param string $value
     * 
     * @return array
     * 
     * @throws \Exceptions\InvalidDataException
     * 
     */
    public function addParam($name, $value)
    {

        if ((!is_string($name) || empty($name)) || empty($value)) {
            throw new \Exceptions\InvalidDataException(
                    "Param/value must be an non-empty string");
        }

        $param = isset($this->_params[$name]) ?
                $this->_params[$name] :
                array();

        $param[] = (string) $value;

        $this->_params[$name] = array_unique($param);

        return $this->_params;
    }

}