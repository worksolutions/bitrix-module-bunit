<?php

namespace WS\BUnit\Console;

use WS\BUnit\Command\BaseCommand;
use WS\BUnit\Command\DBCommand;
use WS\BUnit\Command\RunnerCommand;
use WS\BUnit\Command\HelpCommand;
use WS\BUnit\Console\Formatter\Output;

class Console {

    private $action;

    /**
     * @var array
     */
    private $params;

    public function __construct($args) {
        global $APPLICATION;
        $APPLICATION->ConvertCharsetArray($args, "UTF-8", LANG_CHARSET);
        $this->writer = new Writer(new Output(), fopen('php://stdout', 'w'));

        array_shift($args);
        $this->action = isset($args[0]) ? $args[0] : 'help';

        array_shift($args);
        $additionalCommand = $args[0];
        if ($additionalCommand[0] != '-') {
            $this->params[$additionalCommand] = null;
        }

        foreach ($args as $argument) {
            if ($argument[0] !== '-') {
                continue;
            }
            $argument = substr($argument, 1);
            list($name, $value) = explode("=", $argument);
            $this->params[$name] = $value;
        }
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

    /**
     * @return Writer
     */
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
