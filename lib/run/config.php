<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Run;

use WS\BUnit\DB\Connection;

class Config {

    /**
     * @var string
     */
    private $caseFolder;

    /**
     * @var Connection
     */
    private $db;

    /**
     * @param $path
     * @throws \Exception
     */
    public function setCaseFolder($path) {
        if (!is_dir($path)) {
            throw new \Exception("Path `$path` for test cases not exists");
        }
        $this->caseFolder = $path;
    }


    /**
     * @return string
     */
    public function getCaseFolder() {
        return $this->caseFolder;
    }

    /**
     * @param $host
     * @param $db
     * @param $dbUser
     * @param $dbUserPass
     */
    public function setTestDBParams($host, $db, $dbUser, $dbUserPass) {
        $this->db = new Connection($host, $dbUser, $dbUserPass, $db);
    }

    /**
     * @return Connection
     */
    public function getDBConnection() {
        return $this->db;
    }
}
