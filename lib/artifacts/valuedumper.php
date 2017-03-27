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
        ob_start();
        var_dump($this->value);
        $res = ob_get_clean();
        return trim($res);
    }
}
