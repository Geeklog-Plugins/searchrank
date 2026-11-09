# SearchRank 1.4.0

**Release candidate · Geeklog search analytics plugin**

SearchRank records internal site-search phrases and provides search rankings, daily activity charts, and administrative reports.

> [!IMPORTANT]
> **Release candidate:** SearchRank 1.4.0 is under development. Automated checks do not replace functional testing, particularly for legacy upgrades and database maintenance.

## Requirements

| Component | Requirement |
| --- | --- |
| Geeklog | **2.1.3+** (declared minimum; regression testing pending) |
| PHP | **5.6.40+** (CI syntax checks: PHP 5.6, 8.1, and 8.3) |
| Database | **MySQL-compatible** Geeklog database |
| PostgreSQL | **Not supported** |

## Installation and upgrades

Download the installable archive from [`dist/searchrank_1.4.0_2.1.3.zip`](dist/searchrank_1.4.0_2.1.3.zip). It contains a top-level `searchrank/` directory.

1. **Back up** your Geeklog database, SearchRank tables, and existing plugin files.
2. Install or upgrade using Geeklog's plugin administration tools.
3. Check the plugin configuration, especially the retention period (`ttl`).
4. Confirm that the Search Log, dashboard, and Analytics pages display correctly.

**Do not uninstall the existing plugin just to upgrade.** Uninstalling may remove the stored search history.

### Upgrading from SearchRank 1.0.0

The upgrade code includes the following legacy path:

```text
1.0.0 → 1.1.0 → 1.1.1 → 1.2.1 → 1.2.2 → 1.2.3 → 1.3.0
```

Back up the SearchRank tables before updating. **This legacy migration path has not yet been regression-tested**; test it on a staging installation before updating production sites.

## Administration

Open **Geeklog Admin → SearchRank**.

| Section | Features |
| --- | --- |
| **Dashboard** | Period selector, KPI summary, daily chart, and top search phrases |
| **Search Log** | Recorded phrases, occurrence counts, and pagination |
| **Analytics & CSV** | Filtered rankings paginated at 25 expressions per page, collapsible period comparisons, and CSV export; the daily chart appears only on the dashboard |
| **Help** | Configuration, privacy, and troubleshooting guidance |

### Analytics and exports

- Choose a reporting period of **7, 28, 90, or 365 days**, subject to the configured retention limit.
- View searches by day on the dashboard's responsive SVG chart (not repeated in detailed reports).
- Open day links from the dashboard chart's accessible data table to inspect recorded expressions and occurrence counts.
- Compare expressions with the preceding period when sufficient retention is configured.
- Export a CSV whose filename includes the current site's hostname and a timestamp.

The statistics measure **internal site searches**, not Google or Bing impressions. Historical records already deleted by retention cannot be recovered.

## Data retention

- **New installations default to 365 days** of retention.
- **Existing installations keep their configured retention period** when upgraded.
- Expired daily records are deleted using local calendar-day boundaries.
- Expressions with remaining recent records are retained.
- The Geeklog scheduled-task hook performs maintenance according to Geeklog's own scheduling mechanism. Search and selected administration actions also trigger cleanup as a fallback.

> [!NOTE]
> The Geeklog scheduled-task mechanism does not guarantee wall-clock execution on a completely inactive website. Also, a configured retention period does not mean that a newly installed site already has that much historical data.

## Privacy and limitations

- Search phrases can contain personal information. SearchRank filters some obvious email addresses and payment-card-like patterns, but **this is not comprehensive redaction**.
- Review the visibility of the public ranking block and configure a suitable retention period.
- Searches returning zero results are **not tracked separately**.
- Additional language translations may be delivered in later updates.

## Release and testing status

**Verified so far**

- PHP syntax checks on PHP 5.6, 8.1, and 8.3.
- Automated packaging checks for the installable ZIP.
- Fresh installation and administration interface reviewed on Geeklog 2.2.2.

**Still requiring validation before an official release**

