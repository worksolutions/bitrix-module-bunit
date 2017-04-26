<?php
use Bitrix\Main\Application;

if (!class_exists('\WS\BUnit\Localization')) {
    include __DIR__.'/../lib/localization.php';
}
if (!class_exists('\WS\BUnit\Options')) {
    include __DIR__ . '/../lib/options.php';
}

class ws_bunit extends CModule {
    const FALLBACK_LOCALE = 'ru';
    const MODULE_ID = 'ws.bunit';
    var $MODULE_ID = 'ws.bunit';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    var $localization;

    /**
     * @return bool|string
     */
    public static function getModuleDir() {
        return realpath(__DIR__.'/../');
    }

    /**
     * @return \WS\BUnit\Localization
     */
    public static function localization() {
        $localizePath = static::getModuleDir().'/lang/'.LANGUAGE_ID;

        if (!file_exists($localizePath)) {
            $localizePath = static::getModuleDir().'/lang/'.self::FALLBACK_LOCALE;
        }

        return new \WS\BUnit\Localization(require $localizePath.'/info.php');
    }

    public function __construct() {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->PARTNER_NAME = GetMessage("WS_BUNIT_PARTNER_NAME");

        $localization = $this->localization();
        $this->MODULE_NAME = $localization->getDataByPath("name");
        $this->MODULE_DESCRIPTION = $localization->getDataByPath("description");
        $this->PARTNER_NAME = $localization->getDataByPath("partner.name");
        $this->PARTNER_URI = 'http://worksolutions.ru';
    }

    function InstallFiles() {
        $rootDir = Application::getDocumentRoot().Application::getPersonalRoot();

        $adminGatewayFile = '/tools/bunit';
        $isSuccess = copy(__DIR__. $adminGatewayFile, $rootDir . $adminGatewayFile);

        $bUnitConfigDir = '/php_interface/bunit';

        if (!is_dir($rootDir . $bUnitConfigDir)) {
            $isSuccess && $isSuccess = mkdir($rootDir . $bUnitConfigDir, 0777, true);
        }
        $sourceDir = __DIR__. $bUnitConfigDir;
        if (file_exists($rootDir . $bUnitConfigDir . "/config.php")) {
            unlink($rootDir . $bUnitConfigDir . "/config.php");
        }
        $isSuccess && $isSuccess = copy($sourceDir . "/config.php", $rootDir . $bUnitConfigDir . "/config.php");
        return $isSuccess;
    }

    function UnInstallFiles() {
        $rootDir = Application::getDocumentRoot().Application::getPersonalRoot();

        $adminGatewayFile = '/tools/bunit';
        $isSuccess = unlink($rootDir . $adminGatewayFile);

        return $isSuccess;
    }

    /**
     * @return bool
     */
    public function DoInstall() {
        global /** @var CMain $APPLICATION */
        $APPLICATION;
        $installResult = $this->InstallFiles();
        if (!$installResult) {
            $APPLICATION->ThrowException($this->localization()->getDataByPath('install.error.files'));
            return false;
        }
        if (LANG_CHARSET == "UTF-8" && !$this->isUtfLangFiles()) {
            $this->convertLangFilesToUtf();
        }
        RegisterModule($this->MODULE_ID);
        CModule::IncludeModule($this->MODULE_ID);
        $title = $this->localization()->getDataByPath('setup.up');
        $APPLICATION->IncludeAdminFile($title, __DIR__.'/step.php');
        return true;
    }

    /**
     * @return bool
     */
    public function DoUninstall() {
        global /** @var CMain $APPLICATION */
        $APPLICATION;
        $installResult = $this->UnInstallFiles();
        if (!$installResult) {
            $APPLICATION->ThrowException($this->localization()->getDataByPath('uninstall.error.files'));
            return false;
        }
        UnRegisterModule($this->MODULE_ID);
        $title = $this->localization()->getDataByPath('setup.down');
        $APPLICATION->IncludeAdminFile($title, __DIR__.'/unstep.php');
        return true;
    }

    public function isUtfLangFiles() {
        $localization = new \WS\BUnit\Localization(include static::getModuleDir() . '/lang/ru/info.php');
        return $localization->message("charset") == "Кодировка";
    }

    public function convertLangFilesToUtf() {
        /** @var CMain $APPLICATION */
        global $APPLICATION;
        $di = new RecursiveDirectoryIterator(static::getModuleDir() . '/lang/ru');

        /** @var SplFileInfo $fileInfo */
        foreach ($di as $fileInfo) {
            if ($fileInfo->isDir()) {
                continue;
            }
            $content = file_get_contents($fileInfo->getPath());
            $convertedContent = $APPLICATION->ConvertCharset($content, "windows-1251", "UTF-8");
            file_put_contents($fileInfo->getPath(), $convertedContent);
        }
    }
}
