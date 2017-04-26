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
     * @return \WS\BUnit\Localization
     */
    public static function localization() {
        $localizePath = __DIR__.'/../lang/'.LANGUAGE_ID;

        if (!file_exists($localizePath)) {
            $localizePath = __DIR__.'/../lang/'.self::FALLBACK_LOCALE;
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
        $rootDir = Application::getDocumentRoot().'/'.Application::getPersonalRoot();

        $adminGatewayFile = '/tools/bunit';
        $isSuccess = copy(__DIR__. $adminGatewayFile, $rootDir . $adminGatewayFile);

        $bUnitConfigFolder = '/php_interface/bunit';

        $isSuccess && $isSuccess = mkdir($rootDir . $bUnitConfigFolder, 0664);
        $isSuccess && $isSuccess = copy(__DIR__. $bUnitConfigFolder, $rootDir . $bUnitConfigFolder . "/config.php");
        return $isSuccess;
    }

    function UnInstallFiles() {
        $rootDir = Application::getDocumentRoot().'/'.Application::getPersonalRoot();

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
        RegisterModule($this->MODULE_ID);
        $installResult = $this->InstallFiles();
        if (!$installResult) {
            $APPLICATION->ThrowException($this->localization()->getDataByPath('install.error.files'));
            return false;
        }

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
}