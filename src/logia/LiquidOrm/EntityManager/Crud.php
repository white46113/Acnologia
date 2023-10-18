<?php



namespace logia\LiquidOrm\EntityManager;

use logia\Base\Exception\BaseInvalidArgumentException;
use logia\Base\Exception\BaseUnexpectedValueException;
use logia\LiquidOrm\DataMapper\DataMapper;
use logia\LiquidOrm\QueryBuilder\QueryBuilder;
use logia\LiquidOrm\EntityManager\CrudInterface;
use Throwable;

class Crud implements CrudInterface
{

    /** @var DataMapper */
    protected  $dataMapper;

    /** @var QueryBuilder */
    protected  $queryBuilder;

    /** @var string */
    protected  $tableSchema;

    /** @var string */
    protected  $tableSchemaID;

    /** @var array */
    protected $options;

    /**
     * Main constructor
     *
     * @param DataMapper $dataMapper
     * @param QueryBuilder $queryBuilder
     * @param string $tableSchema
     * @param string $tableSchemaID
     */
    public function __construct(DataMapper $dataMapper, QueryBuilder $queryBuilder, string $tableSchema, string $tableSchemaID, array $options = [])
    {
        $this->dataMapper = $dataMapper;
        $this->queryBuilder = $queryBuilder;
        $this->tableSchema = $tableSchema;
        $this->tableSchemaID = $tableSchemaID;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getSchema()
    {
        return (string)$this->tableSchema;
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getSchemaID() 
    {
        return (string)$this->tableSchemaID;
    }

    /**
     * @inheritdoc
     *
     * @return integer
     */
    public function lastID()
    {
        return $this->dataMapper->getLastId();
    }

    /**
     * @inheritdoc
     *
     * @param array $fields
     * @return boolean
     */
    public function create($fields = [])
    {
        try {
            $args = ['table' => $this->getSchema(), 'type' => 'insert', 'fields' => $fields];
            $query = $this->queryBuilder->buildQuery($args)->insertQuery();
            $this->dataMapper->persist($query, $this->dataMapper->buildQueryParameters($fields));
            if ($this->dataMapper->numRows() ==1) {
                return true;
            }
        } catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritdoc
     *
     * @param array $selectors
     * @param array $conditions
     * @param array $parameters
     * @param array $optional
     * @return array
     */
    public function read($selectors = [],$conditions = [], $parameters = [],  $optional = [])
    {
        //try{
        $args = ['table' => $this->getSchema(), 'type' => 'select', 'selectors' => $selectors, 'conditions' => $conditions, 'params' => $parameters, 'extras' => $optional];
        $query = $this->queryBuilder->buildQuery($args)->selectQuery();
        $this->dataMapper->persist($query, $this->dataMapper->buildQueryParameters($conditions, $parameters));
        if ($this->dataMapper->numRows() >= 0) {
            return $this->dataMapper->results();
        } 
        //} catch(Throwable $throwable) {
            //throw $throwable;
        //}
    }

    /**
     * @inheritdoc
     *
     * @param array $fields
     * @param string $primaryKey
     * @return boolean
     */
    public function update($fields = [], $primaryKey)
    {
        try {
            $args = ['table' => $this->getSchema(), 'type' => 'update', 'fields' => $fields, 'primary_key' => $primaryKey];
            $query = $this->queryBuilder->buildQuery($args)->updateQuery();
            $this->dataMapper->persist($query, $this->dataMapper->buildQueryParameters($fields));
            if ($this->dataMapper->numRows() == 1) {
                return true;
            }
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritdoc
     *
     * @param array $conditions
     * @return boolean
     */
    public function delete($conditions = []) 
    {
        try {
            $args = ['table' => $this->getSchema(), 'type' => 'delete', 'conditions' => $conditions];
            $query = $this->queryBuilder->buildQuery($args)->deleteQuery();
            $this->dataMapper->persist($query, $this->dataMapper->buildQueryParameters($conditions));
            if ($this->dataMapper->numRows() == 1) {
                return true;
            }
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritdoc
     *
     * @param array $selectors
     * @param array $conditions
     * @return array
     */
    public function search($selectors = [], $conditions = [])
    {
        try {
            $args = ['table' => $this->getSchema(), 'type' => 'search', 'selectors' => $selectors, 'conditions' => $conditions];
            $query = $this->queryBuilder->buildQuery($args)->searchQuery();
            $this->dataMapper->persist($query, $this->dataMapper->buildQueryParameters($conditions));
            if ($this->dataMapper->numRows() >= 0) {
                return $this->dataMapper->results();
            }
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @param array $selectors
     * @param array $conditions
     * @return Object|null
     */
    public function get( $selectors = [],  $conditions = [])
    {
        $args = ['table' => $this->getSchema(), 'type' => 'select', 'selectors' => $selectors, 'conditions' => $conditions];
        $query = $this->queryBuilder->buildQuery($args)->selectQuery();
        $this->dataMapper->persist($query, $this->dataMapper->buildQueryParameters($conditions));
        if ($this->dataMapper->numRows() >= 0) {
            return $this->dataMapper->result();
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function aggregate( $type,  $field = 'id',  $conditions = [])
    {
        $args = ['table' => $this->getSchema(), 'primary_key'=>$this->getSchemaID(), 
        'type' => 'select', 'aggregate' => $type, 'aggregate_field' => $field, 
        'conditions' => $conditions];

        $query = $this->queryBuilder->buildQuery($args)->selectQuery();
        $this->dataMapper->persist($query, $this->dataMapper->buildQueryParameters($conditions));
        if ($this->dataMapper->numRows() > 0)
            return $this->dataMapper->column();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function countRecords( $conditions = [],  $field = 'id') 
    {
        if ($this->getSchemaID() !='') {
            return empty($conditions) ? $this->aggregate('count', $this->getSchemaID()) : $this->aggregate('count', $this->getSchemaID(), $conditions);
        }
    }

    /**
     * @inheritDoc
     *
     * @param string $sqlQuery
     * @param array|null $conditions
     * @param string $resultType
     * @return void
     */
    public function rawQuery( $sqlQuery,  $conditions = [],  $resultType = 'column')
    {
        try{
            $args = ['table'=>$this->getSchema(),'type'=>'raw','raw'=>$sqlQuery,'condition' => $conditions];
            $query = $this->queryBuilder->buildQuery($args)->rawQuery();
            $this->dataMapper->persist($query,$this->dataMapper->buildQueryParameters($conditions));
            if($this->dataMapper->numRows()){

            }
        }catch(Throwable $throwable){
            throw $throwable;
        }

    }


}