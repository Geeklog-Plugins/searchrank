<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Word Ranking Plugin 1.2.1                                          |
// +---------------------------------------------------------------------------+
// | german.php                                                                |
// |                                                                           |
// | German language file                                                      |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Rouslan Placella - rouslan AT placella DOT com                   |
// | Translation: Dirk Haun    - dirk AT haun-online DOT de                    |
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
* @package SearchWordRangliste
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
    'plugin_name' => 'Suchwort-Rangliste',
    'title' => 'Am meisten gesucht',
    'noresults' => $LANG09[70],
    'clear_link' => 'Suchprotokoll löschen',
    'conf_link' => 'Einstellungen ändern',
    'viewlog_link' => 'Suchprotokoll ansehen',
    'clear_confirm' => 'Suchprotokoll wirklich löschen?',
    'clear_msg' => 'Das Suchprotokoll wurde gelöscht',
    'entries' => 'Insgesamt: %s Einträge',
    'queries' => 'Suchabfragen',
    'hits' => 'Abfragen',
    'back' => 'Zurück',
    'prev' => 'Vorherige Seite',
    'next' => 'Nächste Seite',
    'info' => 'Information',
    'admin' => 'Willkommen auf der Administrations-Seite des Suchwort-Ranglisten-Plugins.',
    'info_main' => 'Dies sind die Einstellungen, die für dieses Plugin verfügbar sind.',
    'info_log' => 'Hier kannst Du die Suchabfragen sehen, die bisher aufgezeichnet wurden. Mit den Buttons am Ende der Seite kannst Du durch das Protokoll navigieren.',
    'stats_headline' => 'Top Ten der Suchabfragen',
    'stats_no_hits' => 'Es wurden noch keine Suchabfragen aufgezeichnet.'
);

// Messages for the plugin upgrade
$PLG_searchrank_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"


// Localization of the Admin Configuration UI
$LANG_configsections['searchrank'] = array(
    'label' => 'Suchwort-Rangliste',
    'title' => 'Suchwort-Rangliste Konfiguration'
);

$LANG_confignames['searchrank'] = array(
    'showstats' => 'Top Ten-Abfragen in der Statistik anzeigen',
    'showblock' => 'Suchwort-Ranglisten-Block anzeigen',
    'maxresults' => 'Anzahl Einträge im Block',
    'isleftblock' => 'Block links anzeigen',
    'ttl' => 'Protokoll-Einträge aufheben (in Tagen)',
    'itemsperpage' => 'Einträge pro Seite in der Protokoll-Anzeige',
    'blockorder' => 'Block-Reihenfolge',
    'rootonly' => 'Block nur für Root-User anzeigen'
);

$LANG_configsubgroups['searchrank'] = array(
    'sg_main' => 'Haupteinstellungen'
);

$LANG_tab['searchrank'] = array(
    'tab_main' => 'Suchwort-Rangliste Haupteinstellungen'
);

$LANG_fs['searchrank'] = array(
    'fs_main' => 'Suchwort-Rangliste Haupteinstellungen'
);

$LANG_configselects['searchrank'] = array(
    0 => array('Ja' => 1, 'Nein' => 0),
    1 => array('Ja' => true, 'Nein' => false)
);
