<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Report;

use WS\BUnit\Console\Writer;
use WS\BUnit\Interfaces\TestResultPrinter;

class ProgressPrinter implements TestResultPrinter {

    /**
     * @var Writer
     */
    private $writer;
    /**
     * @var int
     */
    private $countInLine;

    /**
     * @var int
     */
    private $counter = 0;

    public function __construct(Writer $writer, $countInLine = 25) {
        $this->writer = $writer;
        $this->countInLine = $countInLine;
    }

    /**
     * @return void
     */
    public function startProgress() {
        $this->writer->nextLine();
    }

    /**
     * @param string $type value of TestReportResult::RESULT_..
     */
    public function printTestResult($type) {
        switch ($type) {
            case TestReportResult::RESULT_SKIP:
                $this->writer->printChars("S");
                break;
            case TestReportResult::RESULT_ERROR:
                $this->writer->printChars("F");
                break;
            case TestReportResult::RESULT_SUCCESS:
                $this->writer->printChars(".");
                break;
            default:
                break;
        }
        $this->counter++;
        if ($this->counter % $this->countInLine == 0) {
            $this->writer->nextLine();
        }
    }

    /**
     * @return void
     */
    public function finishProgress() {
        $this->writer->nextLine();
    }
}