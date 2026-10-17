<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Word Ranking Plugin 1.2.1                                          |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API methods and 2) implements all the common code needed by this plugin.  |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

// This file is sometimes loaded inside a Geeklog function during plugin enable.
// Explicit globals keep runtime state available after the include returns.
global $_SR_CONF, $dohousekeeping, $_TABLES, $_DB_table_prefix;

// Remember to do housekeeping once
$dohousekeeping = true;

require_once $_CONF['path_system'] . 'lib-admin.php';

/**
 * Language file include
 */
$plugin_path = $_CONF['path'] . 'plugins/searchrank/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

/**
* Check and see if we need to load the plugin configuration
*/ 
if (!isset($_SR_CONF['maxresults'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    
    $sr_config = config::get_instance();
    $_SR_CONF = $sr_config->get_config('searchrank');
}

/**
 * Search Word Ranking plugin table(s)
 */
// Geeklog may include functions.inc inside PLG_enableStateChange() scope.
// Use the current site's database table prefix (also in multisite).
$_TABLES['searchrank_queries'] = $_DB_table_prefix . 'searchrank_queries';
$_TABLES['searchrank_hits'] = $_DB_table_prefix . 'searchrank_hits';

/**
* Returns an array of search log entries 
*
* @param    int      $start      offset records
* @param    int      $limit      max rows to return or 0 to return any number of rows
*
* @return    array    log entries
*/
function SEARCHRANK_getResults($start = 0, $limit = 0)
{
    global $_TABLES;
    $retval = array();
    $start = max(0, (int) $start);
    $limit = max(0, min(200, (int) $limit));
    $sql = "SELECT {$_TABLES['searchrank_queries']}.query, SUM({$_TABLES['searchrank_hits']}.hits) AS allhits "
                  . "FROM {$_TABLES['searchrank_queries']} INNER JOIN {$_TABLES['searchrank_hits']} "
                  . "ON {$_TABLES['searchrank_queries']}.hid={$_TABLES['searchrank_hits']}.hid "
                  . "WHERE 1=1 GROUP BY query "
                  . "ORDER BY allhits DESC, {$_TABLES['searchrank_queries']}.query ASC ";
    if ($limit != 0) {
        $sql .= "LIMIT $start,$limit";
    }
    $query = DB_query($sql);
    while ($A = DB_fetchArray($query)) {
        $retval[] = array('query' => $A[0], 'hits' => $A[1]);
    }
    return $retval;
}

function SEARCHRANK_numResults()
{
    global $_TABLES;
    $sql = "SELECT {$_TABLES['searchrank_queries']}.query, SUM({$_TABLES['searchrank_hits']}.hits) "
         . "FROM {$_TABLES['searchrank_queries']} INNER JOIN {$_TABLES['searchrank_hits']} "
         . "ON {$_TABLES['searchrank_queries']}.hid={$_TABLES['searchrank_hits']}.hid "
         . "WHERE 1=1 GROUP BY query";
    $query = DB_query($sql);
    return DB_numRows($query);
}

/**
* Clears the search log
*
* @return    void
*/
function SEARCHRANK_clearLog()
{
    global $_TABLES, $LANG_SEARCHRANK_1;
    DB_query("DELETE FROM {$_TABLES['searchrank_hits']}");
    DB_query("DELETE FROM {$_TABLES['searchrank_queries']}");
}

/**
* Deletes old log entries
*
* @return    void
*/
function SEARCHRANK_housekeeping()
{
    global $_TABLES, $_SR_CONF, $dohousekeeping;

    if ($dohousekeeping) {
        $dohousekeeping = false; // do housekeeping once only
        $today = strtotime(date("d-m-Y") . ' 00:00:00'); // today's date
        // Stored dates are Unix timestamps (seconds); ttl is expressed in days.
        $ttl = max(1, (int) $_SR_CONF['ttl']);
        $old = $today - ($ttl * 86400);
        // Never delete a query while it still has recent hit records.
        DB_query("DELETE FROM {$_TABLES['searchrank_hits']} WHERE date < {$old}");
        DB_query("DELETE FROM {$_TABLES['searchrank_queries']} WHERE NOT EXISTS "
               . "(SELECT 1 FROM {$_TABLES['searchrank_hits']} "
               . "WHERE {$_TABLES['searchrank_hits']}.hid = {$_TABLES['searchrank_queries']}.hid)");
    }
}

/**
* Returns an HTML string with the menu for the admin page
*
* @return    string     admin menu string
*/
function SEARCHRANK_adminNav($active)
{
    global $_CONF, $LANG_SEARCHRANK_1;
    $base = $_CONF['site_admin_url'] . '/plugins/searchrank/index.php';
    $sections = array(
        'dashboard' => array($base, 'Dashboard'),
        'showlog' => array($base . '?action=showlog', $LANG_SEARCHRANK_1['viewlog_link']),
        'report' => array($base . '?action=report', 'Analytics & CSV'),
        'help' => array($base . '?action=help', 'Help')
    );
    $out = '<nav class="plugin-admin-nav" aria-label="SearchRank administration"><div class="plugin-admin-nav__primary">';
    foreach ($sections as $key => $section) {
        $selected = ($key === $active);
        $out .= '<a class="plugin-admin-nav__item' . ($selected ? ' is-active' : '') . '"'
            . ($selected ? ' aria-current="page"' : '') . ' href="'
            . htmlspecialchars($section[0], ENT_QUOTES, 'UTF-8') . '">'
            . htmlspecialchars($section[1], ENT_QUOTES, 'UTF-8') . '</a>';
    }
    $out .= '<form class="plugin-admin-nav__form" method="post" action="'
         . htmlspecialchars($_CONF['site_admin_url'] . '/configuration.php', ENT_QUOTES, 'UTF-8') . '">'
         . '<input type="hidden" name="conf_group" value="searchrank">'
         . '<button class="plugin-admin-nav__item" type="submit">'
         . htmlspecialchars($LANG_SEARCHRANK_1['conf_link'], ENT_QUOTES, 'UTF-8') . '</button></form>';
    return $out . '</div></nav>';
}

function SEARCHRANK_makeMenu()
{
    global $_CONF, $LANG_SEARCHRANK_1;

    SEARCHRANK_housekeeping();
    $base = $_CONF['site_admin_url'] . '/plugins/searchrank/index.php';
    $config = $_CONF['site_admin_url'] . '/configuration.php';
    $count = SEARCHRANK_numResults();

    $html = '<section class="sr-admin-home">';
    $html .= '<p class="sr-intro">' . htmlspecialchars($LANG_SEARCHRANK_1['info_main'], ENT_QUOTES, 'UTF-8') . '</p>';
    $html .= '<div class="sr-summary"><strong>' . (int) $count . '</strong> '
          . htmlspecialchars($LANG_SEARCHRANK_1['queries'], ENT_QUOTES, 'UTF-8') . '</div>';
    $html .= '<nav class="sr-actions" aria-label="SearchRank">';
    $html .= '<a class="sr-action" href="' . htmlspecialchars($base . '?action=showlog', ENT_QUOTES, 'UTF-8') . '">'
          . htmlspecialchars($LANG_SEARCHRANK_1['viewlog_link'], ENT_QUOTES, 'UTF-8') . '</a>';
    $html .= '<form class="sr-config-form" method="post" action="' . htmlspecialchars($config, ENT_QUOTES, 'UTF-8') . '">'
          . '<input type="hidden" name="conf_group" value="searchrank">'
          . '<button class="sr-action" type="submit">' . htmlspecialchars($LANG_SEARCHRANK_1['conf_link'], ENT_QUOTES, 'UTF-8') . '</button></form>';
    $html .= '<a class="sr-action" href="' . htmlspecialchars($base . '?action=report', ENT_QUOTES, 'UTF-8') . '">Analytics &amp; CSV</a>';
    $html .= '<a class="sr-action" href="' . htmlspecialchars($base . '?action=help', ENT_QUOTES, 'UTF-8') . '">Help</a>';
    $html .= '</nav>';
    $html .= '<section class="sr-danger"><h3>' . htmlspecialchars($LANG_SEARCHRANK_1['clear_link'], ENT_QUOTES, 'UTF-8') . '</h3>';
    $html .= '<form method="post" action="' . htmlspecialchars($base, ENT_QUOTES, 'UTF-8') . '" onsubmit="return confirm(this.getAttribute(\'data-confirm\'));" data-confirm="'
          . htmlspecialchars($LANG_SEARCHRANK_1['clear_confirm'], ENT_QUOTES, 'UTF-8') . '">'
          . '<input type="hidden" name="action" value="clear">'
          . '<input type="hidden" name="token" value="' . htmlspecialchars(SEC_createToken(), ENT_QUOTES, 'UTF-8') . '">'
          . '<button class="sr-clear" type="submit">' . htmlspecialchars($LANG_SEARCHRANK_1['clear_link'], ENT_QUOTES, 'UTF-8') . '</button></form>';
    $html .= '</section></section>';

    return $html;
}

/**
* Returns an HTML string with all the entries from the log file
*
* @return    string     log entries
*/
function SEARCHRANK_showLog()
{
   global $_TABLES, $LANG_SEARCHRANK_1, $_SR_CONF, $_CONF, $LANG32, $LANG_ADMIN;

    // Clamp pagination settings to avoid division by zero and SQL injection.
    $perPage = isset($_SR_CONF['itemsperpage']) ? (int) $_SR_CONF['itemsperpage'] : 20;
    $perPage = max(1, min(200, $perPage));
    $total = SEARCHRANK_numResults();
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = isset($_GET['page']) ? (int) COM_applyFilter($_GET['page']) : 1;
    $page = max(1, min($totalPages, $page));
    $results = SEARCHRANK_getResults(($page - 1) * $perPage, $perPage);

    // Print the number of entries
    $baseUrl = $_CONF['site_admin_url'] . '/plugins/searchrank/index.php';
    $retval = '<div class="sr-log-toolbar"><a class="sr-back-link" href="'
        . htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') . '">&larr; '
        . htmlspecialchars($LANG_SEARCHRANK_1['back'], ENT_QUOTES, 'UTF-8') . '</a></div>';
    $retval .= '<p>' . htmlspecialchars(sprintf($LANG_SEARCHRANK_1['entries'], $total), ENT_QUOTES, 'UTF-8') . '</p>';
    // Print all other entries
    $header_arr = array(array('text' => $LANG_SEARCHRANK_1['queries'], 'field' => 'query'),
                        array('text' => $LANG_SEARCHRANK_1['hits'], 'field' => 'hits'));
    $retval .= ADMIN_simpleList('ADMIN_getListField_newplugins', $header_arr, array(), $results);

    $back_url = $baseUrl;

    // Compact pagination: unavailable controls must not appear clickable.
    $retval .= '<nav class="sr-pagination" aria-label="Search log pages">';
    if ($page > 1) {
        $prev_url = $back_url . '?action=showlog&page=' . ($page - 1);
        $retval .= '<a class="sr-action" href="' . htmlspecialchars($prev_url, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($LANG_SEARCHRANK_1['prev'], ENT_QUOTES, 'UTF-8') . '</a>';
    }
    $retval .= '<span class="sr-page-indicator">' . (int) $page . ' / ' . (int) $totalPages . '</span>';
    if ($page < $totalPages) {
        $next_url = $back_url . '?action=showlog&page=' . ($page + 1);
        $retval .= '<a class="sr-action" href="' . htmlspecialchars($next_url, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($LANG_SEARCHRANK_1['next'], ENT_QUOTES, 'UTF-8') . '</a>';
    }
    $retval .= '</nav>';

    return $retval;
}

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+
/**
* This searches for entries matching the user query and returns an array for the
* header and table rows back to search.php where it will be formated and printed
*
* @param    string  $query      Keywords user is looking for
* @param    date    $datestart  Start date to get results for
* @param    date    $dateend    End date to get results for
* @param    string  $topic      The topic they were searching in
* @param    string  $type       Type of items they are searching, or 'all' (deprecated)
* @param    int     $author     Get all results by this author
* @param    string  $keyType    search key type: 'all', 'phrase', 'any'
* @param    int     $page       page number of current search (deprecated)
* @param    int     $perpage    number of results per page (deprecated)
* @return   object              search result object
*
*/
function plugin_dopluginsearch_searchrank($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES, $LANG_SEARCHRANK_1;

    SEARCHRANK_housekeeping();
    $today = strtotime(date("d-m-Y") . " 00:00:00"); // todays date
    $hid = mt_rand(1000000, 9999999); // use random number 'hid'
    // ensure that the random number is unique
    while (DB_Count($_TABLES['searchrank_queries'], 'hid', $hid) != 0) {
        $hid = mt_rand(1000000, 9999999);
    }
    $query = MBYTE_substr(trim(strtolower($query)), 0, 99);
    $query = DB_escapeString($query);
    if (empty($query)) {
        return array(); // Return an empty array
    }
    // Check if this query is already in the database
    $q = DB_query("SELECT hid FROM {$_TABLES['searchrank_queries']} WHERE query='{$query}'");
    $rows = DB_numRows($q);
    if ($rows == 0) {
        // Add a new row
        DB_query("INSERT INTO {$_TABLES['searchrank_queries']} (query, hid) VALUES ('$query',$hid)");
        DB_query("INSERT INTO {$_TABLES['searchrank_hits']} (hid, date, hits) VALUES ($hid, $today, 1)");
    } else     {
        // Increment by a hit
        $data = DB_fetchArray($q);
        $hid = $data['hid'];
        $q = DB_query("SELECT date FROM {$_TABLES['searchrank_hits']} WHERE hid='{$hid}' AND date='{$today}'");
        if (DB_numRows($q) == 0) {
            // there is an entry for this query, but not for today
            $sql = "INSERT INTO {$_TABLES['searchrank_hits']} (hid, date, hits) VALUES ($hid, $today, 1)";
        } else {
            // there is already an entry for this query for today
            $sql = "UPDATE {$_TABLES['searchrank_hits']} SET hits = hits + 1 WHERE hid='{$hid}' AND date='{$today}'";
        }
        DB_query($sql);    
    }
     // Execute query
    return array(); // Return an empty array
}

function plugin_getheadercode_searchrank()
{
    global $_CONF, $_SR_CONF;

    $retval = '';
    
    // Only include the CSS file for the block, if the block will be shown
    if ($_SR_CONF['showblock'] && (!$_SR_CONF['rootonly'] || SEC_inGroup('Root'))) {
        // use this css for the block
        $retval = LB . '<link rel="stylesheet" type="text/css" href="'
                . $_CONF['site_admin_url'] . '/plugins/searchrank/css/block.css?v=1.2.4"' . XHTML . '>' . LB;
    }
    // use this CSS only if we are on the plugin's pages
    if (substr_count($_SERVER['PHP_SELF'], '/searchrank/') > 0) {
        $retval .= '<link rel="stylesheet" type="text/css" href="'
                . $_CONF['site_admin_url'] . '/plugins/searchrank/css/admin.css?v=1.2.4"' . XHTML . '>' . LB;
    }

    return $retval;
}

/**
* Gets Geeklog blocks from plugins
*
* Returns data for blocks on a given side and, potentially, for
* a given topic.
*
* @param    string  $side   Side to get blocks for (right or left for now)
* @param    string  $topic  Only get blocks for this topic
* @return   array           array of block data
* @link     http://wiki.geeklog.net/index.php/Dynamic_Blocks
*
*/
function plugin_getBlocks_searchrank($side, $topic='')
{
    global $_TABLES, $_CONF, $_SR_CONF, $LANG_SEARCHRANK_1;

    SEARCHRANK_housekeeping();
    $retval = array();

    // check that the correct version of the plugin is installed
    if (! isset($_SR_CONF['rootonly'])) {
        return $retval;
    }

    // check if we need to show the block
    if ($_SR_CONF['showblock'] && (!$_SR_CONF['rootonly'] || SEC_inGroup('Root'))) {
        if (($side=='left' && $_SR_CONF['isleftblock'] == 1) || ($side=='right' && $_SR_CONF['isleftblock'] == 0)) { // Create a block
            $results = SEARCHRANK_getResults(0, $_SR_CONF['maxresults']);
            // Create the list
            $rows = count($results);
            if ($rows == 0) {
                // No queries found
                $display = "<div style='margin: 0;'>{$LANG_SEARCHRANK_1['noresults']}</div>";
            } else {
                $display = "<div class='searchrank'>";
                foreach($results as $key => $value) {
                    $value['query'] = stripslashes($value['query']);
                    $title = $value['query'];
                    if (MBYTE_strlen($value['query']) > 15) {
                        $value['query'] = MBYTE_substr($value['query'], 0, 13) . "...";
                    }
                    if ($value['hits'] > 1000) {
                        $value['hits'] = (int)($value['hits'] / 1000) . "K";
                    }
                    $url = $_CONF['site_url'] . '/search.php?mode=search&amp;type=all&amp;keyType=all&amp;query=' . urlencode($title);
                    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                    $display .= "<div class='searchrank_item' title='{$safeTitle}'>";
                    $display .= COM_createLink($value['query'], $url, array('rel' => 'nofollow'));
                    $display .= "<div class='searchrank_entry'>{$value['hits']} {$LANG_SEARCHRANK_1['hits']}</div></div>";
                }
                $display .= "</div>";
            }
            $retval[] = array('name'           => 'searchrank',
                              'type'           => 'dynamic',
                              'onleft'         => 1,
                              'title'          => $LANG_SEARCHRANK_1['title'],
                              'blockorder'     => $_SR_CONF['blockorder'],
                              'content'        => $display,
                              'allow_autotags' => false,
                              'help'           => '');
        }
    }
    return $retval;
}

/**
* Shows the statistics for the searchrank plugin on stats.php.
* If $showsitestats is 1 then we are to only print the overall stats in the
* 'site statistics box' otherwise we show the detailed stats
*
* @param    int     $showsitestate  Flag to let us know which stats to get
* @param    string                  HTML for the stats section
*
*/
function plugin_showstats_searchrank ($showsitestats)
{
    global $_CONF, $_SR_CONF, $_TABLES, $LANG_SEARCHRANK_1;

    $retval = '';

    if ($showsitestats != 1 && $_SR_CONF['showstats']) {
        require_once $_CONF['path_system'] . 'lib-admin.php';

        $results = SEARCHRANK_getResults(0, 10);
        if (count($results) > 0) {
            $header_arr = array(
                array('text'         => $LANG_SEARCHRANK_1['queries'],
                      'field'        => 'query',
                      'header_class' => 'stats-header-title'
                ),
                array('text'         => $LANG_SEARCHRANK_1['hits'],
                      'field'        => 'hits',
                      'header_class' => 'stats-header-count',
                      'field_class'  => 'stats-list-count'
                )
            );
            $text_arr = array('has_menu' => false,
                              'title'    => $LANG_SEARCHRANK_1['stats_headline'],
                        );
            $data_arr = array();
            foreach($results as $key => $value) {
                $data_arr[] = array('query' => $value['query'],
                                    'hits' => COM_numberFormat($value['hits'])
                              );
            }
            $retval .= ADMIN_simpleList ('', $header_arr, $text_arr, $data_arr);
        } else {
            $retval .= COM_startBlock ($LANG_SEARCHRANK_1['stats_headline']);
            $retval .= $LANG_SEARCHRANK_1['stats_no_hits'];
            $retval .= COM_endBlock ();
        }
    }

    return $retval;
}

/**
* Return plugin entry for "Command and Control" (moderation.php)
*
* @return  array  Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_searchrank()
{
    global $_CONF, $LANG_SEARCHRANK_1;

    $retval = array();
    if (SEC_hasRights('searchrank.admin')) {
        $retval = array($LANG_SEARCHRANK_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/searchrank/index.php',
            plugin_geticon_searchrank());
    }

    return $retval;
}

/**
* Return plugin entry for the Admins Only block
*
* @return array Array containing (plugin name, plugin admin url, # of plugin items or '')
*/
function plugin_getadminoption_searchrank()
{
    global $_CONF, $LANG_SEARCHRANK_1;

    if (SEC_hasRights('searchrank.admin')) {
        $num = SEARCHRANK_numResults();
        if ($num > 1000) {
            $num = (int)($num / 1000) . 'K';
        }
        return array($LANG_SEARCHRANK_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/searchrank/index.php', $num);
    }
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_searchrank()
{
    global $_CONF;

    return $_CONF['site_admin_url'] . '/plugins/searchrank/images/searchrank.png';
}

/**
 * Returns the current plugin code version
 *
 * @return string    plugin version
 */
function plugin_chkVersion_searchrank()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/searchrank/autoinstall.php';

    $inst_parms = plugin_autoinstall_searchrank('searchrank');

    return $inst_parms['info']['pi_version'];
}

/**
* Update the plugin
*
* @return   int     Number of message to display or true for success
*
*/
function plugin_upgrade_searchrank()
{
    global $_CONF, $_TABLES, $_DB_dbms;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'searchrank'");
    $code_version = plugin_chkVersion_searchrank();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/searchrank/autoinstall.php';

    if (! plugin_compatible_with_this_version_searchrank('searchrank')) {
        return 3002;
    }

    // update plugin version number
    $inst_parms = plugin_autoinstall_searchrank('searchrank');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    require_once $_CONF['path'] . 'plugins/searchrank/sql/'
                                . $_DB_dbms . '_updates.php';
                                
    require_once $_CONF['path'] . 'plugins/searchrank/install_updates.php';                                

    $current_version = $installed_version;
    $done = false;
    while (! $done) {
        switch ($current_version) {
        case '1.0.0':
            if (isset($_UPDATES[$current_version])) {
                $_SQL = $_UPDATES[$current_version];
                foreach ($_SQL as $sql) {
                    DB_query($sql);
                }
            }
            SEARCHRANK_update_ConfValues_1_0_0();
            $current_version = '1.1.0';
            break;

        case '1.1.0':
            $current_version = '1.1.1';
            break;

        case '1.1.1':
            $current_version = '1.2.1';
            break;
            
        case '1.2.1':
            $current_version = '1.2.2';
            break;
			
        case '1.2.2':
            $current_version = '1.2.3';
            break;			

        default:
            $done = true;
        }
    }

    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'searchrank'");

    return true;
}

/**
* Automatic uninstall function for plugins
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
* @return   array   Plugin information
*
*/
function plugin_autouninstall_searchrank()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('searchrank_queries', 'searchrank_hits'),
        /* give the full name of the group, as in the db */
        'groups' => array('Search Word Ranking Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('searchrank.admin', 'config.searchrank.tab_main'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}
