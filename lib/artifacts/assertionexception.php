<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Artifacts;

class AssertionException extends \Exception {

    /**
     * @var mixed
     */
    private $actualValue;

    /**
     * @var mixed
     */
    private $expectedValue;

    /**
     * @var bool
     */
    private $hasMeasures = false;

    /**
     * @param $actual
     * @param $expected
     * @return $this
     */
    public function setMeasures($actual, $expected = null) {
        $this->hasMeasures = true;
        $this->actualValue = $actual;
        $this->expectedValue = $expected;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasMeasures() {
        return $this->hasMeasures;
    }

    /**
     * @return mixed
     */
    public function getExpectedValue() {
        return $this->expectedValue;
    }

    /**
     * @return mixed
     */
    public function getActualValue() {
        return $this->actualValue;
    }
}