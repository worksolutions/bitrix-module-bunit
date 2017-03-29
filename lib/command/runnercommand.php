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
use WS\BUnit\Run\Config;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class RunnerCommand extends BaseCommand {

    /**
     * @var Config
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

    protected function init() {
        $em = EventManager::getInstance();
        $event = new Event("ws.bunit", "OnTestRun");
        $this->config =  new Config();
        $event->setParameter("config", $this->config);
        $em->send($event);

        if (!$this->config->getCaseFolder()) {
            $this->viewEmpty();
            return;
        }

        $this->config->getDBConnection()->useIt();
    }

    public function execute() {
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

        $onlyClass = $this->getParam("class");

        foreach(get_declared_classes() as $className ){
            if(!is_subclass_of($className, BaseCase::className()) ) {
                continue;
            }
            if ($onlyClass && strpos($className, $onlyClass) === false) {
                continue;
            }
            $refClass = new \ReflectionClass($className);
            if ($refClass->isAbstract()) {
                continue;
            }
            $this->caseInvokers[] = $this->createCaseInvoker($refClass, $this->getLabels());
        }
        $this->report = new TestReport();
    }

    private function runTests() {
        foreach ($this->caseInvokers as $invoker) {
            $invoker->invoke();
            $this->report->apply($invoker->getReport());
        }

        $writer = $this->getConsole()->getWriter();
        $writer->nextLine();
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
        if ($counter % $countInLine != 0) {
            $writer->nextLine();
        }
    }

    private function viewReport() {
        // view errors of exceptions or fails

        $writer = $this->getConsole()->getWriter();
        $writer->nextLine();

        if (!$this->report->isSuccess()) {
            $writer->setColor(Output::COLOR_RED)->printLine(sprintf("Test failed. %s of %s with error.", $this->report->errorCount(), $this->report->count()));
        } else if ($this->report->isSkip()) {
            $writer->setColor(Output::COLOR_YELLOW)->printLine(sprintf("Test was not absolutely right. %s of %s skipped.", $this->report->skippedCount(), $this->report->count()));
        } else {
            $writer->setColor(Output::COLOR_GREEN)->printLine(sprintf("Test success. Count tests: %s.", $this->report->count()));
        }
        $writer->nextLine();

        $this->printErrors();
    }

    private function printErrors() {
        if ($this->report->isSuccess()) {
            return;
        }
        $writer = $this->getConsole()->getWriter();

        $number = 1;
        foreach ($this->report->getResults() as $result) {
            $message = $result->getMessage();
            if (!$message) {
                continue;
            }
            $writer->setColor(Output::COLOR_RED)->printLine($number.") ".$result->getClass()."::".$result->getMethod());
            $writer->setColor(0)->printLine($message);

            if ($result->hasExpected()) {
                $writer->setColor(Output::COLOR_YELLOW)->printLine("Actual: ".(ValueDumper::value($result->getActual())->toString()));
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
     * @param array $labels
     * @return CaseInvoker
     */
    private function createCaseInvoker($refClass, array $labels) {
        $invoker = new CaseInvoker($refClass);
        $invoker->setLabels($labels);
        return $invoker;
    }

    /**
     * @return array
     */
    private function getLabels() {
        return (array) $this->getParam("label");
    }
}
