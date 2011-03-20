<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Work Ranking Plugin 1.1.0                                          |
// +---------------------------------------------------------------------------+
// | japanese_utf-8                                                            |
// |                                                                           |
// | Japanese language file                                                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Ivy                                                              |
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
    'plugin_name' => '検索ランキング',
    'title' => '検索ランキング',
    'noresults' => $LANG09[70],
    'clear_link' => '検索ログを削除する',
    'conf_link' => '設定編集',
    'viewlog_link' => '検索ログをみる',
    'clear_confirm' => '検索ログを削除しますか?',
    'clear_msg' => '検索ログは削除されました',
    'entries' => '%s エントリ',
    'hits' => 'ヒット',
    'back' => '戻る',
    'prev' => '最新のページ',
    'next' => '次のページ',
    'info' => 'Information',
    'admin' => 'Welcome to the Search Word Ranking Plugin administration page.',
    'info_main' => 'Below are the configuration options that are available for this plugin.',
    'info_log' => 'Below you can view the search queries that have been recorded so far. Use the buttons at the bottom of this page to navigate through the log.'
);

// Messages for the plugin upgrade
$PLG_searchrank_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"


// Localization of the Admin Configuration UI
$LANG_configsections['searchrank'] = array(
    'label' => '検索ランキング',
    'title' => '検索ランキングコンフィギュレーション'
);

$LANG_confignames['searchrank'] = array(
    'showblock' => '検索ランキングブロックを表示する',
    'maxresults' => 'ブロックに表示するエントリ数',
    'isleftblock' => '左側のブロックに配置する',
    'ttl' => '検索ログを保存する日数',
    'itemsperpage' => 'Items Per Page in the Log Viewer',
    'blockorder' => 'Block Order',
    'rootonly' => 'Only Show the Block to Users of Root Group'
);

$LANG_configsubgroups['searchrank'] = array(
    'sg_main' => 'メイン設定'
);

$LANG_tab['searchrank'] = array(
    'tab_main' => '検索ランキング'
);

$LANG_fs['searchrank'] = array(
    'fs_main' => '検索ランキングメイン設定'
);

$LANG_configselects['searchrank'] = array(
    0 => array('はい' => 1, 'いいえ' => 0),
    1 => array('はい' => true, 'いいえ' => false)
);

?>
