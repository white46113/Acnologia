<?php

namespace logia\LiquidOrm\EntityManager;
use logia\LiquidOrm\EntityManager\CrudInterface;



class EntityManager implements EntityManagerInterface
{
    protected $crud;
    public function  __construct(CrudInterface $crud){
        $this->crud = $crud;
    }

    public function getCrud(){
        return $this->crud;
    }
}