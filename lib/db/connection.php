<?php

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\DB;

use Bitrix\Main\Application;

class Connection {

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $db;

    /**
     * Connection constructor.
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $db
     */
    public function __construct($host, $user, $pass, $db) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->db = $db;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPass() {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getDb() {
        return $this->db;
    }

    public function useIt() {
        $connection = Application::getInstance()->getConnection();
        if ($connection->getDbName() == $this->db) {
            return;
        }

        $connection->disconnect();

        $dbHostProperty = new \ReflectionProperty($connection, "dbHost");
        $dbHostProperty->setAccessible(true);
        $dbHostProperty->setValue($this->getHost());

        $dbNameProperty = new \ReflectionProperty($connection, "dbName");
        $dbNameProperty->setAccessible(true);
        $dbNameProperty->setValue($this->getDb());

        $dbLoginProperty = new \ReflectionProperty($connection, "dbLogin");
        $dbLoginProperty->setAccessible(true);
        $dbLoginProperty->setValue($this->getUser());

        $dbPasswordProperty = new \ReflectionProperty($connection, "dbPassword");
        $dbPasswordProperty->setAccessible(true);
        $dbPasswordProperty->setValue($this->getPass());

        $connection->connect();

        /**
         * @var \CDatabase
         */
        global $DB;
        $DB->Disconnect();
        $DB->Connect(
            $this->getHost(),
            $this->getDb(),
            $this->getUser(),
            $this->getPass()
        );
    }
}
