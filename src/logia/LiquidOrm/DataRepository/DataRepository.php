<?php
namespace logia\LiquidOrm\DataRepository;

use logia\Base\Exception\BaseInvalidArgumentException;
use logia\LiquidOrm\DataRepository\DataRepositoryInterface;
use logia\LiquidOrm\EntityManager\EntityManagerInterface;
use logia\Utility\Paginator;
use logia\Utility\Sortable;
use Throwable;

class DataRepository implements DataRepositoryInterface
{

    protected  $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Checks whether the arguement is of the array type else throw an exception
     *
     * @param array $conditions
     * @return void
     */
    private function  isArray(array $conditions)
    {
        if (!is_array($conditions))
            throw new BaseInvalidArgumentException('The argument supplied is not an array');
    }

    /**
     * Checks whether the argument is set else throw an exception
     *
     * @param integer $id
     * @return void
     */
    private function isEmpty(int $id)
    {
        if (empty($id))
            throw new BaseInvalidArgumentException('Argument should not be empty');
    }

    /**
     * @inheritDoc
     *
     * @param integer $id
     * @return array
     * @throws BaseInvalidArgumentException
     */
    public function find(int $id)
    {
        $this->isEmpty($id);
        try{
            return $this->findOneBy(['id' => $id]);
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function findAll()
    {
        try{
            return $this->findBy();
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @param array $selectors
     * @param array $conditions
     * @param array $parameters
     * @param array $optional
     * @return array
     */
    public function findBy(array $selectors = [], array $conditions = [], array $parameters = [], array $optional = [])
    {
        try{
            return $this->em->getCrud()->read($selectors, $conditions, $parameters, $optional);
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @param array $conditions
     * @return array
     * @throws BaseInvalidArgumentException
     */
    public function findOneBy(array $conditions) 
    {
        $this->isArray($conditions);
        try{
            return $this->em->getCrud()->read([], $conditions);
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @param array $conditions
     * @param array $selectors
     * @return Object
     */
    public function findObjectBy(array $conditions = [], array $selectors = []) 
    {
        $this->isArray($conditions);
        try{
            return $this->em->getCrud()->get($selectors, $conditions);
        } catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @param array $selectors
     * @param array $conditions
     * @param array $parameters
     * @param array $optional
     * @return array
     * @throws BaseInvalidArgumentException
     */
    public function findBySearch(array $selectors = [], array $conditions = [], array $parameters = [], array $optional = [])
    {
        $this->isArray($conditions);
        try{
            return $this->em->getCrud()->search($selectors, $conditions, $parameters, $optional);
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @param array $conditions
     * @return boolean
     * @throws BaseInvalidArgumentException
     */
    public function findByIdAndDelete(array $conditions) 
    {
        $this->isArray($conditions);
        try{
            $result = $this->findOneBy($conditions);
            if ($result !=null && count($result) > 0) {
                $delete = $this->em->getCrud()->delete($conditions);
                if ($delete) {
                    return true;
                }
            }
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @param array $fields
     * @param integer $id
     * @return boolean
     * @throws BaseInvalidArgumentException
     */
    public function findByIdAndUpdate(array $fields = [], int $id)
    {
        $this->isArray($fields);
        try{
            $result = $this->findOneBy([$this->em->getCrud()->getSchemaID() => $id]);
            if ($result !=null && count($result) > 0) {
                $params = (!empty($fields)) ? array_merge([$this->em->getCrud()->getSchemaID() => $id], $fields) : $fields;
                $update = $this->em->getCrud()->update($params, $this->em->getCrud()->getSchemaID());
                if ($update) {
                    return true;
                }
            }
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @param array $args
     * @param Object $request
     * @return array
     */
    public function findWithSearchAndPaging(Object $request, array $args = []) 
    { 
        list($conditions, $totalRecords) = $this->getCurrentQueryStatus($request, $args);
        $sorting = new Sortable($args['sort_columns']);
        $paging = new Paginator($totalRecords, $args['records_per_page'], $request->query->getInt('page', 1));
        
        $parameters = ['limit' => $args['records_per_page'], 'offset' => $paging->getOffset()];
        $optional = ['orderby' => $sorting->getColumn() . ' ' . $sorting->getDirection()];

        if ($request->query->getAlnum($args['filter_alias'])) {
            $searchRequest = $request->query->getAlnum($args['filter_alias']);
            if (is_array($args['filter_by'])) {
                for ($i=0; $i < count($args['filter_by']); $i++) {
                    $searchConditions = [$args['filter_by'][$i] => $searchRequest];
                }
            }
            $results = $this->findBySearch($args['filter_by'], $searchConditions);
        } else {
            $queryConditions = array_merge($args['additional_conditions'], $conditions);
            $results = $this->findBy($args['selectors'], $queryConditions, $parameters, $optional);
        }
        return [
            $results,
            $paging->getPage(),
            $paging->getTotalPages(),
            $totalRecords,
            $sorting->sortDirection(),
            $sorting->sortDescAsc(),
            $sorting->getClass(),
            $sorting->getColumn(),
            $sorting->getDirection()
        ];
    }

    private function getCurrentQueryStatus(Object $request, array $args)
    {
        $totalRecords = 0;
        $req = $request->query;
        $status = $req->getAlnum($args['query']);
        $searchResults = $req->getAlnum($args['filter_alias']);
        if ($searchResults) {
            for ($i=0; $i < count($args['filter_by']); $i++) {
                $conditions = [$args['filter_by'][$i] => $searchResults];
                $totalRecords = $this->em->getCrud()->countRecords($conditions, $args['filter_by'][$i]);
            }
        } else if ($status) {
            $conditions = [$args['query'] => $status];
            $totalRecords = $this->em->getCrud()->countRecords($conditions);
        } else {
            $conditions = [];
            $totalRecords = $this->em->getCrud()->countRecords($conditions);
        }
        return [
            $conditions,
            $totalRecords
        ];
    }


    /**
     * @inheritDoc
     *
     * @param integer $id
     * @param array $selectors
     * @return self
     */
    public function findAndReturn(int $id, array $selectors = []) 
    { 
        if (empty($id) || $id === 0) {
            throw new BaseInvalidArgumentException('Please add a valid argument');
        }
        try {
            $this->findAndReturn = $this->findObjectBy($selectors, ['id' => $id]);
            return $this;
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     *
     * @return Object|null
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function or404()
    {
        if ($this->findAndReturn !=null) {
            return $this->findAndReturn;
        } else {
            header('HTTP/1.1 404 not found');
            $twig = new \logia\Base\BaseView();
            $twig->twigRender('error/404.html.twig');
            exit;
        }
    }


}