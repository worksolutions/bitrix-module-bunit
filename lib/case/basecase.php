<?php

namespace WS\BUnit\Cases;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class BaseCase {

    public static function className() {
        return get_called_class();
    }
}
