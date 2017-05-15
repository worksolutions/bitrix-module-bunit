<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Artifacts;

class ValueDumper {
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param $value
     * @return static
     */
    static public function value($value) {
        $ob = new static();
        $ob->value = $value;
        return $ob;
    }

    /**
     * @return string
     */
    public function toString() {
        return var_export($this->value, true);
    }
}
