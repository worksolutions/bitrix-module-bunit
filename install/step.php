<?php

if(!check_bitrix_sessid()) {
    return;
}

$message = ws_bunit::localization()->message("install.success");
CAdminMessage::ShowNote($message);
