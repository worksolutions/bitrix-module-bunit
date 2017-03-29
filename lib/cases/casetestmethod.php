<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Cases;

class CaseTestMethod {

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isSkip;

    /**
     * @var array
     */
    private $labels;

    /**
     * @var string
     */
    private $expectedException;

    /**
     * @var string|null
     */
    private $dataProvider;

    /**
     * CaseTestMethod constructor.
     * @param $name
     * @param $isSkip
     * @param array $labels
     */
    public function __construct($name, $isSkip, array $labels = array()) {
        $this->name = $name;
        $this->isSkip = $isSkip;
        $this->labels = $labels;
    }

    /**
     * @param $class
     * @throws \Exception
     */
    public function setExpectedException($class) {
        if (!class_exists($class)) {
            throw new \Exception("Expected exception class `$class` doesn`t exists");
        }
        $this->expectedException = $class;
    }

    /**
     * @return bool
     */
    public function isSkip() {
        return $this->isSkip;
    }

    /**
     * @return array
     */
    public function getLabels() {
        return $this->labels;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getExpectedException() {
        return $this->expectedException;
    }

    /**
     * @return string|null
     */
    public function getDataProvider() {
        return $this->dataProvider;
    }

    /**
     * @param string $dataProvider
     */
    public function setDataProvider($dataProvider) {
        $this->dataProvider = $dataProvider;
    }
}
