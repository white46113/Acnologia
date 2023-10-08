<?php

namespace logia\LiquidOrm\DataMapper;

interface DataMapperInterface
{
    public function prepare($sql_query = '');

    public function bind($value = '');

    public function bindParameters($field = [],$is_seach = false);

    public function numrow();

    public function execute();

    public function result();

    public function results();
    public function getLastId();
}