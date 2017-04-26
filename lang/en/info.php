<?php

return array(
    'name' => 'WS BUnit',
    'description' => 'Unit testing for projects are powered by CMS Bitrix.',
    'partner' => array(
        'name' => 'Work Solutions',
        'url' => 'http://www.worksolutions.ru'
    ),
    'setup' => array(
        'up' => 'Install bunit',
        'down' => 'Uninstall bunit'
    ),
    'install' => array(
        'error' => array(
            'files' => 'Failing of placed files `bitrix/php_interface` è `bitrix/tools`, check up writing files'
        ),
        'success' => 'Module has been installed'
    ),
    'uninstall' => array(
        'error' => array(
            'files' => 'Failing of remove files from `bitrix/tools`, check up remove files from directory'
        ),
        'success' => 'Module has been removed'
    )
);
