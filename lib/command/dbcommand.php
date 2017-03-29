<?php

namespace WS\BUnit\Command;

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use WS\BUnit\Console\Formatter\Output;
use WS\BUnit\DB\Config;
use WS\BUnit\DB\Dumper;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class DBCommand extends BaseCommand {
    const METHOD_CREATE = "create";

    /**
     * @var Dumper
     */
    private $dumper;

    protected function init() {
        $em = EventManager::getInstance();
        $event = new Event("ws.bunit", "OnDbRun");
        $config =  new Config();
        $event->setParameter("config", $config);
        $em->send($event);

        $this->dumper = new Dumper($config->getBaseConnection());
        $writer = $this->getConsole()->getWriter();
        $this->dumper->setEchoWriter($writer);
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
        $this->dumper->update();
    }
}
