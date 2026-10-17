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

function SEARCHRANK_reportRows($days, $filter, $limit)
{
    global $_TABLES;
    $cutoff = strtotime('today') - (max(1, (int) $days) - 1) * 86400;
    $filterSql = '';
    if ($filter !== '') {
        $safe = DB_escapeString('%' . $filter . '%');
        $filterSql = " AND q.query LIKE '{$safe}'";
    }
    $limit = max(1, min(1000, (int) $limit));
    $sql = "SELECT q.query, SUM(h.hits) AS hits FROM {$_TABLES['searchrank_queries']} q "
         . "INNER JOIN {$_TABLES['searchrank_hits']} h ON q.hid=h.hid "
         . "WHERE h.date >= {$cutoff}{$filterSql} GROUP BY q.query "
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
    $cutoff = strtotime('today') - (max(1, (int) $days) - 1) * 86400;
    $result = DB_query("SELECT date, SUM(hits) AS hits FROM {$_TABLES['searchrank_hits']} "
        . "WHERE date >= {$cutoff} GROUP BY date ORDER BY date ASC");
    $rows = array();
    while ($row = DB_fetchArray($result)) {
        $rows[] = array('date' => date('Y-m-d', (int) $row['date']), 'hits' => (int) $row['hits']);
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
    $start = strtotime('today') - (($days + $offsetDays - 1) * 86400);
    $end = strtotime('today') - ($offsetDays * 86400);
    $sql = "SELECT SUM(hits) AS total FROM {$_TABLES['searchrank_hits']} "
         . "WHERE date >= {$start} AND date < " . ($end + 86400);
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
        'filter' => 'Filter'
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
    $html .= '<div class="sr-summary"><strong>' . $total . '</strong> total searches over ' . $days . ' days (all expressions); '
           . count($rows) . ' matching expressions shown (maximum 200)</div>';
    $html .= '<p class="sr-note">Previous ' . $days . ' days: ' . $previous
           . ' searches. ' . ($trend === null ? 'Variation not available (no previous data).' : 'Change: ' . $trend . '%.') . '</p>';
    $html .= '<p><a href="' . $esc($base . '?action=export&days=' . $days . '&q=' . rawurlencode($filter)) . '">Export CSV (maximum 1,000 expressions)</a></p>';
    $html .= '<div class="sr-table-wrap"><table class="sr-report-table"><thead><tr><th scope="col">Expression</th><th scope="col">Hits</th></tr></thead><tbody>';
    foreach ($rows as $row) {
        $html .= '<tr><td>' . $esc($row['query']) . '</td><td>' . $row['hits'] . '</td></tr>';
    }
    if (!$rows) { $html .= '<tr><td colspan="2">No searches found for this period.</td></tr>'; }
    $html .= '</tbody></table></div><h3>Daily activity</h3>';
    $html .= '<div class="sr-table-wrap"><table class="sr-report-table"><thead><tr><th scope="col">Date</th><th scope="col">Hits</th></tr></thead><tbody>';
    foreach ($daily as $row) {
        $html .= '<tr><td>' . $esc($row['date']) . '</td><td>' . $row['hits'] . '</td></tr>';
    }
    $html .= '</tbody></table></div>';
    $html .= '<p class="sr-note">Historical information is limited by SearchRank retention. Counts are internal searches, not Google/Bing impressions. Zero-result searches are not measured yet.</p></div>';
    return $html;
}