- Upgrade paths from legacy installations, particularly SearchRank 1.0.0.
- Retention cleanup and concurrent search writes against live MySQL installations.
- Runtime compatibility with the declared minimum Geeklog 2.1.3.

## Documentation

See the [official SearchRank repository](https://github.com/Geeklog-Plugins/searchrank), its [`docs/`](https://github.com/Geeklog-Plugins/searchrank/tree/master/docs) directory and [`ROADMAP.md`](https://github.com/Geeklog-Plugins/searchrank/blob/master/ROADMAP.md) for additional technical details and outstanding checks.

## 1.4.0 dashboard acceptance

The dashboard uses the same read-only data as detailed reporting. Clear-log maintenance is available on Help, not on the landing page. Verify 7/28/90/365-day plots (subject to configured retention), empty periods, integer y-axis ticks, short x-axis dates, narrow screens and CSS compatibility with Geeklog themes. Compare daily totals with the collapsible data table and ensure CSV/export, pagination, existing search history and CSRF-protected clear-log continue to work. No live database or theme acceptance is claimed by automated syntax checks.

### Report pagination (1.4.0)

Detailed reports show 25 ranked expressions per page with previous/next links anchored to the ranking. Page changes retain the search expression filter and selected period. The matching-expression count describes the full filtered result; export still independently returns up to 1,000 expressions, regardless of the displayed page. The preceding-period trends are collapsed by default to reduce scrolling.

## Local interoperability services (1.4.0)

Follows [Geeklog Plugin Capability Contract](https://github.com/hostellerie/memorandum/blob/main/plugin-capability-contract.md) and the [SEO and Measurement Interoperability Guide](https://github.com/hostellerie/memorandum/blob/main/seo-measurement-interoperability-guide.md).

`plugin_getcapabilities_searchrank()` declares `dashboard.summary` and `searchrank.query.metrics`. Local Geeklog clients can feature-detect `PLG_invokeService()` and request:

```php
$output = array();
$messages = array();
$status = PLG_invokeService('searchrank', 'query_metrics',
    array('days' => 28), $output, $messages);
// Or: 'dashboard_summary' for metrics, alerts and administration links.
```

Both services are **read-only, administrator-only**, limited to periods of 1–365 days within the configured retention, and return aggregate counts only. No individual search terms, personal data or third-party SQL schema is exposed. The dispatcher requires the active Geeklog plugin; the callbacks reject web-service requests (`gl_svc`) and unauthorized users. A returned null for zero-result searches means **not measured**, not zero. These services do not measure Google/Bing queries and are not associated with a content URL.

### Privacy behavior

New internal searches containing recognizable emails, payment-card-like sequences, password/token parameters, bearer credentials or common token prefixes are not logged. Existing history is not silently rewritten, but the public Most Searched block excludes detected sensitive expressions. This is conservative heuristic filtering, **not comprehensive PII detection**: it cannot guarantee removal of all personal data. Site administrators should review stored history, retention and public block visibility; avoid publishing historical exports. No new configuration field or schema migration is required. To run isolated checks: `php tests/privacy-smoke.php`.

### Outstanding runtime acceptance

Validate services using `PLG_invokeService` with Geeklog 2.1.3/2.2.2 enabled/disabled states, administrator/non-admin users, and multisite installs. Confirm legacy history protection and concurrency/retention behavior with a staging database. Automated checks do not substitute for this acceptance.

### Live upgrade feedback (2026-11-09)

A site administrator reports a successful SearchRank 1.3.0 → 1.4.0 upgrade with existing search data preserved. This confirms one real-world upgrade path, not a complete regression suite for every supported Geeklog/PHP/database combination. Detailed reports now display separated export and dashboard actions with responsive styling and a French localized pagination summary.

### Stylesheet cache versioning (1.4.0)

Both `admin/css/admin.css` and `admin/css/block.css` use deterministic `?v=1.4.0-<filemtime>` URLs. The plugin currently does not load its own JavaScript asset. Report-page actions include only CSV export; the dashboard remains available in the primary navigation, avoiding duplicated links. Verify CSS refresh on a deployed install without a forced browser refresh.
