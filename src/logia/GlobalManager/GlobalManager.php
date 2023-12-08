<?php
namespace logia\GlobalManager;

use logia\GlobalManager\GlobalManagerInterface;
use logia\GlobalManager\Exception\GlobalManagerException;
use logia\GlobalManager\Exception\GlobalManagerInvalidArgumentException;
use Throwable;

class GlobalManager implements GlobalManagerInterface
{

    /**
     * @inheritdoc
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set( $key, $value)
    {
        $GLOBALS[$key] = $value;
    }

    /**
     * @inheritdoc
     * 
     * @param string $key
     * @return mixed
     * @throws GlobalManagerException;
     */
    public static function get( $key)
    {
        self::isGlobalValid($key);
        try{
            return $GLOBALS[$key];
        }catch(Throwable $throwable) {
            throw new GlobalManagerException('An exception was thrown trying to retrieve the data.');
        }
    }

    /**
     * Check if we have a valid key and its not empty else throw an 
     * exception
     * 
     * @param string $key
     * @return void
     * @throws GlobalManagerInvalidArgumentException
     */
    private static function isGlobalValid( $key) 
    {
        if (!isset($GLOBALS[$key])) {
            throw new GlobalManagerInvalidArgumentException('Invalid global key. Please ensure you have set the global state for ' . $key);
        }
        if (empty($key)) {
            throw new GlobalManagerInvalidArgumentException('Argument cannot be empty.');

        }
    }
}