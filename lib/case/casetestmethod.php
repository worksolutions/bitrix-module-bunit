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

    public function __construct($name, $isSkip, array $labels = array()) {
        $this->name = $name;
        $this->isSkip = $isSkip;
        $this->labels = $labels;
    }

    public function isSkip() {
        return true;
    }

    public function getLabels() {
        return $this->labels;
    }

    public function getName() {
        return $this->name;
    }
}
