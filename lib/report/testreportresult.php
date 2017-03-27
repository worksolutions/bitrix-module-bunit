<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Report;

class TestReportResult {

    const RESULT_SUCCESS = 0;
    const RESULT_SKIP = 1;
    const RESULT_ERROR = 2;

    /**
     * @var int
     */
    private $result;

    /**
     * @var string
     */
    private $message;

    /**
     * @var String
     */
    private $class;

    /**
     * @var String
     */
    private $method;

    /**
     * @var mixed
     */
    private $actual;

    /**
     * @var mixed
     */
    private $expected;

    /**
     * @var bool
     */
    private $hasMeasures = false;

    /**
     * TestReportResult constructor.
     * @param string $class
     * @param string $method
     */
    public function __construct($class, $method) {
        $this->class = $class;
        $this->method = $method;
    }

    /**
     * @param int $result One of next RESULT_SUCCESS | RESULT_SKIP | RESULT_ERROR
     * @param string $message
     * @throws \Exception
     */
    public function setResult($result, $message = "") {
        if ((int) $result > 2) {
            throw new \Exception("The result is not correct.");
        }
        $this->result = $result;
        $this->message = $message;
    }
    
    public function setMeasures($expected, $actual) {
        $this->hasMeasures = true;
        $this->expected = $expected;
        $this->actual = $actual;
    }

    /**
     * @return int
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @return String
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @return String
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function hasExpected() {
        return $this->hasMeasures;
    }

    /**
     * @return mixed
     */
    public function getExpected() {
        return $this->expected;
    }

    /**
     * @return mixed
     */
    public function getActual() {
        return $this->actual;
    }
}
