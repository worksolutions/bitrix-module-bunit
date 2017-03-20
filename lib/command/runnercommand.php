<?php
namespace WS\BUnit\Command;

use WS\BUnit\Console\Formatter\Output;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class RunnerCommand extends BaseCommand {

    public function execute() {
        $writer = $this->getConsole()->getWriter();
        $writer->nextLine();
        $writer->setColor(Output::COLOR_GREEN)
            ->printLine("Result: 3(12)");
    }
}