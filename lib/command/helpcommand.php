<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Command;

use WS\BUnit\Console\Formatter\Output;

class HelpCommand extends BaseCommand {

    public function execute() {
        $consoleWriter = $this->getConsole()->getWriter();
        $consoleWriter->nextLine();
        $consoleWriter->setColor(Output::COLOR_YELLOW)->printLine("Usage:");
        $consoleWriter->setColor(0)->printLine("   php bunit.php <command> [arg1] [arg2] ...");
        $consoleWriter->nextLine();

        $consoleWriter->setColor(Output::COLOR_YELLOW)->printChars("Help command:");
        $consoleWriter->setColor(0)->printLine(" Shows this manual for right usage");
        $consoleWriter->nextLine();
        $consoleWriter->setColor(Output::COLOR_GREEN)->printLine("   php bunit.php help");
        $consoleWriter->nextLine();

        $consoleWriter->setColor(Output::COLOR_YELLOW)->printChars("Command run:");
        $consoleWriter->setColor(0)->printLine(" Runs testing process");
        $consoleWriter->nextLine();
        $consoleWriter->setColor(Output::COLOR_GREEN)->printLine("   php bunit.php run [-<scope>] [-<tagN>]");
        $consoleWriter->setColor(0)->printLine("     - [-<scope>]       Type of testcases which are declared in project");
        $consoleWriter->setColor(0)->printLine("     - [-<tag>]         Test tags");
        $consoleWriter->nextLine();

        $consoleWriter->setColor(Output::COLOR_YELLOW)->printChars("Command db:");
        $consoleWriter->setColor(0)->printLine(" Works with project fixtures");
        $consoleWriter->nextLine();
        $consoleWriter->setColor(Output::COLOR_GREEN)->printLine("   php bunit.php db");
        $consoleWriter->nextLine();
    }
}
