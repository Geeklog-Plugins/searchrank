<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Work Ranking Plugin 1.1.0                                          |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
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
* Install data and defaults for the Search Word Ranking plugin configuration
*
* @package SearchRank
*/
if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/**
* SearchRank default settings
*
* Initial Installation Defaults used when loading the online configuration
* records.  These settings are only used during the initial installation
* and not referenced any more once the plugin is installed
*/
global $_SR_DEFAULT;
$_SR_DEFAULT = array();

/**
* Initialize SearchRank plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist.  Initial values will be taken from $_SR_DEFAULT.
*
* @return   boolean     TRUE: success; FALSE: an error occurred
*/
function plugin_initconfig_searchrank()
{
    global $_SR_CONF, $_SR_DEFAULT;

    if (is_array($_SR_CONF) && (count($_SR_CONF) > 1)) {
        $_SR_DEFAULT = array_merge($_SR_DEFAULT, $_SR_CONF);
    }

    $me = 'searchrank';

    $c = config::get_instance();
    if (!$c->group_exists('searchrank')) {
        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, $me, 0);
        $c->add('tab_main', NULL, 'tab', 0, 0, NULL, 0, true, $me, 0);
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, $me, 0);
        $c->add('showstats', false, 'select',0, 0, 1, 10, true, $me, 0);
        $c->add('showblock', true, 'select',0, 0, 1, 20, true, $me, 0);
        $c->add('rootonly', false, 'select', 0, 0, 1, 30, true, $me, 0);
        $c->add('isleftblock', true, 'select', 0, 0, 1, 40, true, $me, 0);
        $c->add('blockorder', 1, 'text', 0, 0, null, 50, true, $me, 0);
        $c->add('maxresults', 5, 'text', 0, 0, null, 60, true, $me, 0);
        $c->add('itemsperpage', 20, 'text', 0, 0, null, 70, true, $me, 0);
        $c->add('ttl', 28, 'text', 0, 0, null, 80, true, $me, 0);
    }

    return true;
}

?>

