<?php

namespace logia\LiquidOrm\DataMapper;
use logia\LiquidOrm\DataMapper\Exception\DataMapperinvalidArgumentException;
Class DataMapperEnvironmentConfiguration
{
    public function __construct($credentials = []){
        $this->credentials = $credentials;  
    }
    public function getDatabaseCredentials($driver =''){
        foreach ($this->credentials as $cre){
            if(array_key_exists($driver,$cre)){
                $connection_array = $credentials[$cre];
            };
        };
        return $connection_array;
    }
    private function isValidCredentials($driver = ''){
        if(empty($driver) && !is_string($driver)){
            return new DataMapperinvalidArgumentException('Driver might be missing or Agument is of invalid type');
        }
        if(!is_array($this->credentials)){
            return new DataMapperinvalidArgumentException('Invalid Credentials');
        }
        if(!in_array($driver,$this->credentials[$driver])){
            return new DataMapperinvalidArgumentException('Invalid Credentials');
        }
    }
}