<?php
namespace logia\LiquidOrm\QueryBuilder;
use logia\LiquidOrm\QueryBuilder\QueryBuilderInterface;
use logia\LiquidOrm\QueryBuilder\Exception\QueryBuilderException;
Class QueryBuilderFactory
{

    public function __construct(){

    }
    public function create($query_builder_str = ''){
        $query_builder_obj = new $query_builder_str();
        if(!$query_builder_str instanceof QueryBuilderInterface)
        {
            throw new QueryBuilderException($query_builder_str. 'is not valid object');
        }
        return $query_builder_str;
    }

}