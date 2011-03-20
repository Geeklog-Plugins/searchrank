<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Word Ranking Plugin 1.1.0                                          |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Plugin administration page                                                |
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
* @package SearchWordRanking
*/

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

$display = '';
$action = '';
if (isset($_GET['action'])) {
  $action = trim($_GET['action']);
}

// Ensure user even has the rights to access this page
if (! SEC_hasRights('searchrank.admin')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30])
             . COM_showMessageText($MESSAGE[29], $MESSAGE[30])
             . COM_siteFooter();

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the Search Word Ranking plugin administration screen.");

    echo $display;
    exit;
}

// MAIN
$display .= COM_siteHeader('menu', $LANG_SEARCHRANK_1['plugin_name']);
$display .= COM_startBlock($LANG_SEARCHRANK_1['plugin_name']);
if ($action == 'clear') {
    SEARCHRANK_clearLog(); // assume that everything went well
    $display .= COM_showMessageText($LANG_SEARCHRANK_1['clear_msg'], $LANG_SEARCHRANK_1['info']);
    $display .= ADMIN_createMenu(array(),
                                $LANG_SEARCHRANK_1['admin'] . '<br' . XHTML . '>' . '<br' . XHTML . '>' . $LANG_SEARCHRANK_1['info_main'],
                                plugin_geticon_searchrank());
    $display .= SEARCHRANK_makeMenu();
} else if ($action == 'showlog') {
    $display .= ADMIN_createMenu(array(),
                                $LANG_SEARCHRANK_1['admin'] . '<br' . XHTML . '>' . '<br' . XHTML . '>' . $LANG_SEARCHRANK_1['info_log'],
                                plugin_geticon_searchrank());
    $display .= SEARCHRANK_showLog();
} else {
    $display .= ADMIN_createMenu(array(),
                                $LANG_SEARCHRANK_1['admin'] . '<br' . XHTML . '>' . '<br' . XHTML . '>' . $LANG_SEARCHRANK_1['info_main'],
                                plugin_geticon_searchrank());
    $display .= SEARCHRANK_makeMenu();
}
// this line is a hack to make the admin menu items disappear from professional_css theme
$display .= "<script type='text/javascript'>if(document.getElementById('menu_fields')){document.getElementById('menu_fields').id='';}</script>";
$display .= COM_endBlock();
$display .= COM_siteFooter();

echo $display;

?>
