<?php

namespace WS\BUnit\Report;
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */


class TestReport {

    private $results = array();

    /**
     * @var bool
     */
    private $isSuccess = true;

    /**
     * @var bool
     */
    private $isSkip =false;

    /**
     * @var int
     */
    private $errorCount = 0;

    /**
     * @var int
     */
    private $skippedCount;

    public function apply(TestReport $anotherReport) {
        foreach ($anotherReport->getResults() as $result) {
            $this->addResult($result);
        }
    }

    public function addResult(TestReportResult $testReportResult) {
        $this->results[] = $testReportResult;
        if ($testReportResult->getResult() == TestReportResult::RESULT_ERROR) {
            $this->isSuccess = false;
            $this->errorCount++;
        }

        if ($testReportResult->getResult() == TestReportResult::RESULT_SKIP) {
            $this->isSkip = true;
            $this->skippedCount++;
        }
    }

    public function getCount() {
        return count($this->results);
    }

    /**
     * @return TestReportResult[]
     */
    public function getResults() {
        return $this->results;
    }

    /**
     * @return bool
     */
    public function isSuccess() {
        return $this->isSuccess;
    }

    /**
     * @return bool
     */
    public function isSkip() {
        return $this->isSkip;
    }

    public function count() {
        return count($this->results);
    }

    public function errorCount() {
        return $this->errorCount;
    }

    public function skippedCount() {
        return $this->skippedCount;
    }

}
