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

    public function __construct(array $params, Console $console) {
        $this->params = $params;
        $this->console = $console;
        $this->init();
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
}
