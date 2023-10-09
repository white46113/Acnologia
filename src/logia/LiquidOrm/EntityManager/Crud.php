<?php

namespace logia\LiquidOrm\EntityManager;

Class Crud implements CrudInterface
{
    protected $data_mapper;

    protected  $queryBuilder;

    protected $table_schema;

    protected  $table_schemaID;

    protected  $options;

    public function __construct($dataMapper, $queryBuilder, $tableSchema, $tableSchemaID)
    {
        $this->dataMapper = $dataMapper;
        $this->queryBuilder = $queryBuilder;
        $this->tableSchema = $tableSchema;
        $this->tableSchemaID = $tableSchemaID;
    }
}