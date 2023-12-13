<?php

namespace logia\Router;
// require_once ROOT_PATH . '/App/Controller/HomeController.php';
use logia\Router\RouterInterface;
use logia\Router\Exception\RouterException;
use logia\Router\Exception\RouterBadMethodCallException;
Class Router implements RouterInterface
{
    protected  $routes = [];
    protected  $params = [];
    protected $controller_sufix = 'Controller';

    public function add($route = '',$params = []){
         // Convert the route to a regular expression: escape forward slashes
         $route = preg_replace('/\//', '\\/', $route);

         // Convert variables e.g. {controller}
        //  $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
         // Convert variables with custom regular expressions e.g. {id:\d+}
        //  $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
 
         // Add start and end delimiters, and case insensitive flag
         $route = '/^' . $route . '$/i';
         
         $this->routes[$route] = $params;
    }
    public function dispatch($url = ''){
        if($this->match($url)){
            $controller_string = $this->params['controller'] .$this->controller_sufix;
            $controller_string = $this->transformUpperCamelCase($controller_string);   
            $controller_string = $this->getNameSpace($controller_string).$controller_string;     
            if(class_exists($controller_string)){
                $controller_object = new $controller_string;
                $action = $this->params['action'];
                $action = $this->transformCamelCase($action);
                if(method_exists($controller_object,$action)){
                   $controller_object->$action();
                }else{
                    throw new RouterBadMethodCallException();
                }
                
            }else{
                throw new RouterException('no class found');
            }
        }else{
            throw new RouterException();
        }
    } 
    public function transformUpperCamelCase($string = ''){
        return str_replace(' ','',ucwords(str_replace('-',' ',$string)));
    }
    public function transformCamelCase($string = ''){
        return lcfirst($this->transformUpperCamelCase($string));
    }
    public function match($url = ''){
        foreach($this->routes as $route => $params){
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