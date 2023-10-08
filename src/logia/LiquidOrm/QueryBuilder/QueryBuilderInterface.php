<?php
namespace logia\LiquidOrm\QueryBuilder;

interface QueryBuilderInterface
{
    public function insertQuery();

    public function selectQuery();
    
    public function updateQuery();

    public function deleteQuery();

    public function rawQuery();

}