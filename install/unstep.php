<?php

if(!check_bitrix_sessid()) {
    return;
}

$message = ws_bunit::localization()->message("uninstall.success");
CAdminMessage::ShowNote($message);