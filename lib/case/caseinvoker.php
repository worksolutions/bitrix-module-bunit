<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Cases;

use WS\BUnit\Report\TestReport;

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
    }

    public function invoke() {
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
