<?php

namespace WS\BUnit\Artifacts;
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class Assert {

    /**
     * @param $message
     * @return AssertionException
     */
    private function exception($message) {
        return new AssertionException($message);
    }

    /**
     * @param $actual
     * @param $expects
     * @param string $message
     * @throws AssertionException
     */
    public function equal($actual, $expects,  $message = "") {
        if ($actual != $expects) {
            throw $this->exception($message ? $message : "Expected value is not equal with actual.")
                ->setMeasures($actual, $expects);
        }
    }

    /**
     * @param $actual
     * @param $expects
     * @param string $message
     * @throws AssertionException
     */
    public function same($actual, $expects,  $message = "") {
        if ($actual !== $expects) {
            throw $this->exception($message ? $message : "Expected value is not equal with actual.")
                ->setMeasures($actual, $expects);
        }
    }

    /**
     * @param $actual
     * @param $expects
     * @param string $message
     * @throws AssertionException
     */
    public function notEqual($actual, $expects, $message = "") {
        if ($actual == $expects) {
            throw $this->exception($message ? $message : "Expected value is equal with actual.")
                ->setMeasures($actual, $expects);
        }
    }

    /**
     * @param $actual
     * @param string $message
     * @throws AssertionException
     */
    public function asTrue($actual, $message = "") {
        if ($actual !== true) {
            throw $this->exception($message ? $message : "Value is not a true.")
                ->setMeasures($actual);
        }
    }

    /**
     * @param $actual
     * @param string $message
     * @throws AssertionException
     */
    public function asFalse($actual, $message = "") {
        if ($actual !== false) {
            throw $this->exception($message ? $message : "Value is not a false.")
                ->setMeasures($actual);
        }
    }

    /**
     * @param $actual
     * @param string $message
     * @throws AssertionException
     */
    public function asEmpty($actual, $message = "") {
        if (!empty($actual)) {
            throw $this->exception($message ? $message : "Value is not empty.")
                ->setMeasures($actual);
        }
    }

    /**
     * @param $actual
     * @param array $expected
     * @param string $message
     * @throws AssertionException
     */
    public function in($actual, array $expected, $message = "") {
        if (!in_array($actual, $expected)) {
            throw $this->exception($message ? $message : "Actual value is not in expected list.")
                ->setMeasures($actual);
        }
    }

    /**
     * @param $message
     * @throws AssertionException
     */
    public function fail($message) {
        throw $this->exception($message);
    }
}
