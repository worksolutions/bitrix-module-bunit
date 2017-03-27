<?php
namespace WS\BUnit\Command;

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use WS\BUnit\Artifacts\ValueDumper;
use WS\BUnit\Cases\BaseCase;
use WS\BUnit\Cases\CaseInvoker;
use WS\BUnit\Console\Formatter\Output;
use WS\BUnit\Report\TestReport;
use WS\BUnit\Report\TestReportResult;
use WS\BUnit\RunConfig;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class RunnerCommand extends BaseCommand {

    /**
     * @var RunConfig
     */
    private $config;

    /**
     * @var CaseInvoker[]
     */
    private $caseInvokers;

    /**
     * @var TestReport
     */
    private $report;

    public function execute() {
        $em = EventManager::getInstance();
        $event = new Event("ws.bunit", "OnTestRun");
        $this->config =  new RunConfig;
        $event->setParameter("config", $this->config);
        $em->send($event);

        if (!$this->config->getCaseFolder()) {
            $this->viewEmpty();
            return;
        }

        $this->initTests();
        $this->runTests();
        $this->viewReport();
    }

    private function viewEmpty() {
        $consoleWriter = $this->getConsole()->getWriter();
        $consoleWriter->nextLine();
        $consoleWriter->setColor(Output::COLOR_RED)->printLine("Case of tests is empty. Check up including of test folder.");
        $consoleWriter->nextLine();
    }

    private function initTests() {
        // include tests files
        $dir = $this->config->getCaseFolder();
        $directoryIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \RecursiveIteratorIterator::SELF_FIRST));
        /** @var \SplFileInfo $file */
        foreach ($directoryIterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            if ($file->getExtension() != 'php') {
                continue;
            }
            if (stripos($file->getPathname(), "TestCase") === false) {
                continue;
            }
            include $file->getPathname();
        }

        foreach(get_declared_classes() as $className ){
            if(!is_subclass_of($className, BaseCase::className()) ) {
                continue;
            }
            $refClass = new \ReflectionClass($className);
            if ($refClass->isAbstract()) {
                continue;
            }
            $this->caseInvokers[] = $this->createCaseInvoker($refClass);
        }
        $this->report = new TestReport();
    }

    private function runTests() {
        foreach ($this->caseInvokers as $invoker) {
            $invoker->invoke();
            $this->report->apply($invoker->getReport());
        }

        $writer = $this->getConsole()->getWriter();
        $countInLine = 25;
        $counter = 0;
        foreach ($this->report->getResults() as $result) {
            $counter++;
            if ($counter % $countInLine == 0) {
                $writer->nextLine();
            }
            switch ($result->getResult()) {
                case TestReportResult::RESULT_SKIP:
                    $writer->printChars("S");
                    break;
                case TestReportResult::RESULT_ERROR:
                    $writer->printChars("E");
                    break;
                case TestReportResult::RESULT_SUCCESS:
                    $writer->printChars(".");
                    break;
            }
        }
    }

    private function viewReport() {
        // view errors of exceptions or fails

        $writer = $this->getConsole()->getWriter();
        $writer->nextLine();
        $writer->nextLine();

        if (!$this->report->isSuccess()) {
            $writer->setColor(Output::COLOR_RED)->printLine(sprintf("Test failed. %s of %s have been with error.", $this->report->errorCount(), $this->report->count()));
        } else if ($this->report->isSkip()) {
            $writer->setColor(Output::COLOR_YELLOW)->printLine(sprintf("Test was not absolutely right. %s tests of %s have been skipped.", $this->report->skippedCount(), $this->report->count()));
        } else {
            $writer->setColor(Output::COLOR_GREEN)->printLine(sprintf("Test success. %s tests.", $this->report->count()));
        }
        $writer->nextLine();

        $writer->setColor(0)->printLine("Errors:");
        $writer->nextLine();

        $number = 1;
        foreach ($this->report->getResults() as $result) {
            $message = $result->getMessage();
            if (!$message) {
                continue;
            }
            $writer->setColor(Output::COLOR_RED)->printLine($number.") ".$result->getClass()."::".$result->getMethod());
            $writer->setColor(0)->printLine($message);

            if ($result->hasExpected()) {
                $writer->setColor(Output::COLOR_YELLOW)->printLine("Actual: ".(ValueDumper::value($result->getExpected())->toString()));
                if ($result->getExpected() !== $result->getActual()) {
                    $writer->setColor(Output::COLOR_YELLOW)->printLine("Expected: ".(ValueDumper::value($result->getExpected())->toString()));
                }
                $writer->setColor(0);
            }

            $writer->nextLine();
            $number++;
        }
    }

    /**
     * @param $refClass
     * @return CaseInvoker
     */
    private function createCaseInvoker($refClass) {
        return new CaseInvoker($refClass);
    }
}
