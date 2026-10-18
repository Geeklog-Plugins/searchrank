<?php
/**
 * SearchRank read-only reporting tools. Compatible with PHP 5.6.
 * Daily totals are derived from existing legacy tables: no destructive migration.
 */
if (stripos($_SERVER['PHP_SELF'], basename(__FILE__)) !== false) {
    die('Direct access denied');
}

function SEARCHRANK_reportDays()
{
    $allowed = array(7, 28, 90, 365);
    $days = isset($_GET['days']) ? (int) $_GET['days'] : 28;
    return in_array($days, $allowed, true) ? $days : 28;
}

/**
 * Midnight calendar boundaries: avoids drifting one hour on DST transitions.
 * Legacy SearchRank records are stored as local-midnight Unix timestamps.
 */
function SEARCHRANK_reportStart($daysBack)
{
    $daysBack = max(0, (int) $daysBack);
    return strtotime('-' . $daysBack . ' days', strtotime('today'));
}

function SEARCHRANK_reportRows($days, $filter, $limit)
{
    global $_TABLES;
    $cutoff = SEARCHRANK_reportStart(max(1, (int) $days) - 1);
    $tomorrow = strtotime('tomorrow');
    $filterSql = '';
    if ($filter !== '') {
        $safe = DB_escapeString('%' . $filter . '%');
        $filterSql = " AND q.query LIKE '{$safe}'";
    }
    $limit = max(1, min(1000, (int) $limit));
    $sql = "SELECT q.query, SUM(h.hits) AS hits FROM {$_TABLES['searchrank_queries']} q "
         . "INNER JOIN {$_TABLES['searchrank_hits']} h ON q.hid=h.hid "
         . "WHERE h.date >= {$cutoff} AND h.date < {$tomorrow}{$filterSql} GROUP BY q.query "
         . "ORDER BY hits DESC, q.query ASC LIMIT {$limit}";
    $query = DB_query($sql);
    $rows = array();
    while ($row = DB_fetchArray($query)) {
        $rows[] = array('query' => $row['query'], 'hits' => (int) $row['hits']);
    }
    return $rows;
}

function SEARCHRANK_reportDaily($days)
{
    global $_TABLES;
    $cutoff = SEARCHRANK_reportStart(max(1, (int) $days) - 1);
    $tomorrow = strtotime('tomorrow');
    $result = DB_query("SELECT date, SUM(hits) AS hits FROM {$_TABLES['searchrank_hits']} "
        . "WHERE date >= {$cutoff} AND date < {$tomorrow} GROUP BY date ORDER BY date ASC");
    $counts = array();
    while ($row = DB_fetchArray($result)) {
        $counts[date('Y-m-d', (int) $row['date'])] = (int) $row['hits'];
    }
    // Include days without searches so daily activity reflects the full period.
    $rows = array();
    $days = max(1, (int) $days);
    for ($i = $days - 1; $i >= 0; --$i) {
        $timestamp = SEARCHRANK_reportStart($i);
        $day = date('Y-m-d', $timestamp);
        $rows[] = array('date' => $day, 'hits' => isset($counts[$day]) ? $counts[$day] : 0);
    }
    return $rows;
}

/**
 * Compare current leading search expressions with the preceding equal period.
 * Uses the existing daily-hit tables without changing their schema.
 */
function SEARCHRANK_reportTrends($days, $limit)
{
    global $_TABLES;
    $days = max(1, (int) $days);
    $limit = max(1, min(20, (int) $limit));
    $currentStart = SEARCHRANK_reportStart($days - 1);
    $previousStart = SEARCHRANK_reportStart($days * 2 - 1);
    $tomorrow = strtotime('tomorrow');
    $sql = "SELECT q.query, "
         . "SUM(CASE WHEN h.date >= {$currentStart} THEN h.hits ELSE 0 END) AS current_hits, "
         . "SUM(CASE WHEN h.date < {$currentStart} THEN h.hits ELSE 0 END) AS previous_hits "
         . "FROM {$_TABLES['searchrank_queries']} q "
         . "INNER JOIN {$_TABLES['searchrank_hits']} h ON q.hid=h.hid "
         . "WHERE h.date >= {$previousStart} AND h.date < {$tomorrow} "
         . "GROUP BY q.query HAVING current_hits > 0 "
         . "ORDER BY current_hits DESC, q.query ASC LIMIT {$limit}";
    $result = DB_query($sql);
    $rows = array();
    while ($row = DB_fetchArray($result)) {
        $rows[] = array('query' => $row['query'], 'current' => (int) $row['current_hits'],
                        'previous' => (int) $row['previous_hits']);
    }
    return $rows;
}

