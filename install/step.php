<?php

if(!check_bitrix_sessid()) {
    return;
}

echo CAdminMessage::ShowNote("Модуль bunit установлен. Рабочие решения.");
