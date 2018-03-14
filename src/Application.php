<?php

namespace Phalconit;


class Application extends \Phalcon\Mvc\Application
{
    const ENV_DIST = 'dist';
    const ENV_TEST = 'test';
    const ENV_DEV  = 'dev';

    /**
     * @var string
     */
    protected $env;

    public function __construct($env, \Phalcon\DiInterface $di = null)
    {
        $this->env = $env;

        switch ($this->env) {
            case self::ENV_DIST:
                ini_set('display_errors', 0);
                ini_set('display_startup_errors', 0);
                error_reporting(0);
                break;
            case self::ENV_TEST:
            case self::ENV_DEV:
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(-1);
                break;
            default:
                throw new \Exception('Wrong environment variable $env passed: '. $env);
        }

        if (is_null($di)) {
            $di = new \Phalcon\Di\FactoryDefault();
        }

        parent::__construct($di);
    }
}