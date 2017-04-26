<?php

namespace WS\BUnit\Command;

use WS\BUnit\Console\Formatter\Output;
use WS\BUnit\DB\Connection;
use WS\BUnit\DB\Dumper;
use WS\BUnit\DB\Updater;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class DBCommand extends BaseCommand {
    const METHOD_CREATE = "create";

    /**
     * @var Dumper
     */
    private $dumper;

    /**
     * @var Updater
     */
    private $updater;

    protected function init() {
        $originalDbParams = $this->getConfig()->getOriginalDbConfig();
        $this->dumper = new Dumper(
            new Connection(
                $originalDbParams['host'],
                $originalDbParams['user'],
                $originalDbParams['password'],
                $originalDbParams['db']
            ),
            $originalDbParams['charset'] ?: "utf8"
        );

        $writer = $this->getConsole()->getWriter();
        $this->dumper->setEchoWriter($writer);

        $testDbParams = $this->getConfig()->getTestDbConfig();
        if (!$testDbParams) {
            throw new \Exception("DB command is used with test database only");
        }
        $this->updater = new Updater(
            new Connection(
                $testDbParams['host'],
                $testDbParams['user'],
                $testDbParams['password'],
                $testDbParams['db']
            ),
            $testDbParams['charset'] ?: "utf8"
        );
        $this->updater->setEchoWriter($writer);
    }

    public function execute() {
        switch ($this->getMethod()) {
            case "create" :
                $this->executeCreate();
                break;
            case "update" :
                $this->executeUpdate();
                break;
            default :
                $writer = $this->getConsole()->getWriter();
                $writer->setColor(Output::COLOR_RED);
                $writer->printLine("Method `{$this->getMethod()}` is not valid for db command.");
                break;
        }
    }

    private function executeCreate() {
        $this->dumper->create();
    }

    private function executeUpdate() {
        $file = $this->dumper->getFilePath();
        $this->updater->update($file);
    }
}
