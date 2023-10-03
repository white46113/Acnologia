<?php

namespace logia\Router;
use logia\Router\RouterInterface;
Class Router implements RouterInterface
{
    protected  $routes = [];
    protected  $params = [];
    protected $controller_sufix = 'controller';

    public function add($route = '',$params = []){
        $this->routes[$route] = $params;
    }
    public function dispatch($url = ''){
        if($this->match($url)){
            $controller_string = $this->param['controller'];
            $controller_string = $this->transformUpperCamelCase($controller_string);
            $controller_string = $this->getNameSpace($controller_string);
            if(class_exists($controller_string)){
                $controller_object = new $controller_string;
                $action = $this->params['action'];
                $action = $this->transformCamelCase($action);
                if(is_callable($controller_object,$action)){
                    $controller_object->$action();
                }else{
                    throw new Exception();
                }
                
            }else{
                throw new Exception();
            }
        }else{
            throw new Exception();
        }
    } 
    public function transformUpperCamelCase($string = ''){
        return str_replace(' ','',ucwords(str_replace('-',' ',$string)));
    }
    public function transformCamelCase($string = ''){
        return lcfirst($this->transformUpperCamelCase($string));
    }
    public function match($url = ''){
        foreach($this->$routes as $route => $params){
            if(preg_match($route,$url,$matches)){
                foreach ($matches as $key => $param){
                    if(is_string($key)){
                        $params[$key] = $param;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }
    public function getNameSpace($string = ''){
        $name_space = 'App\Controller\\';
        if(array_key_exists('namespace',$this->params)){
            $name_space .= $this->params['namespace'];
        }
        return $name_space;
    }

}


?>