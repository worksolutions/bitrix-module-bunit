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

// «амер€ть врем€ выполнени€ каждой команды
$console->getCommand()->execute();
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
