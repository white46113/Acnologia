<?php
namespace logia\GlobalManager;

interface GlobalManagerInterface
{

    /**
     * Set the global variable
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set( $key, $value);

    /**
     * Get the value of the set global variable
     * 
     * @param string $key
     * @return mixed
     * @throws GlobalManagerException;
     */
    public static function get( $key);

}