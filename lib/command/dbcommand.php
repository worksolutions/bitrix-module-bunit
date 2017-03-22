<?php
namespace WS\BUnit\Command;

use WS\BUnit\Console\Formatter\Output;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class DBCommand extends BaseCommand {
    const METHOD_COPY = "copy";

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
        $writer->printLine("action of copy dump");
        $writer->printLine("result of copy");
    }
}
