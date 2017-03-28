<?php

namespace WS\BUnit\Invokers;

use WS\BUnit\Module;

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class ComponentInvoker extends BaseInvoker {

    /**
     * @var
     */
    private $name;

    /**
     * @var array
     */
    private $params = array();

    /**
     * @var string
     */
    private $path;

    /**
     * @var \CBitrixComponent
     */
    private $bitrixComponent;

    /**
     * @var mixed
     */
    private $executeResult;

    /**
     * @var \CBitrixComponent
     */
    private $runComponent;

    /**
     * ComponentInvoker constructor.
     * @param $name
     */
    public function __construct($name) {
        $this->name = $name;
        $this->path = Module::getBitrixPath();

        $this->bitrixComponent = new \CBitrixComponent($name);
        $this->bitrixComponent->initComponent($name);
    }

    /**
     * @param array $arParams
     */
    public function setParams(array $arParams) {
        $this->params = $arParams;
    }

    public function execute() {
        $classOfComponent = static::getObjectPropertyValue($this->bitrixComponent, "classOfComponent");

        if($classOfComponent) {
            /** @var \CBitrixComponent $component  */
            $component = new $classOfComponent($this);
            $component->arParams = $component->onPrepareComponentParams($this->params);

            static::invokeMethod($component, "__prepareComponentParams", $component->arParams);
            $component->onIncludeComponentLang();
            // execute
            $this->executeResult = $component->executeComponent();
            $this->runComponent = $component;
        } else {
            static::invokeMethod($this->bitrixComponent, "__prepareComponentParams", $this->params);
            $this->bitrixComponent->arParams = $this->params;
            $this->bitrixComponent->includeComponentLang();
            // execute
            $this->executeResult = static::invokeMethod($this->bitrixComponent, "__IncludeComponent", null);
            $this->runComponent = $this->bitrixComponent;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getResultValue($name) {
        $this->throwIfWasntExecute();
        return $this->runComponent->arResult[$name];
    }

    /**
     * @return array
     */
    public function getArResult() {
        $this->throwIfWasntExecute();
        return $this->runComponent->arResult;
    }

    /**
     * @return mixed
     */
    public function getExecuteResult() {
        return $this->executeResult;
    }

    /**
     * @throws \Exception
     */
    private function throwIfWasntExecute() {
        if ($this->runComponent !== null) {
            return;
        }
        throw new \Exception("Execute of invoker has not been yet.");
    }
}
