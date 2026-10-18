<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Word Ranking Plugin 1.2.1                                          |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// |                                                                           |
// | English language file                                                     |
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
    'plugin_name' => 'Search Word Ranking',
    'title' => 'Most Searched',
    'noresults' => $LANG09[70],
    'clear_link' => 'Clear the Search Log',
    'conf_link' => 'Edit Settings',
    'viewlog_link' => 'View the Search Log',
    'clear_confirm' => 'Really Clear the Search Log?',
    'clear_msg' => 'The search log has been cleared',
    'entries' => 'Total: %s entries',
    'queries' => 'Queries',
    'hits' => 'Hits',
    'back' => 'Go Back',
    'prev' => 'Previous Page',
    'next' => 'Next Page',
    'info' => 'Information',
    'admin' => 'Welcome to the Search Word Ranking Plugin administration page.',
    'info_main' => 'Below are the configuration options that are available for this plugin.',
    'info_log' => 'Below you can view the search queries that have been recorded so far. Use the buttons at the bottom of this page to navigate through the log.',
    'analytics' => 'Analytics & CSV',
    'dashboard' => 'Dashboard',
    'period' => 'Period',
    'days' => 'days',
    'search_expression' => 'Search expression',
    'filter' => 'Filter',
    'help_link' => 'Help',
    'daily_activity' => 'Daily activity',
    'chart_description' => 'Searches per day',
    'chart_show_data' => 'View daily values',
    'comparison_unavailable' => 'Comparison unavailable: retention does not cover two complete periods.',
    'retention_period_note' => 'Retention: %d days. Older data may be unavailable.',
    'trends_title' => 'Search expression trends',
    'ranked_queries' => 'Most searched expressions',
    'trends_note' => 'Leading expressions compared with the preceding period; values depend on retained history.',
    'current' => 'Current',
    'previous' => 'Previous',
    'change' => 'Change',
    'date' => 'Date',
    'no_previous' => 'No previous recorded hits',
    'no_searches' => 'No searches found for this period.',
    'total_summary' => '%1$d searches over %2$d days (all expressions); %3$d matching expressions shown (maximum 200)',
    'previous_summary' => 'Previous %1$d days: %2$d searches.',
    'no_variation' => 'Variation not available (no previous data).',
    'variation' => 'Change: %s%%.',
    'export_link' => 'Export CSV (maximum 1,000 expressions)',
    'retention_note' => 'Historical information is limited by SearchRank retention. Counts are internal searches, not Google/Bing impressions. Zero-result searches are not measured yet.',
    'empty_trends' => 'No searches recorded for this period.',
    'help_title' => 'SearchRank — Getting started',
    'help_intro' => 'SearchRank records searches performed on this website. It does not measure Google or Bing searches.',
    'help_setup_title' => '1. Configure',
    'help_setup_text' => 'Choose the retention duration and whether the public ranking block is visible. Searches are grouped by phrase and day.',
    'help_log_title' => '2. View the search log',
    'help_log_text' => 'Open the Search Log to inspect saved phrases and their total occurrences.',
    'help_analytics_title' => '3. Analyze activity',
    'help_analytics_text' => 'Open Analytics & CSV to filter phrases and compare periods. Older data may have been removed by retention settings.',
    'help_privacy_title' => '4. Privacy',
    'help_privacy_text' => 'Search phrases can contain personal information. Avoid making the ranking block public on sensitive sites. The clear-log action permanently deletes recorded searches.',
    'help_troubleshooting_title' => '5. Troubleshooting',
    'help_troubleshooting_text' => 'If statistics are empty, run a public search, verify the plugin is enabled and check the configured retention period.',
    'invalid_request' => 'Invalid request or security token.',
    'stats_headline' => 'Top Ten Search Queries',
    'stats_no_hits' => 'It appears that no one has ever searched for anything, yet.'
);

// Messages for the plugin upgrade
$PLG_searchrank_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"


// Localization of the Admin Configuration UI
$LANG_configsections['searchrank'] = array(
    'label' => 'Search Word Ranking',
    'title' => 'Search Word Ranking Configuration'
);

$LANG_confignames['searchrank'] = array(
    'showstats' => 'Show Top Ten Search Queries In Site Statistics',
    'showblock' => 'Show Search Word Ranking Block',
    'maxresults' => 'Number of Entries in the Block',
    'isleftblock' => 'Display Block on Left',
    'ttl' => 'Days to Keep Log Entries',
    'itemsperpage' => 'Items Per Page in the Log Viewer',
    'blockorder' => 'Block Order',
    'rootonly' => 'Only Show the Block to Users of Root Group'
);

$LANG_configsubgroups['searchrank'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_tab['searchrank'] = array(
    'tab_main' => 'Search Word Ranking Main Settings'
);

$LANG_fs['searchrank'] = array(
    'fs_main' => 'Search Word Ranking Main Settings'
);

$LANG_configselects['searchrank'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => true, 'False' => false)
);
