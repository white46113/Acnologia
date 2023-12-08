<?php
namespace logia\Base;

use logia\Base\Exception\BaseInvalidArgumentException;
use logia\LiquidOrm\DataRepository\DataRepository;
use logia\LiquidOrm\DataRepository\DataRepositoryFactory;

class BaseModel
{
    
    /** @var string */
    private  $tableSchema;

    /** @var string */
    private  $tableSchemaID;

    /** @var DataRepository */
    private  $repository;

    /**
     * Main class constructor
     *
     * @param string $tableSchema
     * @param string $tableSchemaID
     * @return void
     * @throws BaseInvalidArgumentException
     */
    public function __construct( $tableSchema,  $tableSchemaID)
    {
        if (empty($tableSchema) || empty($tableSchemaID)) {
            throw new BaseInvalidArgumentException('These arguments are required.');
        }
        $factory = new DataRepositoryFactory('basicCrud', $tableSchema, $tableSchemaID);
        $this->repository = $factory->create(DataRepository::class);
    }

    /**
     * Get the data repository object based on the context model
     * which the repository is being executed from.
     *
     * @return DataRepository
     */
    public function getRepo()
    {
        return $this->repository;
    }


}