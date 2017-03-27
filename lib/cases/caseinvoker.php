<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Cases;

use WS\BUnit\Artifacts\Assert;
use WS\BUnit\Artifacts\AssertionException;
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

        $case = null;
        if (!$analyzer->isSkip()) {
            // init assertion, give invokers manager

            /** @var BaseCase $case */
            $case = $this->class->newInstance(new Assert());
        }
        // runs methods
        foreach ($analyzer->getTestMethods() as $method) {
            $result = new TestReportResult($this->class->getName(), $method->getName());
            $this->report->addResult($result);
            if ($analyzer->isSkip() || $method->isSkip()) {
                $result->setResult(TestReportResult::RESULT_SKIP);
                continue;
            }
            // collates reports for each method
            try {
                $this->class->getMethod($method->getName())->invoke($case);
                $result->setResult(TestReportResult::RESULT_SUCCESS);
            } catch (AssertionException $e) {
                $result->setResult(TestReportResult::RESULT_ERROR, $e->getMessage());
                if ($e->hasMeasures()) {
                    $result->setMeasures($e->getExpectedValue(), $e->getActualValue());
                }
            } catch (\Exception $e) {
                $result->setResult(TestReportResult::RESULT_ERROR, $e->getMessage());
            }
        }
        if ($case) {
            $case->tearDown();
        }
    }

    /**
     * @return TestReport
     */
    public function getReport() {
        return $this->report;
    }
}
