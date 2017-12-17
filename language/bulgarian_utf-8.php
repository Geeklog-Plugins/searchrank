<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Word Ranking Plugin 1.2.1                                          |
// +---------------------------------------------------------------------------+
// | bulgarian.php                                                             |
// |                                                                           |
// | Bulgarian language file                                                   |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Preslav Petkov                                                   |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

/**
* @package SearchWordRanking
*/

/**
* Import Geeklog plugin messages for reuse
*
* @global array $LANG32
*/
global $LANG32, $LANG09;

// +---------------------------------------------------------------------------+
// | Array Format:                                                             |
// | $LANGXX[YY]:  $LANG - variable name                                       |
// |               XX    - specific array name                                 |
// |               YY    - phrase id or number                                 |
// +---------------------------------------------------------------------------+

$LANG_SEARCHRANK_1 = array(
    'plugin_name' => 'Класиране на Търсене на Думи',
    'title' => 'Най-Търсени',
    'noresults' => $LANG09[70],
    'clear_link' => 'Истрий Логът',
    'conf_link' => 'Промени Настройки',
    'viewlog_link' => 'Покажи Логът',
    'clear_confirm' => 'Сигорен Ли Си, Че Искаш Да Истриеш Логът?',
    'clear_msg' => 'Логът е Изтрит',
    'entries' => 'Тотален Просент Заглавия',
    'queries' => 'Търсени Неща',
    'hits' => 'Влизания',
    'back' => 'Върни се',
    'prev' => 'Предишна Страница',
    'next' => 'Следваща Страница',
    'info' => 'Информация',
    'admin' => 'Добре Дошъл в Класиране на Търсене на Думи Страница на Администратора.',
    'info_main' => 'Отдолу са Опцийте за Конфигурация Които са на Разположение за Този Плъг-Ин',
    'info_log' => 'Отдолу Може да Видите Търсените Неща Които са Записани до Сега. Използвай Бутоните Най-Отдолу на Страницата да Навигираш През Логът.',
    'stats_headline' => 'Top Ten Search Queries',
    'stats_no_hits' => 'It appears that no one has ever searched for anything, yet.'
);

// Messages for the plugin upgrade
$PLG_searchrank_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"


// Localization of the Admin Configuration UI
$LANG_configsections['searchrank'] = array(
    'label' => 'Класиране на Търсене на Думи',
    'title' => 'Класиране на Търсене на Думи Настройки'
);

$LANG_confignames['searchrank'] = array(
    'showstats' => 'Show Top Ten Search Queries In Site Statistics',
    'showblock' => 'Покажи Блогът на Класиране на Търсене на Думи',
    'maxresults' => 'Брой На Заглавия в Блогът',
    'isleftblock' => 'Покажи Блогът в Ляво',
    'ttl' => 'Дни Блогът да Бъде Пазен',
    'itemsperpage' => 'Колко Неща да Бъдат Показани на Страница',
    'blockorder' => 'Ред на Колоните',
    'rootonly' => 'Покажи Блогът Само на Членове на Руут Групата'
);

$LANG_configsubgroups['searchrank'] = array(
    'sg_main' => 'Главни Настройки'
);

$LANG_tab['searchrank'] = array(
    'tab_main' => 'Класиране на Търсене на Думи Главни Настройки'
);

$LANG_fs['searchrank'] = array(
    'fs_main' => 'Класиране на Търсене на Думи Главни Настройки'
);

$LANG_configselects['searchrank'] = array(
    0 => array('Да' => 1, 'Не' => 0),
    1 => array('Да' => true, 'Не' => false)
);
