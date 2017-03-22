<?php
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

$console = new \WS\BUnit\Console\Console($argv);

$console->getWriter()
    ->setColor(0)
    ->printLine("xUnit framework for CMS Bitrix. Worksolutions company https://worksolutions.ru");

// Замерять время выполнения каждой команды
// Есть команда help

$console->getCommand()->execute();
$console->getWriter()
    ->setColor(\WS\BUnit\Console\Formatter\Output::COLOR_RED)
    ->printLine(sprintf("\nTime: %0.3f sec\n", microtime(true) - $time));

