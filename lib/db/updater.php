<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\DB;

use WS\BUnit\Console\Writer;

class Updater {

    /**
     * @var \mysqli
     */
    private $connection;

    /**
     * @var Writer
     */
    private $echoWriter;

    /**
     * @var string
     */
    private $charset;

    /**
     * Updater constructor.
     * @param Connection $connection
     * @param string $charset
     * @throws \Exception
     */
    public function __construct(Connection $connection, $charset = 'cp1251') {
        $this->connection = new \mysqli(
            $connection->getHost(),
            $connection->getUser(),
            $connection->getPass(),
            $connection->getDb()
        );

        $this->charset = $charset;

        if ($this->connection->connect_errno == 1049) {
            $this->connection = new \mysqli(
                $connection->getHost(),
                $connection->getUser(),
                $connection->getPass()
            );

            $createSql = "CREATE DATABASE IF NOT EXISTS {$connection->getDb()} CHARACTER SET {$this->charset}";
            $this->connection->query($createSql);
            $this->writeEcho($createSql);

            $this->connection = new \mysqli(
                $connection->getHost(),
                $connection->getUser(),
                $connection->getPass(),
                $connection->getDb()
            );
        }

        if ($this->connection->connect_errno) {
            throw new \Exception($this->connection->connect_error);
        } elseif (!$this->connection->set_charset($charset)) {
            throw new \Exception($this->connection->error);
        }
    }

    /**
     * @param $filePath
     * @throws \Exception
     */
    public function update($filePath) {
        if (!file_exists($filePath)) {
            throw new \Exception("Sql file `{$filePath}` is not exist");
        }

        $file = fopen($filePath, 'r');

        while (!feof($file)) {
            $sqlQuery = "";
            while(!feof($file) && substr($sqlQuery, (strlen($sqlQuery) - 2), 1) != ";") {
                $sqlQuery .= fgets($file);
            }
            $this->connection->query($sqlQuery);
            $this->writeEcho($sqlQuery);
        }
        fclose($file);
    }

    /**
     * @param Writer $writer
     */
    public function setEchoWriter(Writer $writer) {
        $this->echoWriter = $writer;
    }

    /**
     * @param string $str
     */
    private function writeEcho($str) {
        if (!$this->echoWriter) {
            return;
        }
        $this->echoWriter->printChars($str);
    }
}
