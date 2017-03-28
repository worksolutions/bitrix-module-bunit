<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Run;

class Config {

    /**
     * @var string
     */
    private $caseFolder;

    /**
     * @var array
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
        $this->db = func_get_args();
    }
}