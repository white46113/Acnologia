<?php

namespace logia\LiquidOrm;

use logia\DatabaseConnection\DatabaseConnection;
use logia\LiquidOrm\DataMapper\DataMapperEnvironmentConfiguration;
use logia\LiquidOrm\DataMapper\DataMapperFactory;
use logia\LiquidOrm\EntityManager\EntityManagerFactory;
use logia\LiquidOrm\QueryBuilder\QueryBuilderFactory;
use logia\LiquidOrm\QueryBuilder\QueryBuilder;
use logia\LiquidOrm\EntityManager\Crud;

class LiquidOrmManager
{
    /** @var string */
    protected  $tableSchema;

    /** @var string */
    protected  $tableSchemaID;

    /** @var array */
    protected  $options;

    /** @var DataMapperEnvironmentConfiguration */
    protected  $environmentConfiguration;

    /**
     * Main class constructor
     *
     * @param DataMapperEnvironmentConfiguration $environmentConfiguration
     * @param string $tableSchema
     * @param string $tableSchemaID
     * @param array|null $options
     */
    public function __construct(DataMapperEnvironmentConfiguration $environmentConfiguration,  $tableSchema,  $tableSchemaID, $options = [])
    {
        $this->environmentConfiguration = $environmentConfiguration;
        $this->tableSchema = $tableSchema;
        $this->tableSchemaID = $tableSchemaID;
        $this->options = $options;
    }

    /**
     * initialize method which glues all the components together and inject the necessary dependency within the 
     * respective object
     *
     * @return Object
     */
    public function initialize()
    {
        $dataMapperFactory = new DataMapperFactory();
        $dataMapper = $dataMapperFactory->create(DatabaseConnection::class, $this->environmentConfiguration);
        if ($dataMapper) {
            $queryBuilderFactory = new QueryBuilderFactory();
            $queryBuilder = $queryBuilderFactory->create(QueryBuilder::class);
            if ($queryBuilder) {
                $entityManagerFactory = new EntityManagerFactory($dataMapper, $queryBuilder);
                return $entityManagerFactory->create(Crud::class, $this->tableSchema, $this->tableSchemaID, $this->options);
            }
        }
    }

}
