<?php

namespace WS\BUnit\Report;
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */


class TestReport {

    private $results = array();

    public function apply(TestReport $anotherReport) {
        foreach ($anotherReport->getResults() as $result) {
            $this->results[] = $result;
        }
    }

    public function addResult(TestReportResult $testReportResult) {
        $this->results[] = $testReportResult;
    }

    public function getCount() {
        return count($this->results);
    }

    private function getResults() {
        return $this->results;
    }
}
