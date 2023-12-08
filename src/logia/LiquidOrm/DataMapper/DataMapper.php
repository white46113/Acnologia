<?php 

namespace logia\LiquidOrm\DataMapper;

use logia\Base\Exception\BaseInvalidArgumentException;
use logia\Base\Exception\BaseNoValueException;
use logia\Base\Exception\BaseException;
use logia\LiquidOrm\DataMapper\Exception\DataMapperException;
use logia\DatabaseConnection\DatabaseConnectionInterface;
//use logia\LiquidOrm\DataMapper\Exception\DataMapperInvalidArgumentException;
use PDOStatement;
use Throwable;
use PDO;

class DataMapper implements DataMapperInterface
{

    /** @var DatabaseConnectionInterface */
    private  $dbh;

    /** @var PDOStatement
     * @param PDOStatement
     */
    
    private  $statement;

    /**
     * Main constructor class
     * 
     * @param DatabaseConnectionInterface
     * @return void
     */
    public function __construct(DatabaseConnectionInterface $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * Check the incoming $valis isn't empty else throw an exception
     * 
     * @param mixed $value
     * @param string|null $errorMessage
     * @return void
     * @throws DataMapperException
     */
    private function isEmpty($value, string $errorMessage = null)
    {
        if (empty($value)) {
            throw new BaseNoValueException($errorMessage);
        }
    }

    /**
     * Check the incoming argument $value is an array else throw an exception
     * 
     * @param array $value
     * @return void
     * @throws BaseInvalidArgumentException
     */
    private function isArray(array $value)
    {
        if (!is_array($value)) {
            throw new BaseInvalidArgumentException('Your argument needs to be an array');
        }
    }

    /**
     * @inheritDoc
     */
    public function prepare($sqlQuery = '')
    {
        $this->isEmpty($sqlQuery);
        $this->statement = $this->dbh->open()->prepare($sqlQuery);
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @param [type] $value
     * @return void
     */
    public function bind($value = '')
    {
        try {
            switch($value) {
                case is_bool($value) :
                case intval($value) :
                    $dataType = PDO::PARAM_INT;
                    break;
                case is_null($value) :
                    $dataType = PDO::PARAM_NULL;
                    break;
                default :
                    $dataType = PDO::PARAM_STR;
                    break;
            }
            return $dataType;
        } catch(BaseException $exception) {
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     *
     * @param array $fields
     * @param boolean $isSearch
     * @return self
     */
    public function bindParameters($fields = [],  $isSearch = false,$optionl = [])
    {
        $this->isArray($fields);
        if (is_array($fields)) {
            $type = ($isSearch === false) ? $this->bindValues($fields) : $this->bindSearchValues($fields);
            if ($type) {
                return $this;
            }
        }
        return false;
    }

    /**
     * Binds a value to a corresponding name or question mark placeholder in the SQL
     * statement that was used to prepare the statement
     * 
     * @param array $fields
     * @return PDOStatement
     * @throws BaseInvalidArgumentException
     */
    protected function bindValues($fields)
    {
        $this->isArray($fields); // don't need
        foreach ($fields as $key => $value) {
            $this->statement->bindValue(':' . $key, $value, (int)$this->bind($value));
        }
        return $this->statement;
    }

    /**
     * Binds a value to a corresponding name or question mark placeholder
     * in the SQL statement that was used to prepare the statement. Similar to
     * above but optimised for search queries
     * 
     * @param array $fields
     * @return mixed
     * @throws BaseInvalidArgumentException
     * @var  $value 
     */
    protected function bindSearchValues( $fields)
    {
        $this->isArray($fields); // don't need
        foreach ($fields as $key => $value) {
            $this->statement->bindValue(':' . $key,  '%' . $value . '%', (int)$this->bind($value));
        }
        return $this->statement;
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function execute()
    {
        if ($this->statement) 
            return $this->statement->execute();
    }
    /**
     * @inheritDoc
     *
     * @return integer
     */
    public function numRows()
    {
        if ($this->statement) return $this->statement->rowCount();
    }
    /**
     * @inheritDoc
     *
     * @return Object
     */
    public function result() 
    {
        if ($this->statement) return $this->statement->fetch(PDO::FETCH_OBJ);
    }
    /**
     * @inheritDoc
     *
     * @return array
     */
    public function results() 
    {
        if ($this->statement) return $this->statement->fetchAll();
    }

    /**
     * @inheritDoc
     */
    public function column()
    {
        if ($this->statement) return $this->statement->fetchColumn();
    }

    /**
     * @inheritDoc
     *
     * @return integer
     */
    public function getLastId()
    {
        try {
            if ($this->dbh->open()) {
                $lastID = $this->dbh->open()->lastInsertId();
                if (!empty($lastID)) {
                    return intval($lastID);
                }
            }
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Returns the query condition merged with the query parameters
     * 
     * @param array $conditions
     * @param array $parameters
     * @return array
     */
    public function buildQueryParameters( $conditions = [],  $parameters = []) 
    {
        return (!empty($parameters) || (!empty($conditions)) ? array_merge($conditions, $parameters) : $parameters);
    }

    /**
     * Persist queries to database
     * 
     * @param string $query
     * @param array $parameters
     * @return mixed
     * @throws Throwable
     */
    public function persist( $sqlQuery,  $parameters)
    {
        try {
            return $this->prepare($sqlQuery)->bindParameters($parameters)->execute();
        } catch(Throwable $throwable){
            throw $throwable;
        }
    }
}