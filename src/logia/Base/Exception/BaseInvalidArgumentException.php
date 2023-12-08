<?php
namespace logia\Base\Exception;

use InvalidArgumentException;

class BaseInvalidArgumentException extends InvalidArgumentException
{ 
    /**
     * Exception thrown if an argument is not of the expected type.
     *
     * @param string $message
     * @param integer $code
     * @param InvalidArgumentException $previous
     * @throws LogicException
     */
    public function __construct( $message,  $code = 0, InvalidArgumentException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}