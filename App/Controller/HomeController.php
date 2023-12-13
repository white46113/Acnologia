<?php

namespace App\Controller;

// use App\Model\UserModel;
use logia\Yaml\YamlConfig;
use App\DataColumns\UserColumns;
use logia\Base\BaseController;
// use logia\Datatable\Datatable;
use logia\Http\RequestHandler;

class HomeController extends BaseController
{

    public function __construct($routeParams)
    {
        parent::__construct($routeParams);

    }

    public function index()
    {
        echo 'salman "s Frame work';
        // $args = YamlConfig::file('controller')['user'];
        // $user = new UserModel();
        // $repository = $user->getRepo()->findWithSearchAndPaging((new RequestHandler())->handler(), $args);

        // $tableData = (new Datatable())->create(UserColumns::class, $repository, $args)->setAttr(['table_id' => 'sexytable', 'table_class' => ['youtube-datatable']])->table();

        // $this->render('client/home/index.html.twig', [
        //     'table' => $tableData,
        //     'pagination' => (new Datatable())->create(UserColumns::class, $repository, $args)->pagination()
        // ]);
    }
    protected function before()
    {}
    protected function after()
    {}


}
