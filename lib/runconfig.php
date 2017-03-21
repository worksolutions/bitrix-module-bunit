<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit;

class RunConfig {
    private $caseFolder;

    public function setCaseFolder($path) {
        if (!is_dir($path)) {
            throw new \Exception("Path `$path` for test cases not exists");
        }
        $this->caseFolder = $path;
    }

    public function getCaseFolder() {
        return $this->caseFolder;
    }
}