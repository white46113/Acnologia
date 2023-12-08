<?php

namespace logia\LiquidOrm\DataMapper;
use logia\LiquidOrm\DataMapper\Exception\DataMapperException;

Class DataMapperFactory 
{
    public function __contruct(){

    }
    public function create($database_connection_string = '',$data_mapper_env_config = ''){
        $credentials = (new $data_mapper_env_config([]))->getDatabaseCredentials('mysql');
        $data_connection_obj = new $database_connection_string($credentials);
        if(!$data_connection_obj instanceof DatabaseConnectionInterface){
            throw new DataMapperException($data_connection_obj.' is not a object');
        }
        return new DataMapper($data_connection_obj);
    }
}