function SEARCHRANK_reportExport($days, $filter)
{
    $rows = SEARCHRANK_reportRows($days, $filter, 1000);
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="searchrank-report.csv"');
    header('Cache-Control: private, no-store');
    $out = fopen('php://output', 'w');
    fputcsv($out, array('Search query', 'Hits'));
    foreach ($rows as $row) {
        // Prevent spreadsheet formula injection in downloaded CSV files.
        $query = $row['query'];
        if (preg_match('/^\\s*[=+@\\-]/u', $query)) {
            $query = "'" . $query;
        }
        fputcsv($out, array($query, $row['hits']));
    }
    fclose($out);
}

function SEARCHRANK_reportTotals($days, $offsetDays)
{
    global $_TABLES;
    $days = max(1, (int) $days);
    $offsetDays = max(0, (int) $offsetDays);
    $start = SEARCHRANK_reportStart($days + $offsetDays - 1);
    $endExclusive = SEARCHRANK_reportStart($offsetDays - 1);
    $sql = "SELECT SUM(hits) AS total FROM {$_TABLES['searchrank_hits']} "
         . "WHERE date >= {$start} AND date < {$endExclusive}";
    $result = DB_query($sql);
    $row = DB_fetchArray($result);
    return $row && isset($row['total']) ? (int) $row['total'] : 0;
}

