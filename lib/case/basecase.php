<?php

namespace WS\BUnit\Cases;
use WS\BUnit\Artifacts\Assert;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
abstract class BaseCase {

    /**
     * @var Assert
     */
    private $assert;

    public static function className() {
        return get_called_class();
    }

    public function __construct(Assert $assert) {
        $this->assert = $assert;
    }

    public function setUp() {
    }

    public function tearDown() {
    }

    /**
     * @return Assert
     */
    public function getAssert() {
        return $this->assert;
    }
}
