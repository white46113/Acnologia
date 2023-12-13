<?php

namespace logia\Application;

use logia\Application\Config;
use logia\Yaml\YamlConfig;
use logia\Traits\SystemTrait;
use logia\Router\RouterFactory;

class Application
{

    /** @var string */
    protected  $appRoot;

    protected  $options = [];

    /**
     * Main class constructor
     *
     * @param string $appRoot
     */
    public function __construct($appRoot)
    {
        
        $this->appRoot = $appRoot;
    }

    /**
     * Execute at application level
     *
     * @return self
     */
    public function run()
    {
        
        $this->constants();
        if (version_compare($phpVersion = PHP_VERSION, $coreVersion = Config::LOGIA_MIN_VERSION, '<')) {
            die(sprintf('You are runninig PHP %s, but the core framework requires at least PHP %s', $phpVersion, $coreVersion));
        }
        $this->environment();
        $this->errorHandler();
        return $this;
    }

    /**
     * Define framework and application directory constants
     *
     * @return void
     */
    private function constants()
    {
        defined('DS') or define('DS', '/');
        defined('APP_ROOT') or define('APP_ROOT', $this->appRoot);
        defined('CONFIG_PATH') or define('CONFIG_PATH', APP_ROOT . DS . 'Config');
        defined('TEMPLATE_PATH') or define('TEMPLATE_PATH', APP_ROOT . DS . 'App/');
        defined('LOG_DIR') or define('LOG_DIR', APP_ROOT . DS . 'tmp/log');

    }

    /**
     * Set default framework and application settings
     *
     * @return void
     */
    private function environment()
    {
        ini_set('default_charset', 'UTF-8');
    }

    /**
     * Convert PHP errors to exception and set a custom exception
     * handler. Which allows us to take control or error handling 
     * so we can display errors in a customizable way
     *
     * @return void
     */
    private function errorHandler()
    {
        error_reporting(E_ALL | E_STRICT);
        set_error_handler('logia\ErrorHandling\ErrorHandling::errorHandler');
        set_exception_handler('logia\ErrorHandling\ErrorHandling::exceptionHandler');
    }

    public function setSession()
    {
        SystemTrait::sessionInit(true);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param array $routes
     * @return self
     */
    public function setRouteHandler($url = null, $routes = [])
    {
        $url = ($url) ? $url : $_SERVER['QUERY_STRING'];
        $routes = ($routes) ? $routes : YamlConfig::file('routes');
        
        $factory = new RouterFactory($url, $routes);
        $factory->create(\logia\Router\Router::class)->buildRoutes();
        return $this;
    }

}