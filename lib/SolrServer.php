<?php

class SolrServer 
{
    public $host = 'localhost';
    
    public $port;
    
    public $core;
    
    public $timeout = 10;
    
    public function __toString()
    {
        $port = isset($this->port) ? ':' . $this->port : '';
        $core = isset($this->core) ? '/' . $this->core : '' ;
        
        return 'http://' . $this->host . $port . $core ;
    }
    
}

?>
