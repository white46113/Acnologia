<?php
namespace logia\LiquidOrm\DataMapper;
use logia\DatabaseConnection\DataBaseConnectionInterface;
use PDOStatement;
Class DataMapper implements DataMapperInterface
{

    private $dbh;
    private $statement;

    public function __construct(DataBaseConnectionInterface $dbh){
        $this->dbh = $dbh;
    }
    
    private function isEmpty($value = '',$error_message = null){
        if(empty($value)){
            throw new DataMapperException($error_message);
        }
    }
    private function isArray($fields = []){
        if(!is_array($fields)){
            throw new DataMapperException('Your arguments need to be an array');
        }
    }

    public function prepare($sql_query = ''){
        $this->statement = $this->dbh->open()->prepare($sql_query);
        return $this;
    }

    public function bind($value){
        try{
            switch($value){
                case is_bool($value):
                case intval($value):
                    $data_type = PDO::PARAM_INT;
                    break;
                case is_null($value):
                    $data_type = PDO::PARAM_NULL;
                    break;
                default :
                    $data_type = PDO::PARAM_STR;
                    break;
            }
        }catch(DataMapperException $exception){
            throw $exception;
        }
    }

    protected function bindValues($fields = []){
        $this->isArray($fields);
        foreach($fields  as $key => $val){
            $this->statement->bindValue(':'.$key,$val,$this->bind($val));
        }
        return $this->statement;
    }
    public function bindParameters($fields = [],$is_search = false){
        if(is_array($fields)){
            $type = ($is_search === false) ? $this->bindValues($fields):$this->bindSearchValues($fields);
            if($type){
                return $this;
            }
        }
        return false;
    }
    protected function bindSearchValues($fields = []){
        $this->isArray($fields);
        foreach($fields  as $key => $val){
            $this->statement->bindValue(':'.$key,'%'.$val.'%',$this->bind($val));
        }
        return $this->statement;
    }
    public function execute(){
        if($this->statement){
            $this->statement->execute();
        }
    }
    public function numRows(){
        if($this->statement){
            $this->statement->rowCount();
        }
    }

    public function result(){
        if($this->statement){
            $this->statement->fetch(PDO::FETCH_OBJ);
        }
    }
    public function results(){
        if($this->statement){
            $this->statement->fetchAll(PDO::FETCH_OBJ);
        }
    }
    public function getLastId(){
        try{
            if($this->dbh->open()){
                $lastId-> $this->dbh->open()->lastInsertedId();
                if(!empty($lastId)){
                    return $lastId;
                }
            }
        }
        catch(Throwable $throwable){
            throw $throwable;
        }
    }

}