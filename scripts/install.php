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

ob_start();
$installResult = $module->DoInstall();
ob_end_clean();

if (!$installResult) {
    throw new Exception($APPLICATION->GetException()->GetString());
} else {
    echo "Install success\n";
}
