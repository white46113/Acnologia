<?php

namespace logia\DatabaseConnection;
use PDO;
use logia\DatabaseConnection\Exception\DatabaseConnectionException;
class DatabaseConnection implements DatabaseConnectionInterface
{
    protected $dbh = '';
    protected $credentials = [];

    public function _construct($credentials = array())
    {
        $this->credentials = $credentials;
    }
    
    public function open(){
        try{
            $params = [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            ];
            $this->dbh = new PDO(
                $this->credentials['dsn'],
                $this->credentials['username'],
                $this->credentials['password'],
                $params
            );
            
        }catch(Exception $exception){
            throw new DatabaseConnectionException($exception->getMessage(),(int)$exception->getCode());
        }
        return $this->dbh;
    }
    
    public function close()
    {
        $this->dbh = null;
    }
} 