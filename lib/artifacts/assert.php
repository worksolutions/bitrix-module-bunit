<?php

namespace WS\BUnit\Artifacts;
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class Assert {

    /**
     * @param $errorMessage
     * @return AssertionException
     */
    private function exception($errorMessage) {
        return new AssertionException($errorMessage);
    }

    /**
     * @param $actual
     * @param $expects
     * @param string $errorMessage
     * @throws AssertionException
     */
    public function equal($actual, $expects,  $errorMessage = "") {
        if ($actual != $expects) {
            throw $this->exception($errorMessage ? $errorMessage : "Expected value is not equal with actual.")
                ->setMeasures($actual, $expects);
        }
    }

    /**
     * @param $actual
     * @param $expects
     * @param string $errorMessage
     * @throws AssertionException
     */
    public function same($actual, $expects,  $errorMessage = "") {
        if ($actual !== $expects) {
            throw $this->exception($errorMessage ? $errorMessage : "Expected value is not equal with actual.")
                ->setMeasures($actual, $expects);
        }
    }

    /**
     * @param $actual
     * @param $expects
     * @param string $errorMessage
     * @throws AssertionException
     */
    public function notEqual($actual, $expects, $errorMessage = "") {
        if ($actual == $expects) {
            throw $this->exception($errorMessage ? $errorMessage : "Expected value is equal with actual.")
                ->setMeasures($actual, $expects);
        }
    }

    /**
     * @param $actual
     * @param string $errorMessage
     * @throws AssertionException
     */
    public function asTrue($actual, $errorMessage = "") {
        if ($actual !== true) {
            throw $this->exception($errorMessage ? $errorMessage : "Value is not a true.")
                ->setMeasures($actual);
        }
    }

    /**
     * @param $actual
     * @param string $errorMessage
     * @throws AssertionException
     */
    public function asFalse($actual, $errorMessage = "") {
        if ($actual !== false) {
            throw $this->exception($errorMessage ? $errorMessage : "Value is not a false.")
                ->setMeasures($actual);
        }
    }

    /**
     * @param $actual
     * @param string $errorMessage
     * @throws AssertionException
     */
    public function asEmpty($actual, $errorMessage = "") {
        if (!empty($actual)) {
            throw $this->exception($errorMessage ? $errorMessage : "Value is not empty.")
                ->setMeasures($actual);
        }
    }

    /**
     * @param $actual
     * @param array $expected
     * @param string $errorMessage
     * @throws AssertionException
     */
    public function in($actual, array $expected, $errorMessage = "") {
        if (!in_array($actual, $expected)) {
            throw $this->exception($errorMessage ? $errorMessage : "Actual value is not in expected list.")
                ->setMeasures($actual);
        }
    }

    /**
     * @param $errorMessage
     * @throws AssertionException
     */
    public function fail($errorMessage) {
        throw $this->exception($errorMessage);
    }
}
