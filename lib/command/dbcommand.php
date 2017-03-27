<?php
namespace WS\BUnit\Command;

use Bitrix\Main\Entity\Event;
use Bitrix\Main\EventManager;
use WS\BUnit\Console\Formatter\Output;
use WS\BUnit\DB\Config;
use WS\BUnit\DB\Dumper;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class DBCommand extends BaseCommand {
    const METHOD_COPY = "copy";

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
        $this->dumper = new Dumper($config);
    }

    public function execute() {
        switch ($this->getMethod()) {
            case "copy" :
                $this->executeCopy();
                break;
            default :
                $writer = $this->getConsole()->getWriter();
                $writer->setColor(Output::COLOR_RED);
                $writer->printLine("Method `{$this->getMethod()}` is not valid for db command.");
                break;
        }
    }

    private function executeCopy() {
        $writer = $this->getConsole()->getWriter();
        $this->dumper->copy();
        $writer->printLine("action of copy dump");
        $writer->printLine("result of copy");
    }
}
