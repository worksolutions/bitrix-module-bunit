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
        $consoleWriter->setColor(0)->printLine("   php bunit <command> [<method>] [arg1] [arg2] ...");
        $consoleWriter->nextLine();

        $consoleWriter->setColor(Output::COLOR_YELLOW)->printChars("Help command:");
        $consoleWriter->setColor(0)->printLine(" Shows this manual for right usage");
        $consoleWriter->nextLine();
        $consoleWriter->setColor(Output::COLOR_GREEN)->printLine("   php bunit help");
        $consoleWriter->nextLine();

        $consoleWriter->setColor(Output::COLOR_YELLOW)->printChars("Command run:");
        $consoleWriter->setColor(0)->printLine(" Runs testing process");
        $consoleWriter->nextLine();
        $consoleWriter->setColor(Output::COLOR_GREEN)->printLine("   php bunit run [-<label>] [-<class>]");
        $consoleWriter->setColor(0)->printLine("     - [-<label>]       Includes tests which only have that label");
        $consoleWriter->setColor(0)->printLine("     - [-<class>]       Runs test only for pointed class");
        $consoleWriter->nextLine();

        $consoleWriter->setColor(Output::COLOR_YELLOW)->printChars("Command db:");
        $consoleWriter->setColor(0)->printLine(" Provides several helpful functions");
        $consoleWriter->nextLine();
        $consoleWriter->setColor(Output::COLOR_GREEN)->printLine("   php bunit db <method>");
        $consoleWriter->setColor(0)->printLine("   Methods:");
        $consoleWriter->setColor(0)->printLine("     create       Creates mysql dump from original DB into system temporary file");
        $consoleWriter->setColor(0)->printLine("     update       Updates test DB from dump file");
        $consoleWriter->nextLine();
        $consoleWriter->nextLine();
    }
}
