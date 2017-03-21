<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Cases;

use WS\BUnit\Report\TestReport;
use WS\BUnit\Report\TestReportResult;

class CaseInvoker {

    /**
     * @var \ReflectionClass
     */
    private $class;

    /**
     * @var TestReport
     */
    private $report;

    function __construct(\ReflectionClass $class) {
        $this->class = $class;
        $this->report = new TestReport();
    }

    public function invoke() {
        $analyzer = new CaseAnalyzer($this->class);

        foreach ($analyzer->getTestMethods() as $method) {
            $result = new TestReportResult($this->class->getName(), $method->getName());
            $this->report->addResult($result);
            if ($analyzer->isSkip() || $method->isSkip()) {
                $result->setResult(TestReportResult::RESULT_SKIP);
                continue;
            }
            
            
        }
        // analyzes class
        // init assertion, give invokers manager
        // runs methods
        // collates reports for each method
    }

    /**
     * @return TestReport
     */
    public function getReport() {
        return $this->report;
    }
}
