<?php


namespace logia\LiquidOrm\DataRepository;

use logia\Base\Exception\BaseUnexpectedValueException;
use logia\LiquidOrm\DataMapper\DataMapperEnvironmentConfiguration;
use logia\LiquidOrm\LiquidOrmManager;
use logia\Yaml\YamlConfig;

class DataRepositoryFactory
{

    /** @var string */
    protected  $tableSchema;

    /** @var string */
    protected  $tableSchemaID;

    /** @var string */
    protected  $crudIdentifier;

    /**
     * Main class constructor
     *
     * @param string $crudIdentifier
     * @param string $tableSchema
     * @param string $tableSchemaID
     */
    public function __construct(string $crudIdentifier, string $tableSchema, string $tableSchemaID)
    {
        $this->crudIdentifier = $crudIdentifier;
        $this->tableSchema = $tableSchema;
        $this->tableSchemaID = $tableSchemaID;
    }

    /**
     * Create the DataRepository Object
     *
     * @param string $dataRepositoryString
     * @return void
     * @throws BaseUnexpectedValueException
     */
    public function create(string $dataRepositoryString) 
    {
        $entityManager = $this->initializeLiquidOrmManager();
        $dataRepositoryObject = new $dataRepositoryString($entityManager);
        if (!$dataRepositoryObject instanceof DataRepositoryInterface) {
            throw new BaseUnexpectedValueException($dataRepositoryString . ' is not a valid repository object');
        }
        return $dataRepositoryObject;
    }

    public function initializeLiquidOrmManager()
    {
        $environmentConfiguration = new DataMapperEnvironmentConfiguration(YamlConfig::file('database'));
        $ormManager = new LiquidOrmManager($environmentConfiguration, $this->tableSchema, $this->tableSchemaID);
        return $ormManager->initialize();
    }

}