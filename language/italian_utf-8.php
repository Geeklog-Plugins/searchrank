<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Work Ranking Plugin 1.1.0                                          |
// +---------------------------------------------------------------------------+
// | italian.php                                                               |
// |                                                                           |
// | Italian language file                                                     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Rouslan Placella - rouslan AT placella DOT com                   |
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
* @package SearchWorkRanking
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
    'plugin_name' => 'Classifica dei Termini di Ricerca',
    'title' => 'I più ricercati',
    'noresults' => $LANG09[70],
    'clear_link' => 'Svuota la Lista della Ricerche',
    'conf_link' => 'Modifica impostazioni',
    'viewlog_link' => 'Visualizza la Lista delle Ricerche',
    'clear_confirm' => 'Veramente Svuotare la Lista della Ricerche?',
    'clear_msg' => 'La Lista della Ricerche é Stata Svuotata',
    'entries' => 'Totale: %s voci',
    'queries' => 'Termini di Ricerca',
    'hits' => 'Visite',
    'back' => 'Ritorna',
    'prev' => 'Pagina Precedente',
    'next' => 'Pagina Seguente',
    'info' => 'Informazioni',
    'admin' => 'Benvenuto alla ricerca pagina amministrativa della plugin di Classifica dei Termini di Ricerca.',
    'info_main' => 'Sottostanti sono le opzioni di configurazione disponibili per questa plugin.',
    'info_log' => 'In basso sono mostrate i termini di ricerca che sono stati registrati finora. Utilizza i collegamenti in fondo alla pagina per navigare attraverso la lista.'
);

// Messages for the plugin upgrade
$PLG_searchrank_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"


// Localization of the Admin Configuration UI
$LANG_configsections['searchrank'] = array(
    'label' => 'Classifica dei Termini di Ricerca',
    'title' => 'Impostazioni della Classifica dei Termini di Ricerca'
);

$LANG_confignames['searchrank'] = array(
    'showblock' => 'Mostra il Blocco della Classifica dei Termini di Ricerca',
    'maxresults' => 'Numero di Elementi nel Blocco',
    'isleftblock' => 'Mostra il Blocco a Sinistra',
    'ttl' => 'Dopo Quanti Giorni Cancellare le Voci dalla Lista della Ricerche',
    'itemsperpage' => 'Numero di Elementi per Pagina nel Visualizzatore della Lista delle Ricerche',
    'blockorder' => 'Ordine del Blocco',
    'rootonly' => 'Mostra il Blocco Solo ad Utenti del Gruppo Root'
);

$LANG_configsubgroups['searchrank'] = array(
    'sg_main' => 'Impostazioni Principali'
);

$LANG_tab['searchrank'] = array(
    'tab_main' => 'Impostazioni Principali della Classifica dei Termini di Ricerca'
);

$LANG_fs['searchrank'] = array(
    'fs_main' => 'Impostazioni Principali della Classifica dei Termini di Ricerca'
);

$LANG_configselects['searchrank'] = array(
    0 => array('Vero' => 1, 'Falso' => 0),
    1 => array('Vero' => true, 'Falso' => false)
);

?>
