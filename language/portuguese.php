<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Search Work Ranking Plugin 1.1.0                                          |
// +---------------------------------------------------------------------------+
// | portuguese.php                                                            |
// |                                                                           |
// | Portuguese language file                                                  |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Oona                                                             |
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
    'plugin_name' => 'Ordem da Pesquisa de Palavras',
    'title' => 'Mais Pesquisados',
    'noresults' => $LANG09[70],
    'clear_link' => 'Limpar Registro da Pesquisa',
    'conf_link' => 'Editar Configura��es',
    'viewlog_link' => 'Exibir o Registo de Pesquisa',
    'clear_confirm' => 'Tem a Certeza que Quer Limpar o Registo de Pesquisa?',
    'clear_msg' => 'O Registo de Pesquisa foi Limpo',
    'entries' => 'Total: %s registros',
    'queries' => 'Consultas',
    'hits' => 'Hits',
    'back' => 'Voltar',
    'prev' => 'P�gina Anterior',
    'next' => 'Pr�xima P�gina',
    'info' => 'Informa��o',
    'admin' => 'Bem-vindo � p�gina de administra��o do Plugin Ordem da Pesquisa de Palavras.',
    'info_main' => 'Abaixo est�o as op��es de configura��o dispon�veis para este plugin.',
    'info_log' => 'Abaixo pode ver as consultas de pesquisa que t�m sido registradas at� o momento. Use os bot�es na parte inferior da p�gina para navegar pelo registro.',
    'stats_headline' => 'Top Ten Search Queries',
    'stats_no_hits' => 'It appears that no one has ever searched for anything, yet.'
);

// Messages for the plugin upgrade
$PLG_searchrank_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"


// Localization of the Admin Configuration UI
$LANG_configsections['searchrank'] = array(
    'label' => 'Ordem da Pesquisa de Palavras',
    'title' => 'Configura��o da Ordem da Pesquisa de Palavras'
);

$LANG_confignames['searchrank'] = array(
    'showstats' => 'Show Top Ten Search Queries In Site Statistics',
    'showblock' => 'Mostrar o Bloco da Ordem da Pesquisa de Palavras',
    'maxresults' => 'N�mero de Inscri��es no Bloco',
    'isleftblock' => 'Mostrar Bloco no Lado Esquerdo',
    'ttl' => 'Dias a Manter as Entradas no Registro',
    'itemsperpage' => 'Itens por P�gina no Exibidor do Registro',
    'blockorder' => 'Ordem de Posicionamento do Bloco',
    'rootonly' => 'Mostrar apenas o bloco aos usu�rios do grupo Root'
);

$LANG_configsubgroups['searchrank'] = array(
    'sg_main' => 'Defini��es Principais'
);

$LANG_tab['searchrank'] = array(
    'tab_main' => 'Defini��es Principais da Ordem da Pesquisa de Palavras'
);

$LANG_fs['searchrank'] = array(
    'fs_main' => 'Defini��es Principais da Ordem da Pesquisa de Palavras'
);

$LANG_configselects['searchrank'] = array(
    0 => array('Verdadeiro' => 1, 'Falso' => 0),
    1 => array('Verdadeiro' => true, 'Falso' => false)
);

?>
