<?php

namespace WS\BUnit\DB;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class Config {

    /**
     * @var string
     */
    private $baseDB;

    /**
     * @var string
     */
    private $baseHost;

    /**
     * @var string
     */
    private $baseUser;

    /**
     * @var string
     */
    private $basePass;

    /**
     * @var string
     */
    private $testHost;

    /**
     * @var string
     */
    private $testUser;

    /**
     * @var string
     */
    private $testPass;

    /**
     * @var string
     */
    private $testDB;

    /**
     * @param $host
     * @param $user
     * @param $pass
     * @param $db
     */
    public function setBaseConnection($host, $user, $pass, $db) {
        $this->baseHost = $host;
        $this->baseUser = $user;
        $this->basePass = $pass;
        $this->baseDB = $db;
    }

    /**
     * @param $host
     * @param $user
     * @param $pass
     * @param $db
     */
    public function setTestConnection($host, $user, $pass, $db) {
        $this->testHost = $host;
        $this->testUser = $user;
        $this->testPass = $pass;
        $this->testDB = $db;
    }

    /**
     * @return Connection
     */
    public function getBaseConnection() {
        return new Connection($this->baseHost, $this->baseUser, $this->basePass, $this->baseDB);
    }

    /**
     * @return Connection
     */
    public function getTestConnection() {
        return new Connection($this->testHost, $this->testUser, $this->testPass, $this->testDB);
    }
}