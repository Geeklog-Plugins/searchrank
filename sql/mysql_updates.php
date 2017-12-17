<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Word Ranking Plugin 1.2.1                                          |
// +---------------------------------------------------------------------------+
// | mysql_updates.php                                                         |
// |                                                                           |
// | SQL for updates                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Rouslan Placella - rouslan AT placella DOT com                   |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

$_UPDATES = array(
    '1.0.0' => array(
        "ALTER TABLE {$_TABLES['searchrank_queries']} ADD PRIMARY KEY (query)",
        "CREATE INDEX {$_TABLES['searchrank_queries']}_hid ON {$_TABLES['searchrank_queries']}(hid)",
        "CREATE INDEX {$_TABLES['searchrank_hits']}_hid ON {$_TABLES['searchrank_hits']}(hid)",
        "CREATE INDEX {$_TABLES['searchrank_hits']}_date ON {$_TABLES['searchrank_hits']}(date)",
        "UPDATE {$_TABLES['groups']} SET grp_name='Search Word Ranking Admin' WHERE grp_name='Search Work Ranking Admin'",
        "UPDATE {$_TABLES['groups']} SET grp_descr='Users in this group can administer the Search Word Ranking plugin' WHERE grp_name='Search Word Ranking Admin'",
        "UPDATE {$_TABLES['features']} SET ft_descr='Full access to Search Word Ranking plugin' WHERE ft_descr='Full access to Search Work Ranking plugin'",
        "UPDATE {$_TABLES['features']} SET ft_descr='Access to configure Search Word Ranking main settings' WHERE ft_descr='Access to configure Search Work Ranking main settings'"
    )
);
