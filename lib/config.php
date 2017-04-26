<?php

namespace WS\BUnit;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class Config {

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var array
     */
    private $errors = array();

    /**
     * @var Config
     */
    private static $defaultObject;

    /**
     * @param $data
     */
    public function set($data) {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function test() {
        if (!is_dir($this->getWorkFolder())) {
            $this->errors[] = "Test folder `{$this->getWorkFolder()}` doesn`t exist";
        }
        $originalDbConfig = $this->getOriginalDbConfig();

        $mysqlConnection = new \mysqli(
            $originalDbConfig['host'],
            $originalDbConfig['user'],
            $originalDbConfig['password'],
            $originalDbConfig['db']
        );

        if ($mysqlConnection->connect_errno) {
            $this->errors[] = "Original connection. ".$mysqlConnection->connect_error;
        }

        $testDbConfig = $this->getTestDbConfig();
        if (!empty($testDbConfig)) {
            $mysqlConnection = new \mysqli(
                $testDbConfig['host'],
                $testDbConfig['user'],
                $testDbConfig['password'],
                $testDbConfig['db']
            );

            if ($mysqlConnection->connect_errno) {
                $this->errors[] = "Test connection." . $mysqlConnection->connect_error;
            }
        }

        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getTestErrors() {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getOriginalDbConfig() {
        return $this->data['db']['original'];
    }

    /**
     * @return array
     */
    public function getTestDbConfig() {
        return $this->data['db']['test'];
    }

    /**
     * @return array
     */
    public function getWorkFolder() {
        return $this->data['folder'];
    }

    /**
     * @return Config
     */
    public static function getDefaultConfig() {
        if (!static::hasDefaultConfig()) {
            static::$defaultObject = new static();
        }
        return static::$defaultObject;
    }

    /**
     * @return bool
     */
    public static function hasDefaultConfig() {
        return !!static::$defaultObject;
    }
}
