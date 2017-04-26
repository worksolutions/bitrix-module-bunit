<?php

return array(
    'name' => 'WS BUnit',
    'description' => 'Модульное тестирование проектов CMS Bitrix.',
    'partner' => array(
        'name' => 'Рабочие Решения',
        'url' => 'http://www.worksolutions.ru'
    ),
    'setup' => array(
        'up' => 'Установка модуля bunit',
        'down' => 'Деинсталяция модуля bunit'
    ),
    'install' => array(
        'error' => array(
            'files' => 'Не удалось переместить файлы в директориях `bitrix/php_interface` и `bitrix/tools`, проверьте возможность записи файлов'
        ),
        'success' => 'Модуль bunit установлен. Рабочие решения.'
    ),
    'uninstall' => array(
        'error' => array(
            'files' => 'Не удалось удалить файлы из директории `bitrix/tools`, проверьте возможность удаления файлов'
        ),
        'success' => 'Модуль успешно удален из системы'
    )
);
