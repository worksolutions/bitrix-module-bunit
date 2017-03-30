<?php

namespace WS\BUnit\DB;

use WS\BUnit\Console\Writer;

class Dumper {

    const MAX_SQL_SIZE = 1e6;

    const NONE = 0;
    const DROP = 1;
    const CREATE = 2;
    const DATA = 4;
    const TRIGGERS = 8;
    const ALL = 15; // DROP | CREATE | DATA | TRIGGERS

    /** @var array */
    public $tables = array(
        '*' => self::ALL
    );

    /** @var \mysqli */
    private $connection;

    /**
     * @var Writer
     */
    private $echoWriter;

    /**
     * Connects to database.
     * @param Connection $connection
     * @param string $charset
     * @throws \Exception
     * @internal param connection $mysqli
     */
    public function __construct(Connection $connection, $charset = 'cp1251') {
        $this->connection = new \mysqli(
            $connection->getHost(),
            $connection->getUser(),
            $connection->getPass(),
            $connection->getDb()
        );

        if ($this->connection->connect_errno) {
            throw new \Exception($this->connection->connect_error);
        } elseif (!$this->connection->set_charset($charset)) {
            throw new \Exception($this->connection->error);
        }
    }

    /**
     * @return string
     */
    public function getFilePath() {
        $tmpDir = sys_get_temp_dir();
        return $tmpDir.'/'.str_replace("\\", "_", get_class($this)).".sql";
    }

    /**
     * Saves dump to the file.
     * @return void
     * @throws \Exception
     */
    public function create() {
        $file = $this->getFilePath();
        $handle = strcasecmp(substr($file, -3), '.gz') ? fopen($file, 'wb') : gzopen($file, 'wb');
        if (!$handle) {
            throw new \Exception("ERROR: Cannot write file '$file'.");
        }
        $this->write($handle);
    }

    /**
     * Writes dump to logical file.
     * @param  resource|null $handle
     * @return void
     * @throws \Exception
     */
    public function write($handle = NULL) {
        if ($handle === NULL) {
            $handle = fopen('php://output', 'wb');
        } elseif (!is_resource($handle) || get_resource_type($handle) !== 'stream') {
            throw new \Exception('Argument must be stream resource.');
        }

        $tables = $views = array();

        $res = $this->connection->query('SHOW FULL TABLES');
        while ($row = $res->fetch_row()) {
            if ($row[1] === 'VIEW') {
                $views[] = $row[0];
            } else {
                $tables[] = $row[0];
            }
        }
        $res->close();

        $tables = array_merge($tables, $views); // views must be last

        $this->connection->query('LOCK TABLES `' . implode('` READ, `', $tables) . '` READ');

        $db = $this->connection->query('SELECT DATABASE()')->fetch_row();
        $sql = "-- Created at " . date('j.n.Y G:i') . " BUnit Dump Utility\n"
            . (isset($_SERVER['HTTP_HOST']) ? "-- Host: $_SERVER[HTTP_HOST]\n" : '')
            . "-- MySQL Server: " . $this->connection->server_info . "\n"
            . "-- Database: " . $db[0] . "\n"
            . "\n"
            . "SET NAMES utf8;\n"
            . "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n"
            . "SET FOREIGN_KEY_CHECKS=0;\n";
        fwrite($handle, $sql);
        $this->writeEcho($sql);

        foreach ($tables as $table) {
            $this->dumpTable($handle, $table);
        }

        $sql = "-- THE END\n";
        fwrite($handle, $sql);
        $this->writeEcho($sql);

        $this->connection->query('UNLOCK TABLES');
    }

