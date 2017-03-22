<?php
namespace WS\BUnit\Command;

use WS\BUnit\Console\Console;

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

    public function __construct(array $params, Console $console) {
        $this->params = $params;
        list($name, $value) = each($this->params);
        if ($value === null) {
            $this->method = $name;
        } else {
            $this->params[$name] = $value;
        }
        $this->console = $console;
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
}
