<?php

namespace logia\DatabaseConnection\Exception;
use PDOException;
Class DatabaseConnectionException extends PDOException
{

    protected $message;
    protected $code;

    public function _contructor($message = null,$code = null)
    {
    
        $this->message = $message;
        $this->code = $code;

    }


}