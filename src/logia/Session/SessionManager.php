<?php

namespace logia\Session;

use logia\Session\SessionFactory;
use logia\Yaml\YamlConfig;

class SessionManager
{

    /**
     * Create an instance of our session factory and pass in the default session storage
     * we will fetch the session name and array of options from the core yaml configuration
     * files
     *
     * @return void
     */
    public static function initialize()
    {
        // echo 'test';die;
        $factory = new SessionFactory();
        return $factory->create('Acnologia', \logia\Session\Storage\NativeSessionStorage::class, YamlConfig::file('session'));
    }

}