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
    protected $_env;

    /**
     * @var array
     */
    protected $_configuration;

    public function __construct($env, array $configuration, \Phalcon\DiInterface $di = null)
    {
        $this->_env = $env;

        switch ($this->getEnv()) {
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

        $this->_configuration = $configuration;

        if (is_null($di)) {
            $di = new \Phalcon\Di\FactoryDefault();
        }

        parent::__construct($di);
    }

    public function registerLoader()
    {
        $config = $this->getConfiguration();

        $loader = new \Phalcon\Loader();

        if (isset($config['application']['registerNamespaces'])) {
            $loadedNamespaces = $config['application']['registerNamespaces'];
        } else {
            $loadedNamespaces = [];
        }

        foreach ($config['application']['modules'] as $module) {
            $loadedNamespaces[$module . '\Models'] = $config['paths']['modulesDir'] . $module . '/models';
        }

        if (isset($config['application']['registerDirs'])) {
            $loader->registerDirs($config['application']['registerDirs']);
        }

        $loader->registerNamespaces($loadedNamespaces)->register();
    }

    public function bootstrap()
    {
        $this->registerLoader();
    }

    public function run($uri = null)
    {
        $this->handle($uri)
            ->send();
    }

    /**
     * Get environment
     *
     * @return string
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * Get configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->_configuration;
    }
}