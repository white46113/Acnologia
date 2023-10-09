<?php

namespace logia\LiquidOrm\EntityManager;

interface CrudInterface
{
    public function  getSchema();

    public function getSchemaId();

    public function lastId();

    public function create($fields = []);

    public function read($selectors = [],$conditions = [],$parameters = [],$optional = []);

    public function update($fields = [],$primaryKey);

    public function delete($conditions = []);

    public function search($selectors = [],$conditions = []);

    public function rawQuery(string $rawQuery, $conditions = []);


}
 