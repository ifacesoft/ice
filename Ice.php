<?php
namespace ice;

use ice\core\action\Front;
use ice\core\action\Front_Ajax;
use ice\core\action\Front_Cli;
use ice\core\Config;
use ice\core\Loader;
use ice\core\Logger;
use ice\core\View;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 23.11.13
 * Time: 18:19
 */
class Ice
{
    const ENGINE = 'Ice';

    private static $_ice = null;

    /** @var Config */
    private static $_config = null;

    private static $_project = null;

    /**
     * Main module path
     * @var null
     */
    private static $_projectPath = null;

    /**
     * Путь до директории с модулями (в т.ч движка и главного модуля)
     * @var null
     */
    private static $_rootPath = null;

    /**
     * Путь до движка Ice
     * @var null
     */
    private static $_enginePath = null;

    /** @var View */
    private $view = null;

    private function __construct($project)
    {
        self::$_project = $project;
        $this->bootstrap();
    }

    public function bootstrap()
    {
        setlocale(LC_ALL, 'ru_RU.UTF-8');
        setlocale(LC_NUMERIC, 'C');

        date_default_timezone_set('UTC');

        require_once $this->getEnginePath() . 'Core/Config.php';
        require_once $this->getEnginePath() . 'Core/Data/Provider.php';
        require_once $this->getEnginePath() . 'Data/Provider/Buffer.php';
        require_once $this->getEnginePath() . 'Core/Loader.php';

        Loader::register('Ice\core\Loader::load');
        Logger::init($this->getProject());
    }

    /** var Ice */
    public static function get($project)
    {
        if (self::$_ice !== null) {
            return self::$_ice;
        }

        self::$_ice = new Ice($project);

        return self::$_ice;
    }

    public static function getProject()
    {
        return self::$_project;
    }

    public static function getEnginePath()
    {
        if (self::$_enginePath !== null) {
            return self::$_enginePath;
        }

        self::$_enginePath = __DIR__ . '/';
        return self::$_enginePath;
    }

    public static function getRootPath()
    {
        if (self::$_rootPath !== null) {
            return self::$_rootPath;
        }

        self::$_rootPath = dirname(self::getEnginePath()) . '/';
        return self::$_rootPath;
    }

    public static function getProjectPath()
    {
        if (self::$_projectPath !== null) {
            return self::$_projectPath;
        }

        self::$_projectPath = self::getRootPath() . self::getProject() . '/';
        return self::$_projectPath;
    }

    /**
     * @return Config
     */
    public static function getConfig()
    {
        if (self::$_config !== null) {
            return self::$_config;
        }

        $configFile = Ice::getProjectPath() . Ice::getProject() . '.conf.php';

        $config = file_exists($configFile)
            ? include $configFile
            : include Ice::getEnginePath() . self::ENGINE . '.conf.php';

        $_config = array();
        foreach ($config['modules'] as $moduleName => $moduleConfig) {
            $configFile = $moduleConfig['path'] . $moduleName . '.conf.php';

            if (file_exists($configFile)) {
                $_config = array_merge_recursive($_config, include $configFile);
            }
        }

        self::$_config = new Config($_config, self::ENGINE . '.conf.php');

        return self::$_config;
    }

    /**
     * @return Ice
     */
    public function run()
    {
        try {
            if (!empty($_SERVER['argv'])) {
                $this->view = Front_Cli::call();
                return $this;
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                $this->view = Front_Ajax::call();
                return $this;
            }

            $this->view = Front::call();
        } catch (\Exception $e) {
            Logger::outputErrors($e);
        }

        return $this;
    }

    public function flush()
    {
        $this->view->display();
    }
}