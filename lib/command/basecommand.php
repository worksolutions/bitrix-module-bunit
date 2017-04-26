<?php
namespace WS\BUnit\Command;

use WS\BUnit\Config;
use WS\BUnit\Console\Console;
use WS\BUnit\Console\Formatter\Output;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
abstract class BaseCommand {

    /**
     * @var array
     */
    private $params;

    /**
     * @var Console
     */
    private $console;

    /**
     * @var string
     */
    private $method;

    /**
     * @var Config
     */
    private $config;

    final public function __construct(array $params, Console $console, Config $config) {
        $this->params = $params;
        list($name, $value) = each($this->params);
        if ($value === null) {
            $this->method = $name;
        } else {
            $this->params[$name] = $value;
        }
        $this->console = $console;

        $this->config = $config;
        if (!$this->testConfig()) {
            throw new \Exception("Config is wrong.");
        }

        $this->init();
    }

    /**
     * @return string|null
     */
    public function getMethod() {
        return $this->method;
    }

    static public function className() {
        return get_called_class();
    }

    /**
     * Hook for children
     */
    protected function init() {
    }

    public abstract function execute();

    /**
     * @return Console
     */
    public function getConsole() {
        return $this->console;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getParam($name, $default = null) {
        return  $this->params[$name] ? $this->params[$name] : $default;
    }

    /**
     * @return Config
     */
    public function getConfig() {
        return $this->config;
    }

    private function testConfig() {
        if ($this->config->test()) {
            return true;
        }

        $writer = $this->console->getWriter();
        $writer->setColor(Output::COLOR_RED);
        $writer->nextLine();
        foreach ($this->config->getTestErrors() as $error) {
            $writer->printLine($error);
        }
        return false;
    }
}
