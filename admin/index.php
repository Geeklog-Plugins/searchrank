<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Word Ranking Plugin 1.2.1                                          |
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
if (isset($_POST['action'])) {
    $action = COM_applyFilter($_POST['action']);
} elseif (isset($_GET['action'])) {
    $action = COM_applyFilter($_GET['action']);
}

// Ensure user even has the rights to access this page
if (! SEC_hasRights('searchrank.admin')) {
    $display .= COM_startBlock($MESSAGE[30])
             .  COM_showMessageText($MESSAGE[29], $MESSAGE[30])
             .  COM_endBlock();
    $display = COM_createHTMLDocument($display, array('what' => 'meu'));

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the Search Word Ranking plugin administration screen.");

    COM_output($display);
    exit;
}

// Reporting only reads existing SearchRank data. Export before output headers.
require_once __DIR__ . '/reporting.inc.php';
if ($action === 'export') {
    SEARCHRANK_reportExport(SEARCHRANK_reportDays(),
        isset($_GET['q']) && is_string($_GET['q']) ? MBYTE_substr(trim($_GET['q']), 0, 100) : '');
    exit;
}

// MAIN
$display .= COM_startBlock($LANG_SEARCHRANK_1['plugin_name']);
if ($action == 'clear') {
    // Destructive operations require POST and a valid Geeklog CSRF token.
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'
        && SEC_checkToken()) {
        SEARCHRANK_clearLog();
        $display .= COM_showMessageText($LANG_SEARCHRANK_1['clear_msg'], $LANG_SEARCHRANK_1['info']);
    } else {
        $display .= COM_showMessageText('Invalid request or security token.', $LANG_SEARCHRANK_1['info']);
    }
    $display .= ADMIN_createMenu(array(),
                                $LANG_SEARCHRANK_1['admin'] . '<br' . XHTML . '>' . '<br' . XHTML . '>' . $LANG_SEARCHRANK_1['info_main'],
                                plugin_geticon_searchrank());
    $display .= SEARCHRANK_makeMenu();
} else if ($action == 'help') {
    $display .= '<div class="sr-admin-home">'
        . '<h2>SearchRank — Getting started</h2>'
        . '<p>SearchRank records searches performed on this website. It does not measure Google or Bing searches.</p>'
        . '<h3>1. Configure</h3><p>Choose the retention duration and whether the public ranking block is visible. Searches are grouped by phrase and day.</p>'
        . '<h3>2. View the search log</h3><p>Open the Search Log to inspect saved phrases and their total occurrences.</p>'
        . '<h3>3. Analyze activity</h3><p>Open Analytics &amp; CSV to filter phrases and compare periods. Older data may have been removed by retention settings.</p>'
        . '<h3>4. Privacy</h3><p>Search phrases can contain personal information. Avoid making the ranking block public on sensitive sites. The clear-log action permanently deletes recorded searches.</p>'
        . '<h3>5. Troubleshooting</h3><p>If statistics are empty, run a public search, verify the plugin is enabled and check the configured retention period.</p>'
        . '<p><a class="sr-action" href="index.php">Back to dashboard</a></p></div>';
} else if ($action == 'report') {
    $display .= SEARCHRANK_reportPage();
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
$display .= COM_endBlock();
$display = COM_createHTMLDocument($display, array('what' => 'menu'));
COM_output($display);
