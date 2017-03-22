<?php

namespace WS\BUnit\Artifacts;
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class Assert {
    public function equal($expects, $actual, $message = "") {
        if ($actual != $expects) {
            throw new AssertionException($message ? $message : "Expected value is not equal with actual.");
        }
    }

    public function notEqual($expects, $actual, $message = "") {
        if ($actual == $expects) {
            throw new AssertionException($message ? $message : "Expected value is equal with actual.");
        }
    }

    public function asTrue($actual, $message = "") {
        if ($actual !== true) {
            throw new AssertionException($message ? $message : "Value is not a true.");
        }
    }

    public function asFalse($actual, $message = "") {
        if ($actual !== false) {
            throw new AssertionException($message ? $message : "Value is not a false.");
        }
    }

    public function asEmpty($actual, $message = "") {
        if (!empty($actual)) {
            throw new AssertionException($message ? $message : "Value is not empty.");
        }
    }

    public function in(array $expected, $actual, $message = "") {
        if (!in_array($actual, $expected)) {
            throw new AssertionException($message ? $message : "Actual value is not in expected list.");
        }
    }

    public function fail($message) {
        throw new AssertionException($message);
    }
}
