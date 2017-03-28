<?php

namespace WS\BUnit\Invokers;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
abstract class BaseInvoker {

    abstract public function execute();

    /**
     * @param object $object
     * @param string $method
     * @param array|null $params
     * @return mixed
     */
    protected static function invokeMethod($object, $method, array $params = null) {
        $method = new \ReflectionMethod($object, $method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $params ? $params : array());
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed|null $value
     */
    protected static function setObjectPropertyValue($object, $property, $value = null) {
        $property = new \ReflectionProperty($object, $property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * @param object $object
     * @param string $property
     * @return mixed
     */
    protected static function getObjectPropertyValue($object, $property) {
        $property = new \ReflectionProperty($object, $property);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
