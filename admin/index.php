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
$section = in_array($action, array('showlog', 'report', 'help'), true) ? $action : 'dashboard';
$display .= SEARCHRANK_adminNav($section);
if ($action == 'clear') {
    // Destructive operations require POST and a valid Geeklog CSRF token.
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'
        && SEC_checkToken()) {
        SEARCHRANK_clearLog();
        $display .= COM_showMessageText($LANG_SEARCHRANK_1['clear_msg'], $LANG_SEARCHRANK_1['info']);
    } else {
        $display .= COM_showMessageText(isset($LANG_SEARCHRANK_1['invalid_request']) ? $LANG_SEARCHRANK_1['invalid_request'] : 'Invalid request or security token.', $LANG_SEARCHRANK_1['info']);
    }
    $display .= SEARCHRANK_makeMenu();
} else if ($action == 'help') {
    $helpFallback = array(
        'help_title' => 'SearchRank — Getting started',
        'help_intro' => 'SearchRank records searches performed on this website. It does not measure Google or Bing searches.',
        'help_setup_title' => '1. Configure',
        'help_setup_text' => 'Choose retention and public ranking visibility. Searches are grouped by phrase and day.',
        'help_log_title' => '2. View the search log',
        'help_log_text' => 'Inspect saved search phrases and their total occurrences.',
        'help_analytics_title' => '3. Analyze activity',
        'help_analytics_text' => 'Filter phrases and compare periods in Analytics & CSV.',
        'help_privacy_title' => '4. Privacy',
        'help_privacy_text' => 'Search phrases can contain personal information. Clearing the log permanently removes recorded searches.',
        'help_troubleshooting_title' => '5. Troubleshooting',
        'help_troubleshooting_text' => 'If statistics are empty, run a public search, check plugin activation and retention settings.'
    );
    foreach ($helpFallback as $key => $value) {
        if (!isset($LANG_SEARCHRANK_1[$key])) {
            $LANG_SEARCHRANK_1[$key] = $value;
        }
    }
    $display .= '<div class="sr-admin-home">';
    $display .= '<h2>' . htmlspecialchars($LANG_SEARCHRANK_1['help_title'], ENT_QUOTES, 'UTF-8') . '</h2>';
    $display .= '<p>' . htmlspecialchars($LANG_SEARCHRANK_1['help_intro'], ENT_QUOTES, 'UTF-8') . '</p>';
    $display .= '<h3>' . htmlspecialchars($LANG_SEARCHRANK_1['help_setup_title'], ENT_QUOTES, 'UTF-8') . '</h3>';
    $display .= '<p>' . htmlspecialchars($LANG_SEARCHRANK_1['help_setup_text'], ENT_QUOTES, 'UTF-8') . '</p>';
    $display .= '<h3>' . htmlspecialchars($LANG_SEARCHRANK_1['help_log_title'], ENT_QUOTES, 'UTF-8') . '</h3>';
    $display .= '<p>' . htmlspecialchars($LANG_SEARCHRANK_1['help_log_text'], ENT_QUOTES, 'UTF-8') . '</p>';
    $display .= '<h3>' . htmlspecialchars($LANG_SEARCHRANK_1['help_analytics_title'], ENT_QUOTES, 'UTF-8') . '</h3>';
    $display .= '<p>' . htmlspecialchars($LANG_SEARCHRANK_1['help_analytics_text'], ENT_QUOTES, 'UTF-8') . '</p>';
    $display .= '<h3>' . htmlspecialchars($LANG_SEARCHRANK_1['help_privacy_title'], ENT_QUOTES, 'UTF-8') . '</h3>';
    $display .= '<p>' . htmlspecialchars($LANG_SEARCHRANK_1['help_privacy_text'], ENT_QUOTES, 'UTF-8') . '</p>';
    $display .= '<h3>' . htmlspecialchars($LANG_SEARCHRANK_1['help_troubleshooting_title'], ENT_QUOTES, 'UTF-8') . '</h3>';
    $display .= '<p>' . htmlspecialchars($LANG_SEARCHRANK_1['help_troubleshooting_text'], ENT_QUOTES, 'UTF-8') . '</p>';
    $display .= '</div>';
} else if ($action == 'report') {
    $display .= SEARCHRANK_reportPage();
} else if ($action == 'showlog') {
    $display .= SEARCHRANK_showLog();
} else {
    $display .= SEARCHRANK_makeMenu();
}
$display .= COM_endBlock();
$display = COM_createHTMLDocument($display, array('what' => 'menu'));
COM_output($display);
