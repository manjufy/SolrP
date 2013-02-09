<?php

namespace PSolr;

class SolrQuery
{
    
    private $_facets = array();
    
    private $_fields = array();
    
    private $_params = array();
    
    public function __construct(){}
    
    /**
     * Return query facets
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
     * @throws PSolr\Exception\InvalidDataException
     * @return array
     */
    public function addFacet($facetName)
    {
        if(!is_string($facetName)){
            throw Exceptions\InvaliDataException();
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
    public function resetFacets(){
        $this->_facets = array();
    }
}

?>