function SEARCHRANK_reportPage()
{
    global $_CONF;
    $days = SEARCHRANK_reportDays();
    $filter = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
    $filter = MBYTE_substr($filter, 0, 100);
    $base = $_CONF['site_admin_url'] . '/plugins/searchrank/index.php';
    $rows = SEARCHRANK_reportRows($days, $filter, 200);
    $daily = SEARCHRANK_reportDaily($days);
    $total = 0;
    foreach ($daily as $day) { $total += $day['hits']; }
    $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
    global $LANG_SEARCHRANK_1;
    // Until all legacy translations are updated, fall back to English labels.
    $fallback = array(
        'analytics' => 'Analytics & CSV',
        'dashboard' => 'Dashboard',
        'period' => 'Period',
        'days' => 'days',
        'search_expression' => 'Search expression',
        'filter' => 'Filter',
        'total_summary' => '%1$d searches over %2$d days (all expressions); %3$d matching expressions shown (maximum 200)',
        'previous_summary' => 'Previous %1$d days: %2$d searches.',
        'no_variation' => 'Variation not available (no previous data).',
        'variation' => 'Change: %s%%.',
        'no_searches' => 'No searches found for this period.',
        'trends_title' => 'Search expression trends',
        'trends_note' => 'Leading expressions compared with the preceding period; values depend on retained history.',
        'no_previous' => 'No previous recorded hits',
        'empty_trends' => 'No searches recorded for this period.',
        'retention_note' => 'History is limited by retention. Counts are internal searches, not Google/Bing impressions. Zero-result searches are not measured yet.',
        'export_link' => 'Export CSV (maximum 1,000 expressions)'
    );
    foreach ($fallback as $key => $value) {
        if (!isset($LANG_SEARCHRANK_1[$key])) {
            $LANG_SEARCHRANK_1[$key] = $value;
        }
    }
    $html = '<div class="sr-admin-home"><h2>' . $esc($LANG_SEARCHRANK_1['analytics']) . '</h2>';
    $html .= '<form method="get" action="' . $esc($base) . '" class="sr-filter">'
           . '<input type="hidden" name="action" value="report">'
           . '<label>' . $esc($LANG_SEARCHRANK_1['period']) . ' <select name="days">';
    foreach (array(7,28,90,365) as $d) {
        $html .= '<option value="' . $d . '"' . ($d === $days ? ' selected' : '') . '>' . $d . ' ' . $esc($LANG_SEARCHRANK_1['days']) . '</option>';
    }
    $html .= '</select></label><label>Search expression <input name="q" type="search" value="' . $esc($filter) . '"></label>'
           . '<button class="sr-action" type="submit">' . $esc($LANG_SEARCHRANK_1['filter']) . '</button></form>';
    $previous = SEARCHRANK_reportTotals($days, $days);
    $current = SEARCHRANK_reportTotals($days, 0);
    $trend = $previous > 0 ? round((($current - $previous) * 100) / $previous, 1) : null;
    $html .= '<div class="sr-summary">' . $esc(sprintf($LANG_SEARCHRANK_1['total_summary'], $total, $days, count($rows))) . '</div>';
    $html .= '<p class="sr-note">' . $esc(sprintf($LANG_SEARCHRANK_1['previous_summary'], $days, $previous))
           . ' ' . $esc($trend === null ? $LANG_SEARCHRANK_1['no_variation'] : sprintf($LANG_SEARCHRANK_1['variation'], $trend)) . '</p>';
    $html .= '<p><a href="' . $esc($base . '?action=export&days=' . $days . '&q=' . rawurlencode($filter)) . '">Export CSV (maximum 1,000 expressions)</a></p>';
    $html .= '<div class="sr-table-wrap"><table class="sr-report-table"><thead><tr><th scope="col">Expression</th><th scope="col">Hits</th></tr></thead><tbody>';
    foreach ($rows as $row) {
        $html .= '<tr><td>' . $esc($row['query']) . '</td><td>' . $row['hits'] . '</td></tr>';
    }
    if (!$rows) { $html .= '<tr><td colspan="2">' . $esc($LANG_SEARCHRANK_1['no_searches']) . '</td></tr>'; }
    $html .= '</tbody></table></div><h3>Daily activity</h3>';
    $html .= '<div class="sr-table-wrap"><table class="sr-report-table"><thead><tr><th scope="col">Date</th><th scope="col">Hits</th></tr></thead><tbody>';
    foreach ($daily as $row) {
        $html .= '<tr><td>' . $esc($row['date']) . '</td><td>' . $row['hits'] . '</td></tr>';
    }
    $html .= '</tbody></table></div>';
    $trends = SEARCHRANK_reportTrends($days, 20);
    $html .= '<h3>' . $esc($LANG_SEARCHRANK_1['trends_title']) . '</h3>';
    $html .= '<p class="sr-note">' . $esc($LANG_SEARCHRANK_1['trends_note']) . '</p>';
    $html .= '<div class="sr-table-wrap"><table class="sr-report-table"><thead><tr><th scope="col">Expression</th><th scope="col">Current</th><th scope="col">Previous</th><th scope="col">Change</th></tr></thead><tbody>';
    foreach ($trends as $entry) {
        $change = $entry['previous'] > 0
            ? round(100 * ($entry['current'] - $entry['previous']) / $entry['previous'], 1) . '%'
            : $LANG_SEARCHRANK_1['no_previous'];
        $html .= '<tr><td>' . $esc($entry['query']) . '</td><td>' . $entry['current']
               . '</td><td>' . $entry['previous'] . '</td><td>' . $esc($change) . '</td></tr>';
    }
    if (!$trends) {
        $html .= '<tr><td colspan="4">' . $esc($LANG_SEARCHRANK_1['empty_trends']) . '</td></tr>';
    }
    $html .= '</tbody></table></div>';
    $html .= '<p class="sr-note">' . $esc($LANG_SEARCHRANK_1['retention_note']) . '</p></div>';
    return $html;
}
