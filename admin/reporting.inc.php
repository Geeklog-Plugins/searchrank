<?php
/**
 * SearchRank read-only reporting tools. Compatible with PHP 5.6.
 * Daily totals are derived from existing legacy tables: no destructive migration.
 */
if (stripos($_SERVER['PHP_SELF'], basename(__FILE__)) !== false) {
    die('Direct access denied');
}

function SEARCHRANK_reportPeriods()
{
    global $_SR_CONF;
    $ttl = isset($_SR_CONF['ttl']) ? max(1, (int) $_SR_CONF['ttl']) : 28;
    $periods = array();
    foreach (array(7, 28, 90, 365) as $period) {
        if ($period <= $ttl) { $periods[] = $period; }
    }
    if (!$periods) { $periods[] = min($ttl, 7); }
    return $periods;
}

function SEARCHRANK_reportDays()
{
    $periods = SEARCHRANK_reportPeriods();
    $requested = isset($_GET['days']) ? (int) $_GET['days'] : 28;
    return in_array($requested, $periods, true) ? $requested : $periods[count($periods) - 1];
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

/**
 * Accessible, dependency-free SVG chart for daily search totals.
 * Full values remain available in a collapsible HTML data table.
 *
 * @param array $daily Chronological rows containing date and hits
 * @param array $labels Translated chart labels
 * @return string Escaped SVG with underlying accessible data
 */
function SEARCHRANK_reportDailyChart($daily, $labels)
{
    $escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
    // SVG attributes require a dot decimal separator regardless of setlocale().
    $coord = function ($value) { return number_format((float) $value, 2, '.', ''); };
    $count = count($daily);
    if (!$count) { return '<p class="sr-note">' . $escape($labels['empty']) . '</p>'; }
    $max = 0;
    foreach ($daily as $row) { $max = max($max, max(0, (int) $row['hits'])); }
    if (!$max) { return '<p class="sr-note">' . $escape($labels['empty']) . '</p>'; }

    // All SVG geometry uses explicit presentation attributes so Geeklog theme
    // rules for generic path, line, circle or text elements cannot break it.
    $width = 900; $height = 280; $left = 56; $right = 24; $top = 18; $bottom = 50;
    $plotWidth = $width - $left - $right;
    $plotHeight = $height - $top - $bottom;
    $step = max(1, (int) ceil($max / 4));
    $axisMax = $step * 4;
    $points = array(); $markers = '';
    foreach ($daily as $i => $row) {
        $hits = max(0, (int) $row['hits']);
        $x = $coord($left + ($count > 1 ? $i * $plotWidth / ($count - 1) : $plotWidth / 2));
        $y = $coord($top + $plotHeight * (1 - $hits / $axisMax));
        $points[] = ($i === 0 ? 'M ' : 'L ') . $x . ' ' . $y;
        if ($count <= 31) {
            $markers .= '<circle cx="' . $x . '" cy="' . $y . '" r="3" fill="#27684e" stroke="#fff" stroke-width="1.5">'
                . '<title>' . $escape($row['date'] . ': ' . $hits . ' ' . $labels['hits']) . '</title></circle>';
        }
    }
    $svg = '<svg class="sr-daily-chart" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 280" '
         . 'width="900" height="280" preserveAspectRatio="xMidYMid meet" role="img" '
         . 'aria-label="' . $escape($labels['description']) . '">';
    for ($tick = 0; $tick <= 4; ++$tick) {
        $y = $coord($top + $plotHeight * (1 - $tick / 4));
        $svg .= '<line x1="' . $left . '" x2="' . ($width - $right) . '" y1="' . $y
              . '" y2="' . $y . '" stroke="#cbd5d1" stroke-width="1" stroke-dasharray="3 4"/>'
              . '<text x="' . ($left - 10) . '" y="' . $coord((float) $y + 4)
                      . '" text-anchor="end" fill="#51635c" font-size="12">' . ($step * $tick) . '</text>';
    }
    // Use short dates and anchor the end labels inside the chart viewport.
    $tickIndexes = array_unique(array(0, (int) floor(($count - 1) / 2), $count - 1));
    foreach ($tickIndexes as $idx) {
        $x = $coord($left + ($count > 1 ? $idx * $plotWidth / ($count - 1) : $plotWidth / 2));
        $anchor = $idx === 0 ? 'start' : ($idx === $count - 1 ? 'end' : 'middle');
        $shortDate = date('d/m', strtotime($daily[$idx]['date']));
        $svg .= '<text x="' . $x . '" y="' . ($height - 17) . '" text-anchor="' . $anchor
              . '" fill="#51635c" font-size="12">' . $escape($shortDate) . '</text>';
    }
    $svg .= '<path d="' . implode(' ', $points) . '" fill="none" stroke="#27684e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>'
          . $markers . '</svg>';
    $html = '<figure class="sr-chart-figure">' . $svg . '<figcaption class="sr-note">'
          . $escape($labels['description']) . '</figcaption></figure>';
    $html .= '<details class="sr-chart-data"><summary>' . $escape($labels['show_data']) . '</summary>'
          . '<div class="sr-table-wrap"><table class="sr-report-table"><thead><tr><th scope="col">'
          . $escape($labels['date']) . '</th><th scope="col">' . $escape($labels['hits'])
          . '</th></tr></thead><tbody>';
    foreach (array_reverse($daily) as $row) {
        $url = $labels['base_url'] . '?action=day&date=' . rawurlencode($row['date']);
        $html .= '<tr><td><a href="' . $escape($url) . '">' . $escape($row['date'])
              . '</a></td><td>' . (int) $row['hits'] . '</td></tr>';
    }
    return $html . '</tbody></table></div></details>';
}

function SEARCHRANK_reportDayPage($date)
{
    global $_TABLES, $_CONF, $LANG_SEARCHRANK_1;
    $esc = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
    $base = $_CONF['site_admin_url'] . '/plugins/searchrank/index.php';
    $back = '<p><a href="' . $esc($base . '?action=report') . '">&larr; '
          . $esc(isset($LANG_SEARCHRANK_1['analytics']) ? $LANG_SEARCHRANK_1['analytics'] : 'Analytics & CSV') . '</a></p>';
    if (!is_string($date) || !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/D', $date)) {
        return $back . '<p>Invalid date.</p>';
    }
    $start = strtotime($date . ' 00:00:00');
    if ($start === false || date('Y-m-d', $start) !== $date || $start > strtotime('today')) {
        return $back . '<p>Invalid date.</p>';
    }
    $end = strtotime('+1 day', $start);
    $sql = "SELECT q.query, SUM(h.hits) AS hits FROM {$_TABLES['searchrank_queries']} q "
         . "INNER JOIN {$_TABLES['searchrank_hits']} h ON q.hid=h.hid "
         . "WHERE h.date >= {$start} AND h.date < {$end} "
         . "GROUP BY q.query ORDER BY hits DESC, q.query ASC LIMIT 1000";
    $result = DB_query($sql);
    $html = '<section class="sr-admin-home">' . $back . '<h2>' . $esc($date) . '</h2>';
    $html .= '<table class="sr-report-table"><thead><tr><th scope="col">'
          . $esc(isset($LANG_SEARCHRANK_1['search_expression']) ? $LANG_SEARCHRANK_1['search_expression'] : 'Search expression')
          . '</th><th scope="col">' . $esc($LANG_SEARCHRANK_1['hits']) . '</th></tr></thead><tbody>';
    $found = false;
    while ($row = DB_fetchArray($result)) {
        $found = true;
        $html .= '<tr><td>' . $esc($row['query']) . '</td><td>' . (int) $row['hits'] . '</td></tr>';
    }
    if (!$found) { $html .= '<tr><td colspan="2">No searches recorded for this day.</td></tr>'; }
    return $html . '</tbody></table><p class="sr-note">Up to 1,000 expressions, ranked by occurrences.</p></section>';
}

function SEARCHRANK_reportExport($days, $filter)
{
    global $_CONF;
    $rows = SEARCHRANK_reportRows($days, $filter, 1000);
    $host = parse_url($_CONF['site_url'], PHP_URL_HOST);
    $host = strtolower(preg_replace('/[^a-zA-Z0-9.-]/', '-', (string) $host));
    $host = trim($host, '.-');
    if ($host === '') { $host = 'site'; }
    $filename = 'searchrank-report-' . $host . '-' . date('Ymd-His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
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
    // Offset zero must end at tomorrow (include all of today), not today.
    $endExclusive = $offsetDays === 0 ? strtotime('tomorrow') : SEARCHRANK_reportStart($offsetDays - 1);
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
        'export_link' => 'Export CSV (maximum 1,000 expressions)',
        'daily_activity' => 'Daily activity',
        'date' => 'Date',
        'current' => 'Current',
        'previous' => 'Previous',
        'change' => 'Change'
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
    foreach (SEARCHRANK_reportPeriods() as $d) {
        $html .= '<option value="' . $d . '"' . ($d === $days ? ' selected' : '') . '>' . $d . ' ' . $esc($LANG_SEARCHRANK_1['days']) . '</option>';
    }
    $html .= '</select></label><label>' . $esc($LANG_SEARCHRANK_1['search_expression']) . ' <input name="q" type="search" value="' . $esc($filter) . '"></label>'
           . '<button class="sr-action" type="submit">' . $esc($LANG_SEARCHRANK_1['filter']) . '</button></form>';
    global $_SR_CONF;
    $retention = isset($_SR_CONF['ttl']) ? max(1, (int) $_SR_CONF['ttl']) : 28;
    $canCompare = $retention >= 2 * $days;
    $previous = $canCompare ? SEARCHRANK_reportTotals($days, $days) : 0;
    $current = SEARCHRANK_reportTotals($days, 0);
    $trend = $previous > 0 ? round((($current - $previous) * 100) / $previous, 1) : null;
    $html .= '<div class="sr-summary">' . $esc(sprintf($LANG_SEARCHRANK_1['total_summary'], $total, $days, count($rows))) . '</div>';
    if ($canCompare) {
    $html .= '<p class="sr-note">' . $esc(sprintf($LANG_SEARCHRANK_1['previous_summary'], $days, $previous))
           . ' ' . $esc($trend === null ? $LANG_SEARCHRANK_1['no_variation'] : sprintf($LANG_SEARCHRANK_1['variation'], $trend)) . '</p>';
    } else {
        $html .= '<p class="sr-note">' . $esc(isset($LANG_SEARCHRANK_1['comparison_unavailable']) ? $LANG_SEARCHRANK_1['comparison_unavailable'] : 'Comparison unavailable: retention is shorter than two complete periods.') . '</p>';
    }
    // Only the dashboard contains the daily chart. Detailed reports stay
    // focused on filters, period comparisons, ranked expressions and export.
    $html .= '<div class="sr-report-actions"><a class="sr-action" href="'
           . $esc($base . '?action=export&days=' . $days . '&q=' . rawurlencode($filter)) . '">'
           . $esc($LANG_SEARCHRANK_1['export_link']) . '</a><a class="sr-back-link" href="'
           . $esc($base . '?days=' . $days) . '">'
           . $esc(isset($LANG_SEARCHRANK_1['dashboard']) ? $LANG_SEARCHRANK_1['dashboard'] : 'Dashboard')
           . '</a></div>';
    $trends = $canCompare ? SEARCHRANK_reportTrends($days, 20) : array();
    if ($canCompare) {
    $html .= '<h3>' . $esc($LANG_SEARCHRANK_1['trends_title']) . '</h3>';
    $html .= '<p class="sr-note">' . $esc($LANG_SEARCHRANK_1['trends_note']) . '</p>';
    $html .= '<div class="sr-table-wrap"><table class="sr-report-table"><thead><tr><th scope="col">Expression</th><th scope="col">' . $esc($LANG_SEARCHRANK_1['current']) . '</th><th scope="col">' . $esc($LANG_SEARCHRANK_1['previous']) . '</th><th scope="col">' . $esc($LANG_SEARCHRANK_1['change']) . '</th></tr></thead><tbody>';
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
    }
    $html .= '<h3>' . $esc(isset($LANG_SEARCHRANK_1['ranked_queries']) ? $LANG_SEARCHRANK_1['ranked_queries'] : 'Most searched expressions') . '</h3>';
    $html .= '<div class="sr-table-wrap"><table class="sr-report-table"><thead><tr><th scope="col">Expression</th><th scope="col">' . $esc($LANG_SEARCHRANK_1['hits']) . '</th></tr></thead><tbody>';
    foreach ($rows as $row) {
        $html .= '<tr><td>' . $esc($row['query']) . '</td><td>' . $row['hits'] . '</td></tr>';
    }
    if (!$rows) { $html .= '<tr><td colspan="2">' . $esc($LANG_SEARCHRANK_1['no_searches']) . '</td></tr>'; }
    $html .= '</tbody></table></div>';
    $html .= '<p class="sr-note">' . $esc(sprintf(isset($LANG_SEARCHRANK_1['retention_period_note']) ? $LANG_SEARCHRANK_1['retention_period_note'] : 'Retention: %d days. Older data may be unavailable.', $retention)) . '</p>';
    $html .= '<p class="sr-note">' . $esc($LANG_SEARCHRANK_1['retention_note']) . '</p></div>';
    return $html;
}


/**
 * Compact landing dashboard. Reuses read-only reporting queries and leaves
 * detailed filters / CSV exports on the separate reporting page.
 */
function SEARCHRANK_dashboardPage()
{
    global $_CONF, $_SR_CONF, $LANG_SEARCHRANK_1;
    SEARCHRANK_housekeeping();
    $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
    $base = $_CONF['site_admin_url'] . '/plugins/searchrank/index.php';
    $days = SEARCHRANK_reportDays();
    $daily = SEARCHRANK_reportDaily($days);
    $total = 0;
    foreach ($daily as $row) { $total += (int) $row['hits']; }
    $top = SEARCHRANK_reportRows($days, '', 10);
    // The top-ten list is limited; this query counts all distinct expressions.
    $distinct = SEARCHRANK_reportDistinct($days);
    $ttl = isset($_SR_CONF['ttl']) ? max(1, (int) $_SR_CONF['ttl']) : 28;
    $canCompare = $ttl >= $days * 2;
    $previous = $canCompare ? SEARCHRANK_reportTotals($days, $days) : 0;
    $previousLabel = $canCompare && $previous > 0 ? (string) $previous : '—';
    $locale = function ($key, $fallback) use ($LANG_SEARCHRANK_1) {
        return isset($LANG_SEARCHRANK_1[$key]) ? $LANG_SEARCHRANK_1[$key] : $fallback;
    };
    $html = '<section class="sr-admin-home sr-dashboard"><h2>' . $esc($locale('dashboard', 'Dashboard')) . '</h2>';
    $html .= '<form class="sr-filter" method="get" action="' . $esc($base) . '"><label>'
          . $esc($locale('period', 'Period')) . ' <select name="days">';
    foreach (SEARCHRANK_reportPeriods() as $period) {
        $html .= '<option value="' . $period . '"' . ($period === $days ? ' selected' : '') . '>'
              . $period . ' ' . $esc($locale('days', 'days')) . '</option>';
    }
    $html .= '</select></label><button class="sr-action" type="submit">'
          . $esc($locale('filter', 'Filter')) . '</button></form>';
    $cards = array(
        array($locale('dashboard_searches', 'Searches in selected period'), $total),
        array($locale('dashboard_distinct', 'Distinct search expressions'), $distinct),
        array($locale('dashboard_previous', 'Searches in previous period'), $previousLabel)
    );
    $html .= '<div class="sr-kpi-grid">';
    foreach ($cards as $card) {
        $html .= '<div class="sr-kpi"><span class="sr-kpi-label">' . $esc($card[0])
              . '</span><strong>' . $esc($card[1]) . '</strong></div>';
    }
    $html .= '</div>';
    if (!$canCompare || !$previous) {
        $html .= '<p class="sr-note">' . $esc($locale('no_variation', 'Previous-period comparison unavailable.')) . '</p>';
    }
    $html .= '<h3>' . $esc($locale('daily_activity', 'Daily activity')) . '</h3>';
    $html .= SEARCHRANK_reportDailyChart($daily, array(
        'description' => $locale('chart_description', 'Searches per day'),
        'show_data' => $locale('chart_show_data', 'View daily values'),
        'empty' => $locale('no_searches', 'No searches found for this period.'),
        'date' => $locale('date', 'Date'),
        'hits' => $locale('hits', 'Hits'),
        'base_url' => $base
    ));
    $html .= '<div class="sr-dashboard-footer"><h3>' . $esc($locale('dashboard_top', 'Top search expressions')) . '</h3>';
    if ($top) {
        $html .= '<div class="sr-table-wrap"><table class="sr-report-table"><thead><tr><th scope="col">'
              . $esc($locale('search_expression', 'Search expression')) . '</th><th scope="col">'
              . $esc($locale('hits', 'Hits')) . '</th></tr></thead><tbody>';
        foreach ($top as $row) {
            $html .= '<tr><td>' . $esc($row['query']) . '</td><td>' . (int) $row['hits'] . '</td></tr>';
        }
        $html .= '</tbody></table></div>';
    } else {
        $html .= '<p>' . $esc($locale('no_searches', 'No searches found for this period.')) . '</p>';
    }
    $html .= '<p><a class="sr-action" href="' . $esc($base . '?action=report&days=' . $days) . '">'
          . $esc($locale('analytics', 'Analytics & CSV')) . '</a></p></div></section>';
    return $html;
}

function SEARCHRANK_reportDistinct($days)
{
    global $_TABLES;
    $start = SEARCHRANK_reportStart(max(1, (int) $days) - 1);
    $end = strtotime('tomorrow');
    $sql = "SELECT COUNT(DISTINCT q.query) AS total FROM {$_TABLES['searchrank_queries']} q "
         . "INNER JOIN {$_TABLES['searchrank_hits']} h ON q.hid=h.hid "
         . "WHERE h.date >= {$start} AND h.date < {$end}";
    $result = DB_query($sql);
    $row = DB_fetchArray($result);
    return $row && isset($row['total']) ? (int) $row['total'] : 0;
}
