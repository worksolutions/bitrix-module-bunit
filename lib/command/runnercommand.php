<?php
namespace WS\BUnit\Command;

use WS\BUnit\Artifacts\ValueDumper;
use WS\BUnit\Cases\BaseCase;
use WS\BUnit\Cases\CaseInvoker;
use WS\BUnit\Console\Formatter\Output;
use WS\BUnit\DB\Connection;
use WS\BUnit\Module;
use WS\BUnit\Report\ProgressPrinter;
use WS\BUnit\Report\TestReport;
use WS\BUnit\Report\TestReportResult;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class RunnerCommand extends BaseCommand {

    /**
     * @var CaseInvoker[]
     */
    private $caseInvokers;

    /**
     * @var TestReport
     */
    private $report;

    protected function init() {
        $connectionParams = $this->getConfig()->getTestDbConfig();
        if (!$connectionParams) {
            $connectionParams = $this->getConfig()->getOriginalDbConfig();
        }
        $connection = new Connection(
            $connectionParams['host'],
            $connectionParams['user'],
            $connectionParams['password'],
            $connectionParams['db']
        );
        $connection->useIt();
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

    /**
     * @throws \Exception
     */
    private function initTests() {
        if ($this->getConfig()->hasBootstrap()) {
            $bootstrap = $this->getConfig()->getBootstrap();
            Module::safeInclude($bootstrap);
        }
        // include tests files
        $dir = $this->getConfig()->getWorkFolder();
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

        if (count($this->caseInvokers) == 0) {
            $this->viewEmpty();
            throw new \Exception("Count of tests is zero!");
        }
        $this->report = new TestReport();
    }

    private function runTests() {
        $writer = $this->getConsole()->getWriter();
        $progressPrinter = new ProgressPrinter($writer);
        $progressPrinter->startProgress();
        foreach ($this->caseInvokers as $invoker) {
            $invoker->invoke($progressPrinter);
            $this->report->apply($invoker->getReport());
        }
        $progressPrinter->finishProgress();
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
