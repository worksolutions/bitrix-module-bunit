<?php
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use WS\BUnit\Config;

$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('CHK_EVENT', true);
@set_time_limit(0);

$time = microtime(true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

global $USER;
$USER->Authorize(1);

CModule::IncludeModule('ws.bunit');

$configFile = $DOCUMENT_ROOT . BX_ROOT . "/php_interface/bunit/config.php";
if (file_exists($configFile)) {
    $fInclude = function () use ($configFile) {
        global $APPLICATION;
        global $USER;
        global $DB;
        include $configFile;
    };
    $fInclude();
}

$em = EventManager::getInstance();
$event = new Event("ws.bunit", "OnConfigure");
$config =  Config::getDefaultConfig();
$event->setParameter("config", $config);
$em->send($event);

try {
    $console = new \WS\BUnit\Console\Console($argv, $config);

    $console->getWriter()
        ->setColor(0)
        ->printLine("xUnit framework for CMS Bitrix. Worksolutions company https://worksolutions.ru");

    $console->getCommand()->execute();

} catch (Exception $e) {
    $console->getWriter()
        ->setColor(\WS\BUnit\Console\Formatter\Output::COLOR_RED)
        ->printLine($e->getMessage());
}
$console->getWriter()
    ->setColor(0)
    ->printLine(
        "--------------------------------------------------"
    )
    ->printLine(
        sprintf(
            "Time: %0.3f sec, maximum memory usage: %0.2f Mb",
            microtime(true) - $time,
            memory_get_peak_usage()/1024/1024
        )
    );
