<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Console;

use WS\BUnit\Console\Formatter\Output;

class Writer {

    /**
     * @var Output
     */
    private $output;

    /**
     * @var resource
     */
    private $out;

    public function __construct(Output $output, $out) {
        $this->output = $output;
        $this->out = $out;
    }

    /**
     * @param $color "Example Output::COLOR_BLACK
     * @return $this
     */
    public function setColor($color) {
        $this->output->setColor($color);
        return $this;
    }

    /**
     * @param $str
     * @param $type "Example Output::COLOR_BLACK
     * @return $this
     */
    public function printChars($str) {
        global $APPLICATION;
        $str = $APPLICATION->ConvertCharset($str, LANG_CHARSET, "UTF-8");
        $str = $this->output->colorize($str);
        $this->toStream($str);
        return $this;
    }

    public function nextLine() {
        $this->toStream("\n");
    }

    public function toStream($str) {
        fwrite($this->out, $str);
    }

    /**
     * @param $str
     * @return $this
     */
    public function printLine($str) {
        $this->printChars($str);
        $this->toStream("\n");
        return $this;
    }
}
