<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Word Ranking Plugin 1.2.1                                          |
// +---------------------------------------------------------------------------+
// | pgsql_install.php                                                         |
// |                                                                           |
// | Installation SQL                                                          |
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

$_SQL[] = "
CREATE TABLE {$_TABLES['searchrank_queries']} (
  query character varying(100) NOT NULL DEFAULT ''::character varying,
  hid integer NOT NULL DEFAULT 1,
  CONSTRAINT {$_TABLES['searchrank_queries']}_query PRIMARY KEY (query)
)
";

$_SQL[] = "
CREATE INDEX {$_TABLES['searchrank_queries']}_hid
  ON {$_TABLES['searchrank_queries']}
  USING btree (hid);
";

$_SQL[] = "
CREATE TABLE {$_TABLES['searchrank_hits']} (
  hid integer NOT NULL DEFAULT 1,
  date integer NOT NULL DEFAULT 1,
  hits integer NOT NULL DEFAULT 1
)
";

$_SQL[] = "
CREATE INDEX {$_TABLES['searchrank_hits']}_hid
  ON {$_TABLES['searchrank_hits']}
  USING btree (hid);
";

$_SQL[] = "
CREATE INDEX {$_TABLES['searchrank_hits']}_date
  ON {$_TABLES['searchrank_hits']}
  USING btree (date);
";
