# SearchRank 1.3.0

**Release candidate · Geeklog search analytics plugin**

SearchRank records internal site-search phrases and provides search rankings, daily activity charts, and administrative reports.

> [!IMPORTANT]
> **Release candidate:** SearchRank 1.3.0 is still under development. Automated checks do not replace functional testing, particularly for legacy upgrades and database maintenance.

## Requirements

| Component | Requirement |
| --- | --- |
| Geeklog | **2.1.3+** (declared minimum; regression testing pending) |
| PHP | **5.6.40+** (CI syntax checks: PHP 5.6, 8.1, and 8.3) |
| Database | **MySQL-compatible** Geeklog database |
| PostgreSQL | **Not supported** |

## Installation and upgrades

Download the installable archive from [`dist/searchrank_1.3.0_2.1.3.zip`](dist/searchrank_1.3.0_2.1.3.zip). It contains a top-level `searchrank/` directory.

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
| **Dashboard** | Overview and search rankings |
| **Search Log** | Recorded phrases, occurrence counts, and pagination |
| **Analytics & CSV** | Retention-aware periods, daily SVG chart, day-by-day detail links, trends, rankings, and CSV export |
| **Help** | Configuration, privacy, and troubleshooting guidance |

### Analytics and exports

- Choose a reporting period of **7, 28, 90, or 365 days**, subject to the configured retention limit.
- View searches by day on a responsive SVG chart.
- Open day links to inspect recorded expressions and occurrence counts.
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
