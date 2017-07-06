<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Cases;

use WS\BUnit\Artifacts\Assert;
use WS\BUnit\Artifacts\AssertionException;
use WS\BUnit\Interfaces\TestResultPrinter;
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

    /**
     * @var array|null
     */
    private $onlyLabels;

    /**
     * CaseInvoker constructor.
     * @param \ReflectionClass $class
     */
    function __construct(\ReflectionClass $class) {
        $this->class = $class;
        $this->report = new TestReport();
    }

    /**
     * @param array $labels
     */
    public function setLabels(array $labels) {
        $this->onlyLabels = $labels;
    }

    /**
     * @param array $labels
     * @return bool
     */
    private function allowedByLabels(array $labels) {
        if (!$this->onlyLabels) {
            return true;
        }
        return !!array_intersect($this->onlyLabels, $labels);
    }

    public function invoke(TestResultPrinter $printer) {
        $analyzer = new CaseAnalyzer($this->class);

        $case = null;
        if (!$analyzer->isSkip()) {
            /** @var BaseCase $case */
            $case = $this->class->newInstance(new Assert());
        }

        $ignoreLabels = !$this->onlyLabels;
        if (!$ignoreLabels) {
            $ignoreLabels = $this->allowedByLabels($analyzer->getLabels());
        }

        // runs methods
        foreach ($analyzer->getTestMethods() as $method) {
            if (!$ignoreLabels && !$this->allowedByLabels($method->getLabels())) {
                continue;
            }
            $result = new TestReportResult($this->class->getName(), $method->getName());
            $this->report->addResult($result);
            if ($analyzer->isSkip() || $method->isSkip()) {
                $result->setResult(TestReportResult::RESULT_SKIP);
                $printer->printTestResult($result->getResult());
                continue;
            }

            $this->runTest($case, $method, $result);
            $printer->printTestResult($result->getResult());
        }
    }

    private function runTest(BaseCase $case, CaseTestMethod $method, TestReportResult $result) {
        $listArgs = array(array());

        if ($methodDataProvider = $method->getDataProvider()) {
            $listArgs = $this->class->getMethod($methodDataProvider)->invoke($case);
        }
        foreach ($listArgs as $args) {
            try {
                $case->setUp();
                $this->class->getMethod($method->getName())->invokeArgs($case, (array) $args);
                if (!$method->getExpectedException()) {
                    $result->setResult(TestReportResult::RESULT_SUCCESS);
                } else {
                    $result->setResult(TestReportResult::RESULT_ERROR, "Expected exception `{$method->getExpectedException()}`");
                }
                $case->tearDown();
            } catch (AssertionException $e) {
                $result->setResult(TestReportResult::RESULT_ERROR, $e->getMessage());
                $case->tearDown();
                if ($e->hasMeasures()) {
                    $result->setMeasures($e->getExpectedValue(), $e->getActualValue());
                }
            } catch (\Exception $e) {
                $case->tearDown();
                $isExpected = ($expectedException = $method->getExpectedException())
                    &&
                    (get_class($e) == $expectedException || is_subclass_of($e, $expectedException));

                if ($isExpected) {
                    $result->setResult(TestReportResult::RESULT_SUCCESS);
                } else {
                    $result->setResult(TestReportResult::RESULT_ERROR, $e->getMessage());
                }
            }
        }
    }

    /**
     * @return TestReport
     */
    public function getReport() {
        return $this->report;
    }
}
