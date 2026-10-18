<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Classement des recherches Plugin 1.2.1                                          |
// +---------------------------------------------------------------------------+
// | french_france_utf-8.php                                                |
// |                                                                           |
// | French (France) UTF-8 language file                                                     |
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
    'plugin_name' => 'Classement des recherches',
    'title' => 'Recherches populaires',
    'noresults' => $LANG09[70],
    'clear_link' => 'Vider le journal des recherches',
    'conf_link' => 'Configuration',
    'viewlog_link' => 'Journal des recherches',
    'clear_confirm' => 'Vider définitivement le journal des recherches ?',
    'clear_msg' => 'Le journal des recherches a été vidé',
    'entries' => 'Total : %s entrées',
    'queries' => 'Recherches',
    'hits' => 'Occurrences',
    'back' => 'Retour',
    'prev' => 'Page précédente',
    'next' => 'Page suivante',
    'info' => 'Information',
    'admin' => 'Administration du classement des recherches',
    'info_main' => 'Configurez et consultez les recherches internes enregistrées sur ce site.',
    'info_log' => 'Consultez les expressions recherchées et leur nombre d\'occurrences.',
    'analytics' => 'Statistiques et CSV',
    'help_link' => 'Aide',
    'dashboard' => 'Tableau de bord',
    'period' => 'Période',
    'search_expression' => 'Expression recherchée',
    'filter' => 'Filtrer',
    'days' => 'jours',
    'expression' => 'Expression',
    'daily_activity' => 'Activité quotidienne',
    'chart_description' => 'Recherches par jour',
    'chart_show_data' => 'Voir les recherches par jour',
    'date' => 'Date',
    'export_csv' => 'Exporter en CSV',
    'no_period_results' => 'Aucune recherche pour cette période.',
    'invalid_token' => 'Requête ou jeton de sécurité invalide.',
    'trends_title' => 'Évolution des recherches',
    'trends_note' => 'Comparaison des expressions principales avec la période précédente. Les résultats dépendent de la durée de conservation.',
    'current' => 'Période actuelle',
    'previous' => 'Période précédente',
    'change' => 'Variation',
    'no_previous' => 'Aucune occurrence sur la période précédente',
    'no_searches' => 'Aucune recherche pour cette période.',
    'total_summary' => '%1$d recherches sur %2$d jours (toutes expressions) ; %3$d expressions affichées (maximum 200)',
    'previous_summary' => '%2$d recherches sur les %1$d jours précédents.',
    'no_variation' => 'Variation indisponible (aucune donnée précédente).',
    'variation' => 'Variation : %s %%.',
    'export_link' => 'Exporter en CSV (maximum 1 000 expressions)',
    'retention_note' => 'L\'historique dépend de la durée de conservation. Seules les recherches internes sont comptées, et non les impressions Google ou Bing. Les recherches sans résultat ne sont pas encore distinguées.',
    'empty_trends' => 'Aucune recherche enregistrée pour cette période.',
    'help_title' => 'SearchRank — Premiers pas',
    'help_intro' => 'SearchRank enregistre les recherches effectuées sur ce site, et non les recherches Google ou Bing.',
    'help_setup_title' => '1. Configuration',
    'help_setup_text' => 'Choisissez la durée de conservation et la visibilité du bloc public. Les recherches sont regroupées par expression et par jour.',
    'help_log_title' => '2. Journal des recherches',
    'help_log_text' => 'Consultez les expressions enregistrées et leur fréquence.',
    'help_analytics_title' => '3. Analyse de l\'activité',
    'help_analytics_text' => 'Filtrez les expressions, consultez les courbes et comparez les périodes dans Statistiques et CSV.',
    'help_privacy_title' => '4. Confidentialité',
    'help_privacy_text' => 'Les expressions peuvent contenir des données personnelles. Évitez d\'afficher publiquement le classement sur les sites sensibles. La suppression du journal est définitive.',
    'help_troubleshooting_title' => '5. Dépannage',
    'help_troubleshooting_text' => 'Si les statistiques sont vides, effectuez une recherche, vérifiez que le plugin est activé et contrôlez la durée de conservation.',
    'invalid_request' => 'Requête ou jeton de sécurité invalide.',
    'ranked_queries' => 'Expressions les plus recherchées',
    'stats_headline' => 'Les dix recherches les plus fréquentes',
    'stats_no_hits' => 'Aucune recherche n\'a encore été enregistrée.'
);

// Messages for the plugin upgrade
$PLG_searchrank_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"


// Localization of the Admin Configuration UI
$LANG_configsections['searchrank'] = array(
    'label' => 'Classement des recherches',
    'title' => 'Configuration du classement des recherches'
);

$LANG_confignames['searchrank'] = array(
    'showstats' => 'Afficher les dix recherches principales dans les statistiques',
    'showblock' => 'Afficher le bloc de classement des recherches',
    'maxresults' => 'Nombre d\'entrées dans le bloc',
    'isleftblock' => 'Afficher le bloc à gauche',
    'ttl' => 'Durée de conservation des recherches (jours)',
    'itemsperpage' => 'Éléments par page du journal',
    'blockorder' => 'Ordre du bloc',
    'rootonly' => 'Réserver le bloc aux administrateurs'
);

$LANG_configsubgroups['searchrank'] = array(
    'sg_main' => 'Paramètres généraux'
);

$LANG_tab['searchrank'] = array(
    'tab_main' => 'Classement des recherches Paramètres généraux'
);

$LANG_fs['searchrank'] = array(
    'fs_main' => 'Classement des recherches Paramètres généraux'
);

$LANG_configselects['searchrank'] = array(
    0 => array('Oui' => 1, 'Non' => 0),
    1 => array('Oui' => true, 'Non' => false)
);

?>
