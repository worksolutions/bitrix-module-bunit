<?php
namespace WS\BUnit\Command;

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use WS\BUnit\Cases\BaseCase;
use WS\BUnit\Cases\CaseInvoker;
use WS\BUnit\Console\Formatter\Output;
use WS\BUnit\Report\TestReport;
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
        $directoryIterator = new \RecursiveDirectoryIterator($dir);
        /** @var \SplFileInfo $file */
        foreach ($directoryIterator as $file) {
            if ($file->isDir()) {
                continue;
            }
            if ($file->getExtension() != 'php') {
                continue;
            }
            if (stripos($file->getBasename(), "TestCase") === false) {
                continue;
            }
            include $file->getPath();
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

        $report = new TestReport();
        foreach ($this->caseInvokers as $invoker) {
            $invoker->invoke();
            $report->apply($invoker->getReport());
        }
        // calculate run tests
        // analyze test options
    }

    private function runTests() {
        // create report
        // run tests showing (.|F|S)
    }

    private function viewReport() {
        // view errors of exceptions or fails
    }

    /**
     * @param $refClass
     * @return CaseInvoker
     */
    private function createCaseInvoker($refClass) {
        return new CaseInvoker($refClass);
    }
}
