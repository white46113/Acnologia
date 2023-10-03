<?php
namespace logia\Router;

interface RouterInterface
{
    /**
     * Simply add route to routing table 
     */ 
    public function add($string = '',$params = array());

    /**
     * Dispatch is used to load the default page for a controller
     */
    public function dispatch($url = '');


}



?>