    /**
     * Dumps table to logical file.
     * @param $handle
     * @param $table
     * @return void
     * @internal param $resource
     */
    public function dumpTable($handle, $table) {
        $delTable = $this->delimite($table);

        $res = $this->connection->query("SHOW CREATE TABLE $delTable");

        $row = $res->fetch_assoc();
        $res->close();

        $sql = "-- --------------------------------------------------------\n\n";
        fwrite($handle, $sql);
        $this->writeEcho($sql);

        $mode = isset($this->tables[$table]) ? $this->tables[$table] : $this->tables['*'];
        $view = isset($row['Create View']);

        if ($mode & self::DROP) {
            $sql = 'DROP ' . ($view ? 'VIEW' : 'TABLE') . " IF EXISTS $delTable;\n\n";
            fwrite($handle, $sql);
            $this->writeEcho($sql);
        }

        if ($mode & self::CREATE) {
            $sql = $row[$view ? 'Create View' : 'Create Table'] . ";\n\n";
            fwrite($handle, $sql);
            $this->writeEcho($sql);
        }

        if (!$view && ($mode & self::DATA)) {
            $numeric = array();
            $res = $this->connection->query("SHOW COLUMNS FROM $delTable");
            $cols = array();
            while ($row = $res->fetch_assoc()) {
                $col = $row['Field'];
                $cols[] = $this->delimite($col);
                $numeric[$col] = (bool) preg_match('#^[^(]*(BYTE|COUNTER|SERIAL|INT|LONG$|CURRENCY|REAL|MONEY|FLOAT|DOUBLE|DECIMAL|NUMERIC|NUMBER)#i', $row['Type']);
            }
            $cols = '(' . implode(', ', $cols) . ')';
            $res->close();


            $size = 0;
            $res = $this->connection->query("SELECT * FROM $delTable", MYSQLI_USE_RESULT);
            while ($row = $res->fetch_assoc()) {
                $s = '(';
                foreach ($row as $key => $value) {
                    if ($value === NULL) {
                        $s .= "NULL,\t";
                    } elseif ($numeric[$key]) {
                        $s .= $value . ",\t";
                    } else {
                        $s .= "'" . $this->connection->real_escape_string($value) . "',\t";
                    }
                }

                if ($size == 0) {
                    $s = "INSERT INTO $delTable $cols VALUES\n$s";
                } else {
                    $s = ",\n$s";
                }

                $len = strlen($s) - 1;
                $s[$len - 1] = ')';
                fwrite($handle, $s, $len);
                $this->writeEcho($s);

                $size += $len;
                if ($size > self::MAX_SQL_SIZE) {
                    $str = ";\n";
                    fwrite($handle, $str);
                    $this->writeEcho($str);
                    $size = 0;
                }
            }

            $res->close();
            if ($size) {
                fwrite($handle, ";\n");
                $this->writeEcho(";\n");
            }
            fwrite($handle, "\n");
            $this->writeEcho("\n");
        }

        if ($mode & self::TRIGGERS) {
            $res = $this->connection->query("SHOW TRIGGERS LIKE '" . $this->connection->real_escape_string($table) . "'");
            if ($res->num_rows) {
                $delimiter = "DELIMITER ;;\n\n";
                fwrite($handle, $delimiter);
                $this->writeEcho($delimiter);
                while ($row = $res->fetch_assoc()) {
                    $sql = "CREATE TRIGGER {$this->delimite($row['Trigger'])} $row[Timing] $row[Event] ON $delTable FOR EACH ROW\n$row[Statement];;\n\n";
                    fwrite($handle, $sql);
                    $this->writeEcho($sql);

                }
                $delimiter = "DELIMITER ;\n\n";
                fwrite($handle, $delimiter);
            }
            $res->close();
        }

        fwrite($handle, "\n");
        $this->writeEcho("\n");
    }

    private function delimite($s) {
        return '`' . str_replace('`', '``', $s) . '`';
    }

    public function setEchoWriter(Writer $writer) {
        $this->echoWriter = $writer;
    }

    private function writeEcho($sql) {
        if (!$this->echoWriter) {
            return;
        }
        $this->echoWriter->printChars($sql);
    }
}
