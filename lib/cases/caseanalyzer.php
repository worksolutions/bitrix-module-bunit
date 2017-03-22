<?php

namespace WS\BUnit\Cases;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class CaseAnalyzer {

    /**
     * @var bool
     */
    private $skip = false;

    static $caseRules = array(
        'skip' => array(),
        'label' => array(),
        'name' => array()
    );

    /**
     * @var array
     */
    private $labels;

    /**
     * @var string
     */
    private $name;

    /**
     * @var CaseTestMethod[]
     */
    private $methods;

    public function __construct(\ReflectionClass $class) {
        $lineData = $this->parseDocBlock($class->getDocComment());

        foreach ($lineData as $line) {
            $name = $line[0];
            if ($name == 'skip') {
                $this->skip = true;
                continue;
            }
            if ($name == 'label') {
                $this->labels[] = $line[1];
            }
            if ($name == 'name') {
                $this->name = $name;
            }
        }

        foreach ($class->getMethods() as $method) {
            $methodCommentData = $this->parseDocBlock($method->getDocComment());
            if (static::getFromBlock("test", $methodCommentData) === null) {
                continue;
            }
            $methodSkip = static::getFromBlock("skip", $methodCommentData) !== null;
            $labels = static ::getMultipleFromBlock("label", $methodCommentData);
            $this->methods[] = new CaseTestMethod($method->getName(), $methodSkip, $labels);
        }
    }

    /**
     * @return CaseTestMethod[]
     */
    public function getTestMethods() {
        return $this->methods;
    }

    /**
     * @return bool
     */
    public function isSkip() {
        return $this->skip;
    }

    /**
     * @return array
     */
    public function getLabels() {
        return $this->labels;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $docComment
     * @return array
     */
    private static function parseDocBlock($docComment) {
        $lines = preg_split('/[\r\n]+/', $docComment, -1, PREG_SPLIT_NO_EMPTY);
        $lineData = array();
        while ($line = array_shift($lines)) {
            $lineWords = array_values(array_filter(explode(' ', $line)));
            if ($lineWords[0] == '*') {
                array_shift($lineWords);
            }
            if (substr($lineWords[0], 0, 1) !== '@') {
                continue;
            }
            $name = substr($lineWords[0], 1);
            $first = $lineWords[1];
            $second = $lineWords[2];
            $third = $lineWords[3];
            $lineData[] = array($name, $first, $second, $third);
        }
        return $lineData;
    }

    /**
     * @param $param
     * @param $data
     * @return array|null
     */
    private static function getFromBlock($param, $data) {
        foreach ($data as $line) {
            if ($line[0] == $param) {
                array_shift($line);
                return $line;
            }
        }
        return null;
    }

    /**
     * @param $param
     * @param $data
     * @return array
     */
    private static function getMultipleFromBlock($param, $data) {
        $res = array();
        foreach ($data as $line) {
            if ($line[0] != $param) {
                continue;
            }
            array_shift($line);
            if (count($line) == 1) {
                $line = $line[0];
            }
            $res[] = $line;
        }
        return $res;
    }
}
