<?php

namespace WS\BUnit\Console;

use WS\BUnit\Command\BaseCommand;
use WS\BUnit\Command\DBCommand;
use WS\BUnit\Command\RunnerCommand;
use WS\BUnit\Command\HelpCommand;
use WS\BUnit\Console\Formatter\Output;

class Console {
    /**
     * @var resource
     */
    private $out;

    private $action;

    public function __construct($args) {
        global $APPLICATION;
        $APPLICATION->ConvertCharsetArray($args, "UTF-8", LANG_CHARSET);
        $this->writer = new Writer(new Output(), fopen('php://stdout', 'w'));
        array_shift($args);
        $this->params = $args;
        $this->action = isset($this->params[0]) ? $this->params[0] : 'help';
    }

    private static function commands() {
        return array(
            'help' => HelpCommand::className(),
            'db' => DBCommand::className(),
            'run' => RunnerCommand::className(),
        );
    }

    /**
     * @param $str
     * @param $color "Example Output::COLOR_BLACK
     * @return $this
     */
    public function printChars($str, $color = 0) {
        global $APPLICATION;
        $str = $APPLICATION->ConvertCharset($str, LANG_CHARSET, "UTF-8");
        $this->getWriter()->setColor($color)->printChars($str);
        return $this;
    }

    public function getWriter() {
        return $this->writer;
    }

    /**
     * @param $str
     * @param $color "Example Output::COLOR_BLACK
     * @return $this
     */
    public function printLine($str, $color = 0) {
        $this->printChars($str, $color);
        $this->getWriter()->toStream("\n");
        return $this;
    }

    public function readLine() {
        return trim(fgets(STDIN));
    }

    /**
     * @return BaseCommand
     * @throws ConsoleException
     */
    public function getCommand() {
        $commands = static::commands();
        if (!$commands[$this->action]) {
            throw new ConsoleException("Action `{$this->action}` is not supported");
        }
        return new $commands[$this->action]($this->params, $this);
    }
}
