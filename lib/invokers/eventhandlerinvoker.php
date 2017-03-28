<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\BUnit\Invokers;

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;

class EventInvoker extends BaseInvoker {

    /**
     * @var Event
     */
    private $event;

    public function __construct($module, $eventName) {
        $this->event = new Event($module, $eventName);
    }

    /**
     * @param $listParams
     */
    public function setExecuteParams(array $listParams) {
        $this->event->setParameters($listParams);
    }

    public function execute() {
        $eventManager = EventManager::getInstance();
        $eventManager->send($this->event);
    }

    /**
     * @return int
     */
    public function countOfHandlers() {
        $eventManager = EventManager::getInstance();
        return count($eventManager->findEventHandlers($this->event->getModuleId(), $this->event->getEventType()));
    }

    /**
     * @return Event
     */
    public function getEvent() {
        return $this->event;
    }
}
