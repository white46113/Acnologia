<?php

namespace logia\LiquidOrm\QueryBuilder;

Class QueryBuilder implements QueryBuilderInterface
{

    protected $key = [];
    const SQL_DEFAULT = [
        'conditions' => [],
        'selectors'=> [],
        'replace'=> false,
        'distinct'=>false,
        'from'=>[],
        'where'=>null,
        'and'=>[],
        'or'=>[],
        'orderby'=>[],
        'fields'=>[],
        'primary_key'=>'',
        'table'=>'',
        'type'=>'',
        'raw'=>''
    ];
    const SQL_TYPE = ['insert','select','update','delete','raw'];
    protected $sql_query = '';
    public function __construct(){

    }
    public function queryBuilder($args = []){
        if( count($args) < 0){
            throw new QueryBuilderInvalidArgumentException();
        }
        $arg = array_merge(self::SQL_DEFAULT,$args);
        $this->key = $arg;
        return $this;
    }
    private function isQueryTypeValid($type){
        if(in_array($type,self::SQL_TYPE)){
            return true;
        }
        return false;
    }
    public function insertQuery(){
        if($this->isQueryTypeValid('insert')){
            if(is_array($this->key['fields'] && count($this->key['fields']))){
                $indexs = array_keys($this->key['fields']);
                $value = array(implode(', ',$indexs),":".implode(', :',$indexs));
                $this->sql_query = "INSERT INTO {$this->key['table']} ({$value[0]}) VALUES ({$value[1]})";
                return $this->sql_query;
            }
        }
        return false;
    }
    public function selectQuery(){
        if($this->isQueryTypeValid('select')){
            $selector = (!empty($this->key['selectors'])) ? implode(', ',$this->key['selectors']) : '*';
            $this->sql_query = "SELECT $selector FROM {$this->key['table']}";
            $this->sql_query = $this->hasCondition();
            return $this->sql_query;
        }
        return false;
    }

    public function updateQuery(){
        if ($this->isQueryTypeValid('update')) {
            if (is_array($this->key['fields']) && count($this->key['fields']) > 0) {
                $values = '';
                foreach ($this->key['fields'] as $field) {
                    if ($field !== $this->key['primary_key']) {
                        $values .= $field . " = :" . $field . ", ";
                         }
                    }
                $values = substr_replace($values, '', -2);
                if (count($this->key['fields']) > 0) {
                    $this->sqlQuery = "UPDATE {$this->key['table']} SET {$values} WHERE {$this->key['primary_key']} = :{$this->key['primary_key']} LIMIT 1";
                    if (isset($this->key['primary_key']) && $this->key['primary_key'] === '0') {
                        unset($this->key['primary_key']);
                        $this->sqlQuery = "UPDATE {$this->key['table']} SET {$values}";
                        }
                    }
                    return $this->sqlQuery;
                }
            }
        return false;
    }
    public function deleteQuery(){
        if ($this->isQueryTypeValid('update')) {
            $index = array_keys($this->key['conditions']);
            $this->sql_query = "DELETE FROM{$this->key['table']} WHERE {$index[0]} = :{$index[0]}";
            $bulk_delete = array_keys($this->key['fields']);
            if(is_array($bulk_delete) && count($bulk_delete) > 0 ){
                for($i = 0; $i < count($bulk_delete) ; $i++){
                    $this->sql_query = "DELETE FROM{$this->key['table']} WHERE {$index[0]} = :{$index[0]}";
                }
            }
            return $this->sqlQuery; 
        }
        return false;
    }

    private function hasCondition(){
        if (isset($this->key['conditions']) && $this->key['conditions'] !='') {
            if (is_array($this->key['conditions'])) {
                $sort = [];
                foreach (array_keys($this->key['conditions']) as $where) {
                    if (isset($where) && $where !='') {
                        $sort[] = $where . " = :" . $where;
                    }
                }
                if (count($this->key['conditions']) > 0) {
                    $this->sqlQuery .= " WHERE " . implode(" AND ", $sort);
                }
            }
        } else if (empty($this->key['conditions'])) {
            $this->sqlQuery = " WHERE 1";
        }
        $this->sqlQuery .= $this->orderByQuery();
        $this->sqlQuery .= $this->queryOffset();

        return $this->sqlQuery;
    }
    

}