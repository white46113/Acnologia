<?php

namespace logia\Rounter;

use logia\Router\Router;
use logia\Yaml\YamlConfig;

class RounterManager extends Router
{

    public static function dispatchRoute($url)
    {
        $router = new Router;
        $routes = YamlConfig::file('routes');

        if(is_array($routes) && !empty($routes)){
            $args = [];
            foreach($routes as $key => $route){
                if(isset($route['namespace']) && $route['namespace'] != ''){
                    $args['namespace'] = $route['namespace'];
                }
                elseif(isset($route['controller']) && $route != ''){
                    $args['controller'] = $route['controller'];
                    $args['action'] = $route['action'];
                }

                if(isset($key)){
                    $router->add($key,$args);
                }

            }
            $router->dispatch($url);
        }

    }

}