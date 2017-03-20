<?php

namespace WS\BUnit;

use Bitrix\Main\Application;

/**
 * Class Module
 * namespace WS\Tools
 * pattern Singleton
 */

class Module {

    const MODULE_ID = 'ws.bunit';
    const ITEMS_ID = 'ws_bunit_menu';
    const MODULE_NAME = 'ws.bunit';
    const FALLBACK_LOCALE = 'ru';

    private $localizePath = null;
    private $localizations = array();
    private static  $name = self::MODULE_NAME;

    private function __construct() {
        $this->localizePath = __DIR__.'/../lang/'.LANGUAGE_ID;

        if (!file_exists($this->localizePath)) {
            $this->localizePath = __DIR__.'/../lang/'.self::FALLBACK_LOCALE;
        }
    }

    /**
     * @return Application
     */
    public function application() {
        return Application::getInstance();
    }

    /**
     * Will get module facade
     * @return Module
     */
    public static function getInstance() {
        static $self = null;
        if(!$self) {
            $self = new self;
        }
        return $self;
    }

    /**
     * Works with i18n messages
     * @param $path
     * @throws \Exception
     * @return mixed
     */
    public function getLocalization($path) {
        if(!$this->localizations[$path]) {
            $realPath = realpath($this->localizePath.'/'.str_replace('.', '/',$path).'.php');
            if(!file_exists($realPath)) {
                throw new \Exception('Exception '.__CLASS__ . ' method _getLocalization message - файл не найден');
            }

            $data = include $realPath;
            $this->localizations[$path] = new Localization($data);
        }
        return $this->localizations[$path];
    }

    /**
     * @return \CUser
     */
    public function getUser() {
        global $USER;
        return $USER;
    }

    static public function getName($stripDots = false) {
        $name = static::$name;
        if ($stripDots) {
            $name = str_replace('.', '_', $name);
        }
        return $name;
    }
}
