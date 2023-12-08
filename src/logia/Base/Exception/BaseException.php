<?php
namespace logia\Base\Exception;

use Exception;

class BaseException extends Exception
{ 

    /**
     * Main class constructor. Which allow overriding of SPL exceptions to add custom
     * exact message within core framework.
     *
     * @param string $message
     * @param integer $code
     * @param Exception $previous
     */
    public function __construct( $message,  $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}