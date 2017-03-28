<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Invokers;

class ResultModifierInvoker extends BaseInvoker {

    /**
     * @var \CBitrixComponent
     */
    private $component;

    /**
     * @var array
     */
    private $initialArResult;

    /**
     * @var array
     */
    private $totalArResult;

    /**
     * ResultModifierInvoker constructor.
     * @param string $component
     * @param string $template
     */
    public function __construct($component, $template = "") {
        $this->component = new \CBitrixComponent();
        $this->component->initComponent($component, $template);
        $this->component->initComponentTemplate();
    }

    public function setArResult(array $value) {
        $this->initialArResult = $value;
    }

    public function execute() {
        /** @var \CBitrixComponentTemplate $template */
        $template = $this->component->getTemplate();
        if ($template === null) {
            throw new \Exception("Component template has not found.");
        }
        if (!$template->GetFolder() || is_dir($template->GetFolder())) {
            throw new \Exception("Template folder has not found.");
        }

        global $DOCUMENT_ROOT;
        $file = $DOCUMENT_ROOT . $template->GetFolder() . "/result_modifier.php";
        if (!file_exists($file)) {
            throw new \Exception("result_modifier.php file has not found in folder {$template->GetFolder()}");
        }
        $arResult = $this->initialArResult;
        $func = function () use (& $arResult) {
            include func_get_arg(0);
        };
        $func($file);
        $this->totalArResult = $arResult;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getArResult() {
        if ($this->totalArResult === null) {
            throw new \Exception("Execute has not been");
        }
        return $this->totalArResult;
    }

    /**
     * @param $paramName
     * @return mixed
     * @throws \Exception
     */
    public function getArResultValue($paramName) {
        if ($this->totalArResult === null) {
            throw new \Exception("Execute has not been");
        }
        return $this->totalArResult[$paramName];
    }
}
