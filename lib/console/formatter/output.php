<?php

namespace WS\BUnit\Console\Formatter;

class Output {

    const COLOR_BLACK = 30;
    const COLOR_RED = 31;
    const COLOR_GREEN = 32;
    const COLOR_YELLOW = 33;
    const COLOR_BLUE = 34;
    const COLOR_MAGENTA = 35;
    const COLOR_CYAN = 36;
    const COLOR_WHITE = 37;
    const COLOR_DEFAULT = 0;

    private $color;

    /**
     * Output constructor.
     * @param int $color Output::COLOR_BLACK
     */
    public function __construct($color = 0) {
        $this->color = $color;
    }

    public function colorize($text) {
        return chr(27) . "[{$this->color}m" . $text . chr(27) . "[0m";
    }

    /**
     * @param int $color
     * @return $this
     */
    public function setColor($color = 0) {
        $this->color = $color;
        return $this;
    }
}
