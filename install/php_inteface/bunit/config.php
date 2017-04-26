<?php

global $DB;

$charset = defined("BX_UTF") ? "UTF-8" : "cp1251";

$config = \WS\BUnit\Config::getDefaultConfig();

$config->set(
    array(
        'db' => array(
            'original' => array(
                'host' => $DB->DBHost,
                'user' => $DB->DBLogin,
                'password' => $DB->DBPassword,
                'db' => $DB->DBName,
                'charset' => $charset
            ),
            /**
            Use it if you have test clone of real database
            'test' => array(
                'host' => 'localhost',
                'user' => 'root',
                'password' => '',
                'db' => 'migrations_test',
                'charset' => 'cp1251'
            )
             */
        ),
        'folder' => __DIR__."/tests",
    )
);