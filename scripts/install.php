<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('CHK_EVENT', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require(__DIR__ . '/../install/index.php');

/** @var CMain $APPLICATION */
global $APPLICATION;

$module = new ws_bunit();
$module->DoInstall();

$di = new RecursiveDirectoryIterator(__DIR__ . '/../lang');

/** @var SplFileInfo $fileInfo */
foreach ($di as $fileInfo) {
    if ($fileInfo->isDir()) {
        continue;
    }
    $content = file_get_contents($fileInfo->getPath());
    $convertedContent = $APPLICATION->ConvertCharset($content, LANG_CHARSET, "UTF-8");
    file_put_contents($fileInfo->getPath(), $convertedContent);
    $fileInfo->getPath();
}
